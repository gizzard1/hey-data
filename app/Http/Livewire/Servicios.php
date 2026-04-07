<?php

namespace App\Http\Livewire;

use App\Exports\reporteServicios;
use App\Models\categoria_servicio;
use App\Models\excepcion_servicio;
use App\Models\File;
use App\Models\servicio;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Servicios extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $isService = 1;
    public $records, $search, $action = 1, $serviceSelected, $categoriesList, $percent = 0, $finalD = 0, $editing = false;
    public $gallery = [], $pictures = [], $categorias, $marcas = [];
    protected $paginationTheme = 'bootstrap';

    public servicio $service;
    public $rewardType = 0, $rewardQty;
    public $selectedItems = [], $cat;
    private $servicios;
    public $orderByMostOrLessSelled = null;
    public $orderBy = 'name', $direction = 'asc';
    protected $rules =    [
        'service.name' => [
            'required',
            'min:3',
            'max:80',
            'regex:/^[^\'"]+$/'
        ],
        'service.description' => "nullable|max:200",
        'service.gross_price' => "required|min:0|numeric",
        'service.iva' => "required",
        'service.disccount_price' => "nullable|min:0|numeric",
        'service.reward_points' => "nullable",
        'service.duration' => "required|min:0|numeric",
        'service.brand_id' => "nullable",
    ];
    protected $messages = [
        'service.name.regex' => 'Evita usar comillas',
        'service.name.min' => 'Usa al menos 3 carácteres',
    ];
    public $mergeItems = [];
    private function loadDefault()
    {
        $this->service = new servicio();
        $this->marcas = Auth::user()->salon->marcas->where('name', '!=', 'Marca eliminada');
        $this->service->iva = 0.16;
        $this->action = 1;
        $this->service->duration = '60';
        $this->service->brand_id = null;
        $this->percent = 0;
        $this->serviceSelected = null;
        $this->categoriesList = null;
        $this->categorias = Auth::user()->salon->categoriaServicios;
        $this->rewardType = 0;
        $this->rewardQty = null;
        $this->gallery = [];
        $this->pictures = [];
    }
    public function recalculateReward()
    {
        try {
            if (session()->has('customDate')) {
                Carbon::setTestNow(Carbon::createFromFormat('Y-m-d', session('customDate')));
            }

            $this->rewardQty = $this->eliminarCaracteres($this->rewardQty);

            $comision = Auth::user()->salon->recompensaGeneral;

            foreach ($this->selectedItems as $item) {
                $excepcion = new excepcion_servicio();
                $excepcion->servicio_id = $item;
                $excepcion->qty = $this->rewardQty;

                if (!$this->rewardType) {
                    $excepcion->type_comission = 'percent';
                } else {
                    $excepcion->type_comission = 'qty';
                }


                if ($comision) {
                    $excepcion->programa_recompensa_id = $comision->id;
                } else {
                    $comision->salon_id = Auth::user()->salon_id;
                    $comision->save();
                    $excepcion->programa_recompensa_id = $comision->id;
                }
                $excepcion->save();
            }
            $this->dispatchBrowserEvent('noty', ['msg' => 'SOLICITUD PROCESADA CON ÉXITO']);
            $this->dispatchBrowserEvent('hideModalRewardForm');
            $this->loadDefault();

            if (session()->has('customDate')) {
                Carbon::setTestNow();
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 52312Agenda"]);
        }
    }

    private function eliminarCaracteres($data)
    {
        try {
            // Elimina todos los caracteres que no sean números, puntos o comas
            $valorSinCaracter = preg_replace('/[^0-9.]/', '', $data);

            // Convierte el resultado a un float
            $valorNumerico = (float) $valorSinCaracter;

            // Verifica si el resultado es numérico
            if (!is_numeric($valorNumerico)) {
                $this->dispatchBrowserEvent('noty-error', ['msg' => "Corrija el valor numérico"]);
                return;
            } else {
                return $valorNumerico;
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 51312Agenda"]);
        }
    }

    public function mount($search = null)
    {
        try {
            if (!$this->validateSuscription()) {
                return redirect()->route('suscripcion');
            }
            $this->search = $search;
            // if (session()->has('cartMaterials')) {
            //     $this->cartP = session('cartMaterials');
            // } else {
            //     $this->cartP = new Collection;
            // }
            $this->loadDefault();
            $this->cat = null;
            $this->servicios = $this->loadServices();
            $this->calculateFinalDS();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 35241Servicios"]);
        }
    }
    private function validateSuscription()
    {
        $rights = true;

        $suscription = Auth::user()->salon->suscription;

        if ($suscription == 'free') {
            $hoy = Carbon::now();
            $salon = Auth::user()->salon;
            $lastest_suscription = $salon->suscripciones()->latest()->first();
            if ($lastest_suscription) {
                if ($hoy->diffInDays($lastest_suscription) > 7) {
                    $rights = false;
                }
            } else {
                if ($hoy->diffInDays($salon->created_at) > 7) {
                    $rights = false;
                }
            }

            $rights = false;
        }

        return $rights;
    }

    public function toggleItem($itemId)
    {
        if (in_array($itemId, $this->selectedItems)) {
            $this->selectedItems = array_diff($this->selectedItems, [$itemId]);
        } else {
            $this->selectedItems[] = $itemId;
        }

        $this->selectedItems = array_values($this->selectedItems); // Reindexar el array

    }
    public function toggleSelectAll($isSelected)
    {
        if ($isSelected) {
            $items = $this->loadServices(0);
            $this->selectedItems = $items->pluck('id')->toArray();
        } else {
            $this->selectedItems = [];
        }
    }

    protected $listeners = [
        'refresh' => '$refresh',
        // 'addProduct' => 'addProductFromCard',
        'search' => 'searching',
        'searchSKU',
        'eliminar' => 'Delete',
        'calculateFinalDS',
        'calculate',
        'removeItem',
        'updateQty',
        'help',
        'categoriaAgregada'
    ];

    public function searchSKU($searchText)
    {
        $this->search = trim($searchText);
    }

    // function loadProducts()
    // {
    //     try{
    //         $this->resetPage();
    //         if (!empty($this->search)) {

    //             $product = producto::where('salon_id',Auth::user()->salon->id)
    //             ->where(function ($query) {
    //                     $query->where('sku', '=', $this->search)
    //                         ->orWhere('intern_sku', '=', $this->search);
    //                 })->where('salon_id',Auth::user()->salon->id)->first();
    //         }else{
    //             $product='';
    //         }
    //         // Verifica si se encontró un producto
    //         if ($product) {
    //             // Llama a la función para agregar el producto al carrito
    //             $this->addProductFromCard($product);   
    //         }

    //         // Restablece el valor de búsqueda después de agregar el producto
    //         $this->search = '';
    //     }catch(\Throwable $th){
    //         $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 64242Servicios"] );
    //     }
    // }
    // function addProductFromCard(producto $product)
    // {
    //     $this->AddProduct($product);
    // }
    public function render()
    {
        try {
            //validamos que exista la sesion
            // if (session()->has('cartMaterials')) {
            //     //obtenemos el carrito
            //     $cart = session('cartMaterials');
            //     // ordenar los items del carrito mediante nombre de forma asc
            //     $cartInfo = $cart->sortBy(['name', ['name', 'asc']]);
            // } else {
            //     $cartInfo = new Collection;
            // }
            return view('livewire.servicios.main', [
                'servicios' => $this->loadServices(),
                'itemMerged' => $this->service,
                // 'productos' => $this->loadProducts(),
                // 'cartInfo'=>$cartInfo,
            ]);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 93243Servicios"]);
        }
    }
    private function loadCategorias()
    {
        return Auth::user()->salon->categoriaServicios;
    }
    public function joinGroup($categoryId)
    {
        try {
            foreach ($this->selectedItems as $servicio) {
                $service = servicio::with('categorias')->find($servicio);
                $service->categorias()->syncWithoutDetaching([$categoryId]);
            }
            $this->emit('refresh');
            $this->dispatchBrowserEvent('noty', ['msg' => 'SOLICITUD PROCESADA CON ÉXITO']);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 93Servicios"]);
        }
    }

    public function archiveItem($status = 'hide')
    {
        try {
            foreach ($this->selectedItems as $servicio) {
                $item = servicio::find($servicio);
                $item->visibility = $status;
                $item->save();
            }
            $this->resetPage();
            $this->selectedItems = [];
            $this->dispatchBrowserEvent("noty", ["msg" => "SOLICITUD PROCESADA CON ÉXITO"]);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent("noty-error", ['msg' => 'Código de error: 1223Servicios']);
        }
    }
    public function categoriaAgregada()
    {
        $this->categorias = Auth::user()->salon->categoriaServicios;
    }
    public function updatedPercent()
    {
        $this->calculateFinalDS();
    }
    public function filtrarCategoria($categoriaId = null)
    {
        $this->cat = $categoriaId;
        $this->orderByMostOrLessSelled = null;
        $this->search = null;
        $this->selectedItems = [];
        $this->resetPage();
    }
    public function orderByMostOrLessSelled($type)
    {
        $this->orderByMostOrLessSelled = $type;
        $this->selectedItems = [];
        $this->resetPage();
    }
    public function loadServices($wP = 1)
    {
        try {
            $visibility = $this->orderByMostOrLessSelled === 'archives' ? 'hide' : 'visible';
            $query = servicio::with('marca', 'categorias', 'asignaciones')
                ->where('servicios.visibility', $visibility)
                ->where('servicios.salon_id', Auth::user()->salon->id)
                ->where('servicios.name', '!=', 'Servicio eliminado');

            // Si hay una categoría, filtrarla usando whereHas
            if ($this->cat != null) {
                $cat = $this->cat;
                $query->whereHas('categorias', function ($q) use ($cat) {
                    $q->where('categoria_servicios.id', $cat);
                });
            }

            // Si hay búsqueda, agregar las condiciones
            if (!empty($this->search)) {
                $query = $this->searchService($query, $this->search);
            }

            // Ordenar por más o menos vendidos
            if ($this->orderByMostOrLessSelled && $this->orderByMostOrLessSelled !== 'archives') {

                if ($this->orderByMostOrLessSelled == 'noSales') {
                    $query = $query->withCount([
                        'asignaciones as asignaciones_sum_quantity' => function ($query) {
                            $query->selectRaw('COUNT(DISTINCT CONCAT(cita_id, "-", customer_id))')
                                ->join('citas', 'asignacion_servicios.cita_id', '=', 'citas.id');
                        }
                    ])->havingRaw('COALESCE(asignaciones_sum_quantity, 0) = 0');
                } else {
                    $query = $query->selectRaw("servicios.*, 
                                        (SELECT COUNT(*) 
                                        FROM (SELECT DISTINCT asignacion_servicios.cita_id, asignacion_servicios.selected_service 
                                            FROM asignacion_servicios 
                                            JOIN citas ON asignacion_servicios.cita_id = citas.id 
                                            WHERE citas.status IN ('Pagada', 'Pendiente') 
                                            AND asignacion_servicios.selected_service = servicios.id) AS ventas_unicas) 
                                        AS asignaciones_sum_quantity")
                        ->having('asignaciones_sum_quantity', '>', 0)
                        ->orderBy('asignaciones_sum_quantity', $this->orderByMostOrLessSelled);
                }
            } else {
                if ($this->orderBy == "marca.name") {
                    $query = $query->join('marcas', 'servicios.brand_id', '=', 'marcas.id')
                        ->select('servicios.*')
                        ->orderBy('marcas.name', $this->direction);
                } else {
                    $query = $query->orderBy($this->orderBy, $this->direction);
                }
            }

            // Si $wP es verdadero, paginar y contar los registros
            if ($wP) {
                $query = $query->paginate(8);
                $this->records = $query->total();
                // Si no hay paginación, obtener todos los resultados
            } else {
                $query = $query->get();
            }
            return $query;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 119244Servicioss"]);
        }
    }
    public static function searchService($query, $search)
    {

        $terms = collect(preg_split('/\s+/', trim($search)))
            ->filter(fn($term) => strlen($term) >= 2)   // ignorar palabras de 1 carácter
            ->unique();

        if ($terms->isNotEmpty()) {
            $query->where(function ($q) use ($terms) {
                foreach ($terms as $index => $term) {
                    // escapamos comodines para evitar comportamientos raros
                    $like = '%' . str_replace(['%', '_'], ['\%', '\_'], $term) . '%';
                    $method = $index === 0 ? 'where' : 'orWhere';

                    // agrupamos las condiciones de cada palabra
                    $q->$method(function ($sub) use ($like, $term) {
                        $sub->where('servicios.name', 'like', $like)
                            ->orWhere('servicios.description', 'like', $like)
                            ->orWhereRaw('SOUNDEX(servicios.name) = SOUNDEX(?)', [$term])
                            ->orWhereRaw('SOUNDEX(servicios.description) = SOUNDEX(?)', [$term]);
                    });
                }
            });
        }

        return $query;
    }
    public function orderBy($by, $direction)
    {
        $this->orderBy = $by;
        $this->direction = $direction;
        $this->orderByMostOrLessSelled = null;
        $this->selectedItems = [];
        $this->resetPage();
    }

    public function exportar()
    {
        try {
            $cat = null;
            $servicios = $this->loadServices(0);
            if ($this->cat != null) {
                $cat = categoria_servicio::find($this->cat);
                $cat = $cat->name;
            }
            $date = Carbon::now()->format('Y_m_d_H_i_s');
            $fileName = 'servicios_' . $date . '.xlsx';
            return Excel::download(new reporteServicios($servicios, $cat, 'servicios'), $fileName);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 497369Cliente"]);
        }
    }
    public function searching($searchText)
    {
        $this->search = trim($searchText);
        $this->selectedItems = [];
    }

    public function Add()
    {
        $this->resetValidation();
        $this->resetExcept('service', 'marcas', 'categorias');
        $this->loadDefault();
        $this->dispatchBrowserEvent('openCreate');
    }
    public function Delete()
    {
        foreach ($this->selectedItems as $servicio) {
            $this->destroy($servicio);
        }
        $this->loadDefault();

        $this->emit('refresh');
        $this->dispatchBrowserEvent('noty', ['msg' => 'SOLICITUD PROCESADA CON ÉXITO']);
    }

    public function removeFile($filename, $fromGallery)
    {
        try {
            if ($fromGallery) {
                // Filtrar el arreglo para eliminar el archivo con el nombre coincidente
                $this->gallery = array_filter($this->gallery, function ($file) use ($filename) {
                    return $file->getFilename() !== $filename;
                });
            } else {
                // Filtrar la colección para eliminar el archivo con la ruta coincidente
                $this->pictures = $this->pictures->filter(function ($picture) use ($filename) {
                    return $picture !== $filename;
                });
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 115459Agenda"]);
        }
    }
    public function Edit($id = null)
    {
        try {
            $this->loadDefault();
            $service = servicio::find($id ?? $this->selectedItems[0]);
            $this->categoriesList = implode(", ", $service->categorias->pluck('name')->toArray());
            $this->pictures = $service->photos;
            $this->resetValidation();
            $this->editing = true;
            $this->service = $service;

            // $materials = Material::where('servicio_id',$this->service->id)->get();

            // // Limpiar elementos existentes en cartP
            // $this->cartP = new Collection;
            // // Loop through each product associated with the servicio
            // foreach ($materials as $material) {
            //     $product=producto::find($material->producto_id);
            //     if($product){
            //         $salePrice = ($product->disccount_price > 0 && $product->disccount_price < $product->gross_price ?  $product->disccount_price : $product->gross_price);
            //         $uid = uniqid() . $product->id;
            //         $coll = collect([
            //             'id' => $uid,  // Make sure to define $uid appropriately
            //             'pid' => $product->id,
            //             'name' => $product->name,
            //             'gross_price' => floatval($product->gross_price),
            //             'sale_price' => floatval($salePrice),  // Make sure $salePrice is defined appropriately
            //             'qty' => $material->qty,
            //             'stock' => $product->stock_qty,
            //             'type' => $product->type_product,
            //             'unit_type' => $product->unit_type,
            //         ]);

            //         $itemCart = Arr::add($coll, null, null);
            //         $this->cartP->push($itemCart);
            //     }
            // }

            // $this->save();
            $this->dispatchBrowserEvent('openCreate');
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 154245Servicios"]);
        }
    }

    public function cancelEdit()
    {
        $this->resetValidation();
        $this->loadDefault();
        // $this->clear();  
    }

    function viewService(servicio $service)
    {
        $this->serviceSelected = $service;
        $this->dispatchBrowserEvent('view-service');
    }
    function calculate()
    {
        $this->service->disccount_price = null;
    }
    function calculateFinalDS()
    {
        try {
            if ($this->percent) {
                $disccountP = floatval($this->service->gross_price) - (floatval($this->service->gross_price) * floatval($this->percent / 100));
                $this->service->disccount_price = floatval($disccountP);
                $this->dispatchBrowserEvent('next');
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 215246Servicios"]);
        }
    }
    private function deleteFiles($files)
    {
        try {
            foreach ($files as $file) {
                $found = false;
                $filename = 'storage/servicios/' . $file->file;
                foreach ($this->pictures as $picture) {
                    if ($filename == $picture) {
                        $found = true;
                    }
                }
                if (!$found) {
                    unlink($filename);
                    $file->delete();
                }
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 545Agenda"]);
        }
    }
    function Store()
    {
        $this->validate($this->rules);
        try {

            $this->service->disccount_price = $this->service->disccount_price ? $this->service->disccount_price : null;
            $this->service->disccount_price = $this->service->disccount_price != '' ? $this->service->disccount_price : null;
            $this->service->salon_id = Auth::user()->salon->id;
            $this->service->save();

            // Rutina para eliminar los archivos que ya no se encuentren en el arreglo de pictures
            if (isset($this->service->files)) {
                $this->deleteFiles($this->service->files);
            }

            // Rutina que guarda los nuevos archivos subidos
            if (!empty($this->gallery)) {

                // guardar imagenes nuevas
                foreach ($this->gallery as $file) {
                    $fileName = uniqid() . '_.' . $file->extension();
                    $file->storeAs('public/servicios', $fileName);

                    // creamos relacion
                    $img = File::create([
                        'model_id' => $this->service->id,
                        'model_type' => 'App\Models\servicio',
                        'file' => $fileName
                    ]);

                    // guardar relacion
                    $this->service->files()->save($img);
                }
            }

            $listCategories = null;
            if ($this->categoriesList != null)  $listCategories =  explode(",", $this->categoriesList);


            //relacionar categorias         

            if ($listCategories != null) {

                $listCategories = array_map(function ($item) {
                    $catName = trim($item);
                    // verificar si el elemento no es numérico
                    if (!is_numeric($catName)) {
                        // buscar el ID de la categoría en la tabla correspondiente
                        $categoria = categoria_servicio::where('name', $catName)->where('salon_id', Auth::user()->salon->id)->first();
                        // reemplazar el elemento con el ID de la categoría si existe
                        if ($categoria) {
                            return $categoria->id;
                        }
                    }

                    // devolver el elemento sin cambios              
                    return $item;
                }, $listCategories);
            }
            $listCategories !== null ? $this->service->categorias()->sync($listCategories) : $this->service->categorias()->detach();

            // $cart = $this->cartP;


            // if($this->cartP){

            //     DB::transaction(function () use ($cart) {

            //         $service=$this->service;
            //         $oldMaterials = Material::where('servicio_id',$service->id);
            //         //asignaciones de venta
            //         // Elimina los materiales antiguos
            //         $oldMaterials->each(function ($oldMaterial) {
            //             $oldMaterial->delete();
            //         });
            //         $materials = $cart->map(function ($item) use ($service) {
            //             return [
            //                 'qty' => $item['qty'],
            //                 'sale_price' => $item['sale_price'],
            //                 'gross_price' => $item['gross_price'],
            //                 'producto_id' => $item['pid'],
            //                 'servicio_id' => $service->id,
            //                 'salon_id' => Auth::user()->salon->id
            //             ];
            //         })->toArray();
            //         Material::insert($materials);

            //         $this->dispatchBrowserEvent('noty', ['msg' => 'SOLICITUD PROCESADA CON ÉXITO']);
            //         //
            //         $this->emit('clear-cart');
            //     });
            //     $this->emit('refresh');
            //     $this->clear();
            //     $this->service = new servicio();
            //     $this->service->duration = '60';
            // }
            $this->loadDefault();
            $this->emit('refresh');
            $this->dispatchBrowserEvent('closeCreate');
            $this->dispatchBrowserEvent('noty', ['msg' =>  'SOLICITUD PROCESADA CON ÉXITO']);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 228247Servicios"]);
        }
    }
    public function destroy($servicioId)
    {
        try {
            $servicio = servicio::with('categorias', 'asignaciones', 'excepciones')->find($servicioId);

            // Desvincula las categorías y excepciones del servicio
            $servicio->categorias()->detach();
            $servicio->excepciones()->delete();

            //eliminar el archivo físicamente    ----------------------------        
            $servicio->files()->each(function ($img) {
                unlink('storage/servicios/' . $img->file);
            });
            //Eliminar archivo de la base de datos
            $servicio->files()->delete();

            // Verifica si tiene asignaciones sin cargar todas las relaciones
            if ($servicio->asignaciones()->count() > 0) {
                // Si tiene asignaciones, solo cambia el nombre y guarda
                $servicio->name = "Servicio eliminado";
                $servicio->save();
            } else {
                // Si no tiene asignaciones, desvincula y elimina el servicio
                $servicio->delete();
            }
            $this->selectedItems = [];
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 315248Servicios"]);
        }
    }

    public function startMerge()
    {
        try {
            $this->loadDefault();
            if (count($this->selectedItems) < 2) {
                $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Seleccione al menos dos servicios para combinar"]);
                return;
            }
            foreach ($this->selectedItems as $item) {
                $service = servicio::with('marca', 'categorias', 'files', 'asignaciones')->find($item);
                $this->mergeItems[] = $service;
            }
            $this->service = $this->mergeItems[0];
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 93241Servicios"]);
        }
    }

    public function cancelMerge()
    {
        $this->mergeItems = [];
        $this->selectedItems = [];
        $this->loadDefault();
    }
    private function transferRelation($relation, $attribute)
    {
        foreach ($relation as $item) {
            $item->$attribute = $this->service->id;
            $item->save();
        }
    }
    public function merge()
    {
        $this->validate($this->rules);
        DB::beginTransaction();
        try {
            if ($this->service->brand_id == '') {
                $this->service->brand_id = null;
            }
            // Guardar el servicio principal
            $this->service->save();
            $sid = $this->service->id;

            // Transferir relaciones del servicio principal
            foreach ($this->mergeItems as $itemArrayForm) {
                $item = servicio::with('categorias', 'files', 'asignaciones', 'excepciones')->find($itemArrayForm['id']);
                if ($item->id != $sid) {
                    //relacionar categorias         
                    $itemCategories = $item->categorias->pluck('id');
                    $this->service->categorias()->attach($itemCategories);

                    //relacionar archivos
                    if (isset($item->files)) $this->transferRelation($item->files, 'model_id');

                    // relacionar asignaciones
                    if (isset($item->asignaciones)) $this->transferRelation($item->asignaciones, 'selected_service');

                    // relacionar excepciones
                    if (isset($item->excepciones)) $this->transferRelation($item->excepciones, 'servicio_id');

                    //eliminar producto
                    $item->delete();
                }
            }
            $this->cancelMerge();
            $this->emit('refresh');
            $this->dispatchBrowserEvent('noty', ['msg' => 'Servicios combinados exitosamente']);
            DB::commit();
        } catch (\Throwable $th) {
            dd($th);
            DB::rollBack();
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 9241Servicios"]);
        }
    }
}
