<?php

namespace App\Http\Livewire;

use App\Exports\ReporteGastos;
use App\Models\categoria_gasto;
use App\Models\File;
use App\Models\gasto;
use App\Models\marca;
use App\Models\tipo_gasto;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Livewire\WithFileUploads;

class Gastos extends Component
{
    use WithFileUploads;
    use WithPagination;
    public $currentDate, $currentDateEnd,$is_interval=false;
    public $start,$currentDateC,$end,$currentDateCEnd;

    public $editing,$metodosPago=[],$records,$gasto,$search,$action=1,$gastoSelected,$rest,$methods,$paymentMethod,$cash,$reference;
    public $max;
    public $total_bruto=0, $movs=0;
    public $brandId,$brand,$selectedFormaPago;
    protected $paginationTheme = 'bootstrap';

    public $query,$proveedores=[];
    public $gallery=[],$pictures=[],$respaldoFiles;
    public $queryCat,$queryType,$categorias=[],$tipos=[],$category,$categoryId,$type,$typeId;
    public function removeImage($index)
    {
        array_splice($this->gallery, $index, 1);
    }    
    public function createCat()
    {
        if($this->queryCat!=null){
            //guardar categoría
            $newCat =  new categoria_gasto;
            $newCat->name = $this->queryCat;
            $newCat->salon_id = Auth::user()->salon_id;
            $newCat->save();
            $this->category = $newCat;
            $this->categoryId = $newCat->id;
            $this->emit('refresh');
        }
    }
    public function createType()
    {
        if($this->queryType!=null){
            //guardar categoría
            $newCat =  new tipo_gasto;
            $newCat->name = $this->queryType;
            $newCat->salon_id = Auth::user()->salon_id;
            $newCat->save();
            $this->type = $newCat;
            $this->typeId = $newCat->id;
            $this->emit('refresh');
        }
    }
    public function updatedQueryCat()
    {
        try{
            
            $this->categorias = categoria_gasto::where('salon_id', Auth::user()->salon->id)
            ->where(function ($q) {
                $q->where('name', 'like', "%{$this->queryCat}%");
            })
            ->orderBy('name', 'asc')
            ->get();      

        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 2097Agenda"] );
        }
    }
    public function updatedQueryType()
    {
        try{
            
            $this->tipos = tipo_gasto::where('salon_id', Auth::user()->salon->id)
            ->orWhere('salon_id',null)
            ->where(function ($q) {
                $q->where('name', 'like', "%{$this->queryType}%");
            })
            ->orderBy('name', 'asc')
            ->get();      

        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 2097Agenda"] );
        }
    }
    public function removeFile($filename,$fromGallery)
    {
        try{
            if($fromGallery){
                // Filtrar el arreglo para eliminar el archivo con el nombre coincidente
                $this->gallery = array_filter($this->gallery, function($file) use ($filename) {
                    return $file->getFilename() !== $filename;
                });
            }else{
                // Filtrar la colección para eliminar el archivo con la ruta coincidente
                $this->pictures = $this->pictures->filter(function ($picture) use ($filename) {
                    return $picture !== $filename;
                });
            }
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 115459Gastos"] );
        }
    }
    public function filesDroped($files)
    {
        $this->gallery[] = $files;
    }


    public function mount()
    {
        try{
            $this->loadDefault();
            $this->loadFecha();
            $this->rest = $this->gasto->total;
            if (session()->has('methodsG')) {
                $this->methods = session('methodsG');
                $this->calculateRest();
            } else {
                $this->methods = new Collection;
            }
            
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 22109Gastos"] );
        }
    }
    
    function updatedQuery()
    {
        try{
            $this->proveedores = marca::where('salon_id', Auth::user()->salon->id)
            ->where('name', '!=', 'Marca eliminada')
            ->where(function ($q) {
                $q->where('name', 'like', "%{$this->query}%")
                  ->orWhere('contact_name', 'like', "%{$this->query}%")
                  ->orWhere('rfc', 'like', "%{$this->query}%")
                  ->orWhere('phone_number', 'like', "%{$this->query}%")
                  ->orWhere('email', 'like', "%{$this->query}%");
            })
            ->orderBy('name', 'asc')
            ->get();
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 2097CartView"] );
        }
    }
    protected $rules =
    [
        'gasto.note' => "nullable|min:3|max:255",
        'gasto.folio_fiscal' => "nullable|min:36|max:36",
        'gasto.date' => "required",
        'gasto.total' => "required|numeric",
        'gasto.iva' => "nullable|in:0.16,0.08,0",
        'gasto.type' => "required|in:Acreditable,No acreditable",
        'gasto.payment_method' => "required|in:Caja chica,Efectivo,Cheque nominativo,Transferencia electrónica de fondos,Tarjeta de crédito,Monedero electrónico,Dinero electrónico,Vales de despensa,Dación en pago,Pago por subrogación,Pago por consignación,Condonación,Compensación,Novación,Confusión,Remisión de deuda,Prescripción o caducidad,A satisfacción del acreedor,Tarjeta de débito,Tarjeta de servicios,Aplicación de anticipos,Intermediario pagos,Por definir",
    ];

