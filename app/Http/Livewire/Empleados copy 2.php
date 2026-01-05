<?php

namespace App\Http\Livewire;

use App\Models\Comision;
use App\Models\Empleado;
use App\Models\empleado_huella;
use App\Models\File;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class Empleados extends Component
{
    public $search, $editing, $records, $empleadoSeleccionado, $usuarios,$password,$username,$role,$email;
    public $intentos,$max_intentos=5;
    public Empleado $empleado;
    public $salon_id;
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    protected $rules =
    [
        'empleado.first_name' => "required|min:3|max:80",
        'empleado.last_name' => "nullable|min:3|max:80",
        'email' => "required|max:80|email|unique:empleados,email",
        'empleado.phone_number' => "nullable|min:7|max:15|unique:empleados,phone_number",
        'empleado.birth_date' => 'nullable',
        'empleado.is_active' => 'nullable',
    ];

    public function mount($search=null)
    {
        try{
            if(!$this->validateSuscription()){
                return redirect()->route('suscripcion');
            }
            $this->search=$search;
            $this->clear();
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 32192Empleados"] );
        }
    }
    private function validateSuscription()
    {
        $rights = true;

        $suscription = Auth::user()->salon->suscription;

        if($suscription == 'free') {
            $hoy = Carbon::now();
            $salon = Auth::user()->salon;
            $lastest_suscription = $salon->suscripciones()->latest()->first();
            if($lastest_suscription){
                if($hoy->diffInDays($lastest_suscription) > 7) {
                    $rights = false;
                }
            }else{
                if($hoy->diffInDays($salon->created_at) > 7) {
                    $rights = false;
                }
            }
            
            $rights = false;
        }

        return $rights;
    }
    protected $listeners = [
        'refresh' => '$refresh',
        'search' => 'searching',
        'Delete','Activate','Deactivate','sendFingerprint','returnPatterns'
    ];
    public function createFingerprint(empleado $empleado)
    {
        $this->empleado = $empleado;
        session()->put('checador', false);
        session()->save();
        $this->emit('contarIntentos',$this->intentos,$this->max_intentos);
    }
    // public function sendFingerprint($base64Image)
    // {
    //     $this->storeFingerp($base64Image);
    // }
    public function returnPatterns($untitledImage)
    {
        if (session('checador')) return;

        if (!$untitledImage || !str_starts_with($untitledImage, 'data:image/')) {
            $this->dispatchBrowserEvent('noty-error', ['msg' => 'Formato de imagen inválido']);
            return;
        }

        // Solo en el primer intento se borran las muestras anteriores
        if ($this->intentos === 0 && $this->empleado->files) {
            $this->deleteFiles($this->empleado->files);
        }

        // Guardar la nueva imagen
        $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $untitledImage));
        $relativePath = 'empleados/' . Auth::user()->salon_id;
        $filename = 'huella_' . $this->empleado->first_name . '_' . $this->intentos . '.png';

        // Asegurarse de que el directorio exista
        Storage::disk('public')->makeDirectory($relativePath);

        // Guardar el archivo en el disco 'public'
        Storage::disk('public')->put($relativePath . '/' . $filename, $image);

        $img = File::create([
            'model_id' => $this->empleado->id,
            'model_type' => get_class($this->empleado),
            'file' => $filename
        ]);
        
        $this->empleado->files()->save($img);

        // Aumentar contador y emitir evento
        $this->intentos++;
        $this->emit('contarIntentos', $this->intentos, $this->max_intentos);

        // Si se llegó al número máximo de muestras
        if ($this->intentos >= $this->max_intentos) {
            $this->dispatchBrowserEvent('closeFingerprint');
            $this->dispatchBrowserEvent('noty', ['msg' => 'SOLICITUD PROCESADA CON ÉXITO']);
            $this->clear();
            $this->emit('refresh');

            session()->put('checador', true);
            session()->save();
        }
    }

    private function deleteFiles($files)
    {
        try {
            foreach ($files as $file) {
                $filePath = storage_path('app/public/empleados/' . Auth::user()->salon_id . '/' . $file->file);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                $file->delete();
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' => "Código de error: 545empleados"]);
        }
    }

    // private function storeFingerp($fileUploaded)
    // {
    //     if(session('checador')){
    //         return;
    //     }
    //     // Extraer solo el contenido base64, eliminando el encabezado 'data:image/png;base64,'
    //     $image = str_replace('data:image/png;base64,', '', $fileUploaded);
    //     $image = str_replace(' ', '+', $image); // Asegurarse de que el base64 sea válido
    //     $imageData = base64_decode($image);

    //     // Crear un nombre de archivo único para la imagen
    //     $fileName = 'fingerprint_' . time() . '.png';
    //     dd($this->empleado->photos(Auth::user()->salon_id));
    //     // Rutina para eliminar los archivos que ya no se encuentren en el arreglo de pictures
    //     if(isset($this->empleado->files)){
    //         $this->deleteFiles($this->empleado->files);
    //     }

    //     // Almacenar el archivo en el disco público (puedes ajustar la ruta)
    //     Storage::disk('public')->put('empleados/' . $fileName, $imageData);

    //     // Crear la relación de archivo en la base de datos
    //     $img = File::create([
    //         'model_id' => $this->empleado->id,
    //         'model_type' => 'App\Models\Empleado',
    //         'file' => 'empleados/' . $fileName,
    //     ]);

    //     // Relacionar el archivo con el empleado
    //     $this->empleado->files()->save($img);

    //     $this->dispatchBrowserEvent('closeFingerprint' );
    //     $this->emit('refresh');
    //     $this->dispatchBrowserEvent('noty', ['msg' => 'SOLICITUD PROCESADA CON ÉXITO']);
    // }
    public function visibilidadEmpleado(Empleado $empleado,$visible)
    {
        try{
            $empleado->visible=$visible;
            $empleado->save();
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 48193Empleados"] );
        }
    }
    function Activate(Empleado $empleado)
    {
        try{
            $empleado->is_active=true;
            $empleado->save();
            if(isset($empleado->user)){
                $this->dispatchBrowserEvent('noty', ['msg' =>  "Asigne una contraseña nueva, por favor"] );
            }
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 48193Empleados"] );
        }
    }
    function Deactivate(Empleado $empleado)
    {
        try{
            $empleado->is_active=false;
            if(isset($empleado->user)){
                $empleado->user->password='';
            }
            $empleado->save();
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 57194Empleados"] );
        }
    }

    public function render()
    {
        try{
            return view('livewire.empleados.empleados',[
                'empleados' => $this->loadEmployee()
            ]);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 67195Empleados"] );
        }
    }
    public function loadEmployee()
    {
        try{
            if (!empty($this->search)) {

                $this->resetPage();

                $query = Empleado::with('user')->where(function ($query) {
                        $query->where('first_name', 'like', "%{$this->search}%")
                            ->orWhere('email', 'like', "%{$this->search}%")
                            ->orWhere('last_name', 'like', "%{$this->search}%");
                    })
                    ->where('salon_id',Auth::user()->salon->id)
                    ->where('visible',1)
                    ->orderBy('first_name', 'asc')
                    ->paginate(14);
                    
            } else {
                $query =  Empleado::orderBy('first_name', 'asc')->where('salon_id',Auth::user()->salon->id)->paginate(14);
            }

            $this->records = $query->total();

            return $query;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 77196Empleados"] );
        }
    }

    public function searching($searchText)
    {
        $this->search = trim($searchText);
    }
    public function Add()
    {
        $this->resetValidation();
        $this->resetExcept('empleado');
        $this->empleado = new Empleado();
        $this->empleado->user = null;
    }
    public function Edit($empleado)
    {
        $this->clear();
        $this->role = null;
        $empleado = Empleado::with('user')->find($empleado);
        if(isset($empleado->user)){
            $this->username = $empleado->user->name ? $empleado->user->name : '';
            $this->role = $empleado->user->role;
            $this->email = $empleado->user->email;
        }
        $this->empleado = $empleado;
        $this->editing = true;
        $this->salon_id = Auth::user()->salon->id;
        $this->emit('refresh');
    }
    public function cancelEdit()
    {
        $this->resetValidation();
        $this->resetExcept(['empleado', 'usuarios']);
        $this->empleado = new Empleado();
        $this->empleado->user = null;
        $this->empleado->is_active = true;
        $this->editing = false;
    }

    function Store()
    {
        $this->rules['empleado.phone_number'] = $this->empleado->id > 0 ? "nullable|min:7|max:15|unique:empleados,phone_number,{$this->empleado->phone_number}" : 'nullable|min:7|max:15|unique:empleados,phone_number';
        $this->rules = [
            'empleado.first_name' => "required|min:3|max:80",
            'empleado.last_name' => "nullable|min:3|max:80",
            'empleado.birth_date' => 'nullable',
            'empleado.is_active' => 'nullable',
            'username' => 'nullable',
            'password' => 'nullable',
        ];
        $this->empleado->salon_id=Auth::user()->salon->id;

        if($this->username!==null){
            
            if(!$this->editing){
                $this->crearUsuario();
            }else{
                if($this->empleado->user_id!==null){
                    $user = $this->empleado->user;
                    $user->name = $this->username;
                    $user->email = $this->email;
                    $user->role = $this->role;
                    if($this->password!=null){
                        $user->password = Hash::make($this->password);
                    }
                    $user->save();
                }else{
                    $this->crearUsuario();
                }
            }
        }
        $this->validate($this->rules);

        try{
            
            if (session()->has('customDate')) {
                Carbon::setTestNow(Carbon::createFromFormat('Y-m-d', session('customDate')));
            }

            //save
            $this->empleado->save();

            if(!$this->editing){
                $comision = new Comision();
                $comision->empleado_id = $this->empleado->id;
                $comision->save();
            }

            $this->dispatchBrowserEvent('noty', ['msg' => 'SOLICITUD PROCESADA CON ÉXITO']);
            $this->clear();
            $this->emit('refresh');
            if (session()->has('customDate')) {
                Carbon::setTestNow();
            }
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 144197Empleados"] );
        }
    }

    private function crearUsuario()
    {
        $rules = [
            'username' => "required|max:255",
            'password' => "required|max:255",
            'email' => "required|min:2|max:255|unique:users,email",
            'role' => "required|in:admin,estilista,recepcionista"
        ];
        $this->validate($rules);
            $user = User::create([
                'name' => $this->username,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'salon_id' => Auth::user()->salon->id,
                'role' => $this->role
            ]);

            $this->empleado->user()->associate($user);  // Asocia el nuevo usuario al empleado
    }
    private function clear()
    {
        $this->username = null;
        $this->password = null;
        $this->email = null;
        $this->role = 'admin';
        
        $this->empleado = new Empleado();
        $this->editing = false;
        $this->usuarios = User::orderBy('name')->get();
        $this->empleado->is_active = true;

        $this->intentos = 0;
    }
}