    protected $listeners = [
        'refresh' => '$refresh',
        'search' => 'searching',
        'DeleteExpense' => 'Delete','setRest','datesSelected' => 'setDatesFromPeriod',
        'prevDay','dateSelected' => 'setDate','setBrandId','enviarProveedor'=>'recibirProveedor'
    ];
    public function verificarFactura(gasto $gasto)
    {
        $this->validateInvoiceWithSAT($gasto);
    }

    private function validateInvoiceWithSAT($gasto)
    {

        // URL del servicio de consulta de CFDI del SAT
        $wsdl = 'https://consultaqr.facturaelectronica.sat.gob.mx/ConsultaCFDIService.svc?wsdl';

        // Crear un cliente SOAP
        $client = new \SoapClient($wsdl);

        $re = $gasto->marca->rfc;
        $rr = Auth::user()->salon->rfc;
        $tt = $gasto->total;
        $id = $gasto->folio_fiscal;

        // Preparar la solicitud
        $params = [
            'expresionImpresa' => '?re=' . $re . '&rr=' . $rr . '&tt=' . $tt . '&id=' . $id
        ];

        // Llamar al método de consulta del servicio
        $response = $client->__soapCall('Consulta', [$params]);

        // Procesar la respuesta
        $estado = $response->ConsultaResult->Estado ?? null;

        // Verificar el estado del CFDI
        if ($estado) {
            switch ($estado) {
                case 'Vigente':
                    $this->dispatchBrowserEvent('noty', ['msg' =>  "CFDI vigente"] );
                    $gasto->status='vigente';
                    $gasto->save();
            break;
                case 'Cancelado':
                    $this->dispatchBrowserEvent('noty', ['msg' =>  "CFDI cancelado"] );
                    $gasto->status='cancelado';
                    $gasto->save();
            break;
                default:
                    $this->dispatchBrowserEvent('noty', ['msg' =>  "CFDI desconocido. Revise los datos"] );
                    $gasto->status='default';
                    $gasto->save();
        }
        } else {
            $this->dispatchBrowserEvent('noty', ['msg' =>  "CFDI desconocido. Revise los datos"] );
        }
    }

    public function recibirProveedor($brandId)
    {
        $this->dispatchBrowserEvent('closeModalBrand');
        $this->setBrandId($brandId);
    }

    public function render()
    {
        try{
            //validamos que exista la sesion
            if (session()->has('methodsG')) {
                //obtenemos los métodos de pago
                $methods = session('methodsG');
                // ordenar los métodos mediante nombre de forma asc
                $methodsInfo = $methods->sortBy(['name', ['name', 'asc']]);
            } else {
                $methodsInfo = new Collection;
            }
            return view('livewire.gastos.gastos',compact('methodsInfo'),[
                'gastos' => $this->useDate()
            ]);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 54110Gastos"] );
        }
    }
    private function calculateRest()
    {
        try{
            if($this->rest==$this->gasto->total){
                foreach($this->methods as $method){
                        $this->rest -= $method['qty'];
                }
            }
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 73111Gastos"] );
        }
    }
    public function setRest()
    {
        try{
            $this->rest = $this->gasto->total;
            $this->calculateRest();
            $this->dispatchBrowserEvent('modal-payment-gastos');
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 85112Gastos"] );
        }
    }
    public function setMethod($paymentMethod)
    {
        $this->calculateRest();
        $this->paymentMethod = $paymentMethod;
        $this->AddMethod();
    }
    // private function AddMethod()
    // {
    //     try{
    //         if ($this->inMethods()) {
    //             $this->updateQty();
    //             return; // => con esta línea se agrupan los productos por nombre dentro del carrito
    //         }
    //         $qty=$this->cash;
    //         $this->rest-= $this->cash;
    //         if($this->rest < 0){
    //             $this->dispatchBrowserEvent('noty-error', ['msg' => 'EL MÉTODO SOBREPASA LA CANTIDAD']);
    //             $this->rest+= $this->cash;

    //             return;
    //         }

    //         $uid = uniqid();
    //         $coll = collect(
    //             [
    //                 'uid' => $uid,
    //                 'name' => $this->paymentMethod,
    //                 'qty' => floatval($qty),
    //                 'reference' => $this->reference,
    //             ]
    //         );
    //         $method = Arr::add($coll, null, null);
    //         $this->methods->push($method);

    //         $this->save();
    //         $this->reset(['cash','reference']);
    //     }catch(\Throwable $th){
    //         $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 101113Gastos"] );
    //     }
    // }
    // private function updateQty()
    // {
    //     try{
    //         $mymethods = $this->methods; 
    //         $oldItem = $mymethods->where('name', $this->paymentMethod)->first();
            
    //         $newItem  = $oldItem;

    //         $newItem['qty'] += floatval($this->cash);
            
    //         $this->rest-= $this->cash;
    //         if($this->rest < 0){
    //             $this->dispatchBrowserEvent('noty-error', ['msg' => 'EL MÉTODO SOBREPASA LA CANTIDAD']);
    //             $this->rest+= $this->cash;

    //             return;
    //         }
    //         //eliminar el item de la coleccion / sesion
    //         $paymentMethod=$this->paymentMethod;
    //         $this->methods = $this->methods->reject(function ($method) use ($paymentMethod) {
    //             return $method['name'] === $paymentMethod;
    //         });

    //         $this->methods->push(Arr::add($newItem, null, null));
    //         $this->save();
    //         $this->reset(['cash','reference']);

    //         $this->dispatchBrowserEvent('noty', ['msg' => 'MÉTODO ACTUALIZADO']);
    //     }catch(\Throwable $th){
    //         $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 135114Gastos"] );
    //     }
    // }
    // public function removeMethod($uid)
    // {
    //     try{
    //         $this->methods = $this->methods->reject(function ($method) use ($uid) {
    //             return $method['uid'] === $uid;
    //         });
    //         $this->save();
    //         $this->rest = $this->gasto->total;
    //         $this->calculateRest();

    //     }catch(\Throwable $th){
    //         $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 167115Gastos"] );
    //     }
    // }
    private function inMethods()
    {
        try{
            $mymethods = $this->methods;

            $cont = $mymethods->where('name', $this->paymentMethod)->count();

            return  $cont > 0 ? true : false;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 183116Gastos"] );
        }
    }
    private function save()
    {
        try{
            session()->put('methodsG', $this->methods);
            session()->save();
            
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 195117Gastos"] );
        }
    }
    public function searching($searchText)
    {
        try{
            $this->search = trim($searchText);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 231119Gastos"] );
        }
    }
    public function Delete(gasto $gasto)
    {
        $this -> destroy($gasto);
        $this -> loadDefault();
    }
    public function Edit(gasto $gasto)
    {
        try{
            $this->resetValidation();
            $this->loadDefault();
            $this->gasto = $gasto;
            $this->gasto->date = Carbon::parse($gasto->date)->format('Y-m-d');
            $this->pictures = $this->gasto->photos;
            $this->setBrandId($gasto->marca_id);
            $this->setCategoryId($gasto->categoria_id);
            $this->setTypeId($gasto->tipo_id);
            $this->rest = $gasto->total;
            // $this->restoreMethods($gasto->metodosPago);
            $this->action = 3;
            $this->editing = true;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 243120Gastos"] );
        }
    }
    // private function restoreMethods($methods)
    // {
    //     try{
    //         foreach($methods as $method){
    //             $this->cash=$method->amount;
    //             if(isset($method->reference)){
    //                 $this->reference=$method->reference;
    //             }
    //             $this->setMethod($method->Payment_method);
    //             $this->save();
    //         }
    //     }catch(\Throwable $th){
    //         $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 257121Gastos"] );
    //     }
    // }
    public function cancelEdit()
    {
        $this->resetValidation();
        $this->loadDefault();
        $this->action = 1;
    }
    private function destroy(gasto $gasto)
    {
        try{
            $this->deleteFiles($gasto->files);

            //eliminar gasto
            $gasto->delete();

            $this->dispatchBrowserEvent('stop-loader');
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 278122Gastos"] );
        }
    }
    private function deleteFiles($files)
    {
        try{
            foreach($files as $file) {
                $filename = 'storage/gastos/' . $file->file; 
                unlink($filename);
                $file->delete();
            }
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 545Gastos"] );
        }
    }
    private function getTotal()
    {
        try{
            $total = $this->methods->sum(function ($method) {
                if(isset($method['qty'])){
                    return $method['qty'];
                }else{
                    return 0;
                }
            });
            return $total;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 298123Gastos"] );
        }
    }
    public function setBrandId($brandId)
    {
        $this->brandId = $brandId;
        $this->brand = marca::find($brandId);
    }
    public function unsetBrand()
    {
        $this->brandId = null;
        $this->brand = marca::find(null);
        $this->emit('reloadTom');
    }
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;
        $this->category = categoria_gasto::find($categoryId);
    }
    public function unsetCategory()
    {
        $this->categoryId = null;
        $this->category = categoria_gasto::find(null);
        $this->emit('reloadTom');
    }
    public function setTypeId($typeId)
    {
        $this->typeId = $typeId;
        $this->type = tipo_gasto::find($typeId);
    }
    public function unsetType()
    {
        $this->typeId = null;
        $this->type = tipo_gasto::find(null);
        $this->emit('reloadTom');
    }
    private function storeImages($gallery)
    {
        //gallery
        if (!empty($gallery)) {
            // guardar imagenes nuevas
            foreach ($gallery as $file) {
                $fileName = uniqid() . '_.' . $file->extension();
                $file->storeAs('public/gastos', $fileName);

                // creamos relacion
                $img = File::create([
                    'model_id' => $this->gasto->id,
                    'model_type' => 'App\Models\gasto',
                    'file' => $fileName
                ]);

                // guardar relacion
                $this->gasto->files()->save($img);
            }
        }
    }
    private function compareFiles($files)
    {
        try{
            //gallery
            if (!empty($files)) {
                foreach($files as $file) {
                    $found=false;
                    $filename = 'storage/gastos/' . $file->file; 
                    foreach($this->pictures as $picture){
                        if($filename == $picture){
                            $found = true;
                        }
                    }
                    if(!$found){
                        unlink($filename);
                        $file->delete();
                    }else{
                        $this->respaldoFiles[] = $file->id;
                    }
                }
            }
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 512345Agenda"] );
        }
    }
    private function vincularFiles()
    {
        try{
            foreach($this->respaldoFiles as $file_id){
                $file = File::find($file_id);
                if($file!=null){
                    $file->model_id = $this->gasto->id;
                    $file->save();
                }
            }
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 14031Agenda"] );
        }
    }
    public function Store()
    {
        $this->validate($this->rules);
        try{
            if (session()->has('customDate')) {
                Carbon::setTestNow(Carbon::createFromFormat('Y-m-d', session('customDate')));
            }
            if($this->editing){
                $oldGasto = $this->gasto;
                //eliminar gasto
                $this->compareFiles($this->gasto->files);
                $this->gasto->delete();
                $this->gasto = $oldGasto;
            }
            //save
            $this->gasto->user_id = Auth()->user()->id;     
            $this->gasto->salon_id=Auth::user()->salon->id;
            $this->gasto->marca_id=$this->brandId;
            $this->gasto->categoria_id=$this->categoryId;
            $this->gasto->tipo_id=$this->typeId;
            $this->gasto->status='default';
            $this->gasto->save();
            $this->storeImages($this->gallery);
            if($this->respaldoFiles){
                $this->vincularFiles();
            }
            $gasto=$this->gasto;
            if($this->brandId!=null && $this->gasto->folio_fiscal!=null && Auth::user()->salon->rfc!=null){
                $this->validateInvoiceWithSAT($gasto);
            }
            $this->dispatchBrowserEvent('noty', ['msg' => 'SOLICITUD PROCESADA CON ÉXITO']);
            $this->loadDefault();

            if (session()->has('customDate')) {
                Carbon::setTestNow();
            }
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 340235Gastos"] );
        }
    }
    private function loadDefault()
    {
        try{
            $this->gasto = new gasto();
            $this->gasto->type = 'No acreditable';
            $this->gasto->iva = 0.16;
            $currentDate=Carbon::now()->format('Y-m-d');
            $this->gasto->date = $currentDate;
            $this->editing = false;
            $this->brandId = null;
            $this->brand = null;
            $this->categoryId = null;
            $this->category = null;
            $this->typeId = null;
            $this->type = null;
            $this->pictures=null;
            $this->gallery=null;
            $this->categorias=Auth::user()->salon->categoriaGastos;
            $this->tipos = tipo_gasto::where('salon_id',Auth::user()->salon_id)->orWhere('salon_id',null)->get();
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 340125Gastos"] );
        }
    }
    // private function clear()
    // {
    //     try{
    //         $this->methods = new Collection;
    //         $this->save();
    //         $this->emit('refresh');
    //     }catch(\Throwable $th){
    //         $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 355126Gastos"] );
    //     }
    // }
    private function loadFecha()
    {
        try{
            $this->currentDate=Carbon::now()->locale('es')->isoFormat('dddd, D MMMM YYYY');
            $this->start=Carbon::now()->toDateString();
            $this->currentDateC=Carbon::now();
            $this->currentDateEnd='';
            $this->end='';
            $this->currentDateCEnd='';
            $this->aplicarFiltros();
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 103356InformeMovimientos"] );
        }
    }

    public function setDatesFromPeriod($selectedDates)
    {
        try{
            if (count($selectedDates) >= 2) {
                // Actualizar las fechas según la lógica que necesites
                $currentDateC = Carbon::parse($selectedDates[0]);
                $currentDateCEnd = Carbon::parse($selectedDates[1]);
            
            
                $this->currentDateC = Carbon::parse($currentDateC);
                $this->currentDateCEnd = Carbon::parse($currentDateCEnd);
                $this->is_interval=true;
                $this->currentDate=$this->currentDateC->locale('es')->isoFormat('dddd, D MMMM YYYY');
                $this->start=$this->currentDateC->toDateString();
                $this->currentDateEnd=$this->currentDateCEnd->locale('es')->isoFormat('dddd, D MMMM YYYY');
                $this->end=$this->currentDateCEnd->toDateString();
                $this->aplicarFiltros();
                $this->loadDatesWithNewPeriod();
            }
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 118357InformeMovimientos"] );
        }
    }
    public function setDate($selectedDate)
    {
        try{
            // Actualizar las fechas según la lógica que necesites
            $currentDateC = Carbon::parse($selectedDate[0]);
            $this->currentDateC = Carbon::parse($currentDateC);
            $this->is_interval=false;
            $this->currentDate=$this->currentDateC->locale('es')->isoFormat('dddd, D MMMM YYYY');
            $this->start=$this->currentDateC->toDateString();
            $this->currentDateEnd='';
            $this->end='';
            $this->currentDateCEnd='';
            $this->aplicarFiltros();
            $this->loadDatesWithNewPeriod();
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 141358InformeMovimientos"] );
        }
    }
    
    #Función que establece un día anterior 
    public function prevDay()
    {
        try{
            $this->is_interval=false;
            $this->currentDateC= $this->currentDateC->subDay();
            $this->start= $this->currentDateC->toDateString();
            $this->currentDate= $this->currentDateC->locale('es')->isoFormat('dddd, D MMMM YYYY');
            $this->aplicarFiltros();
            $this->loadDatesWithNewPeriod();
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 161359InformeMovimientos"] );
        }
    }
    #Función que retorna la fecha actual
    public function returnToday()
    {
        $this->is_interval=false;
        $this->loadFecha();
        $this->loadDatesWithNewPeriod();
    }
    #Función que retorna información de "ayer"
    public function returnYesterday()
    {
        $this->loadFecha();
        $this->prevDay();
    }
    
    public function setWeek()
    {
        try{
            $this->is_interval=true;
            $this->currentDate=Carbon::now()->startOfWeek()->locale('es')->isoFormat('dddd, D MMMM YYYY');
            $this->start=Carbon::now()->startOfWeek()->toDateString();
            $this->currentDateC=Carbon::now()->startOfWeek();
            $this->currentDateEnd=Carbon::now()->endOfWeek()->locale('es')->isoFormat('dddd, D MMMM YYYY');
            $this->end=Carbon::now()->endOfWeek()->toDateString();
            $this->currentDateCEnd=Carbon::now()->endOfWeek();
            $this->aplicarFiltros();
            $this->loadDatesWithNewPeriod();
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 188360InformeMovimientos"] );
        }
    }
    public function setMonth()
    {
        try{
            $this->is_interval=true;
            $this->currentDate=Carbon::now()->startOfMonth()->locale('es')->isoFormat('dddd, D MMMM YYYY');
            $this->start=Carbon::now()->startOfMonth()->toDateString();
            $this->currentDateC=Carbon::now()->startOfMonth();
            $this->currentDateEnd=Carbon::now()->endOfMonth()->locale('es')->isoFormat('dddd, D MMMM YYYY');
            $end=Carbon::now()->endOfMonth()->addDay();
            $this->end = $end->toDateString();
            $this->currentDateCEnd=Carbon::now()->endOfMonth();
            $this->aplicarFiltros();
            $this->loadDatesWithNewPeriod();
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 204361InformeMovimientos"] );
        }
    }
    public function setYear()
    {
        try{
            $this->is_interval=true;
            $this->currentDate=Carbon::now()->startOfYear()->locale('es')->isoFormat('dddd, D MMMM YYYY');
            $this->start=Carbon::now()->startOfYear()->toDateString();
            $this->currentDateC=Carbon::now()->startOfYear();
            $this->currentDateEnd=Carbon::now()->endOfYear()->locale('es')->isoFormat('dddd, D MMMM YYYY');
            $this->end=Carbon::now()->endOfYear()->toDateString();
            $this->currentDateCEnd=Carbon::now()->endOfYear();
            $this->aplicarFiltros();
            $this->loadDatesWithNewPeriod();
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 221362InformeMovimientos"] );
        }
    }
    private function recalculate($query)
    {
        $movs = $query->get();
        $totalMovs = $movs->count();
        $total = 0;
        foreach($movs as $mov){
            $total += $mov->total; 
        }
        return [$totalMovs,$total];
    }
    private function useDate($wp=true)
    {
        try{
            $query = [];
            if($this->is_interval==false){
                $query =  gasto::with('categoria')
                ->where('salon_id',Auth::user()->salon->id)
                ->whereDate('date',$this->currentDateC)
                ->orderBy('type', 'desc');
            }else{
                $query =  gasto::with('categoria')
                ->where('salon_id',Auth::user()->salon->id)
                ->whereBetween('date', [$this->currentDateC,$this->currentDateCEnd])
                ->orderBy('type', 'desc');
            }

            $data = $this->recalculate(clone $query);
            $this->total_bruto = $data[1];
            $this->movs = $data[0];

            if($wp){
                $query = $query->paginate(6);
            }
            return $query;
            
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 235363InformeMovimientos"] );
        }
    }
    
    public function aplicarFiltros()
    {
        if(isset($this->max)){
            $this->consultaRangos();
        }else{
         $this->useDate();
        }
    }
    private function consultaRangos()
    {
        try{
            $query = [];
            
            if($this->is_interval==false){
                $query =  gasto::where('salon_id',Auth::user()->salon->id)
                ->whereBetween('total',[$this->min ?? 0,$this->max])
                ->whereDate('date',$this->start)
                ->orderBy('type', 'desc')
                ->paginate(6);
            }else{
                $query =  gasto::where('salon_id',Auth::user()->salon->id)
                ->whereBetween('total',[$this->min ?? 0,$this->max])
                ->whereBetween('date', [$this->start,$this->end])
                ->orderBy('type', 'desc')
                ->paginate(6);
            }
            return $query;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 360366InformeMovimientos"] );
        }
    }

    #función que actualiza las gráficas con la nueva fecha
    private function loadDatesWithNewPeriod()
    {
        try{
            $this->emit('dateUpdated-gastos', $this->currentDate,$this->currentDateEnd);
            $this->emit('reloadTom');
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 343365InformeMovimientos"] );
        }
    }
    public function generatePdf(){
        $date = $this->currentDateC;
        $date = $date->format('Y_m_d_H_i_s');
        $fileName = 'gastos_' . $date . '.pdf';
        $gastos = $this->useDate();
        return Excel::download(new ReporteGastos($gastos,true),$fileName);
    }
    public function generateExcel()
    {
        $date = $this->currentDateC;
        $date = $date->format('Y_m_d_H_i_s');
        $fileName = 'gastos_' . $date . '.xlsx';
        $gastos = $this->useDate(false);

        return Excel::download(new ReporteGastos($gastos->get(),false),$fileName);
    }
}
