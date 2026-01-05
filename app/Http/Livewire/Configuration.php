<?php

namespace App\Http\Livewire;

use App\Models\File;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Configuration extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $photo,$salon,$lada;

    protected $paginationTheme = 'bootstrap';
    protected $rules =    [
        'salon.name' => "nullable|min:3|max:200",
        'salon.email' => "nullable|max:65|unique:salon,email",
        'salon.rfc' => "nullable|max:15|unique:salon,rfc",
        'salon.webPage' => "nullable|max:100|unique:salon,webPage",
        'salon.facebook' => "nullable|max:100|unique:salon,facebook",
        'salon.instagram' => "nullable|max:100|unique:salon,instagram",
        'salon.youtube' => "nullable|max:100|unique:salon,youtube",
        'salon.tiktok' => "nullable|max:100|unique:salon,tiktok",
        'salon.phone' => "nullable|max:15|unique:salon,phone",
        'salon.start' => "nullable",
        'salon.end' => "nullable",
        'salon.simulador' => "nullable",
    ];
    public function mount()
    {
        $this->loadDefault();
    }

    private function loadDefault()
    {
        try{
            $this->salon = Auth::user()->salon;
            $this->photo = $this->salon->picture ?? null;
            $this->salon->phone = substr($this->salon->phone, -10);
            // Obtener el resto de la cadena
            $this->lada = substr($this->salon->phone, 0, strlen($this->salon->phone) - 10);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 38257Configuration"] );
        }
    }


    protected $listeners = ['refresh' => '$refresh'];

    public function render()
    {
        try{

            return view('livewire.ajustes.modulos.configuration',['salon' => $this->salon]);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 50258Configuration"] );
        }
    }

    public function cancelEdit()
    {
        $this->loadDefault();
    }

    public function Store()
    {
            $this->rules['salon.email'] = $this->salon->id > 0 ? "nullable|max:65|unique:salons,email,{$this->salon->id}" : 'nullable|max:65|unique:salons,email';
            $this->rules['salon.rfc'] = $this->salon->id > 0 ? "nullable|max:15|unique:salons,rfc,{$this->salon->id}" : 'nullable|max:15|unique:salons,rfc';
            $this->rules['salon.webPage'] = $this->salon->id > 0 ? "nullable|max:100|unique:salons,webPage,{$this->salon->id}" : 'nullable|max:100|unique:salons,webPage';
            $this->rules['salon.facebook'] = $this->salon->id > 0 ? "nullable|max:100|unique:salons,facebook,{$this->salon->id}" : 'nullable|max:100|unique:salons,facebook';
            $this->rules['salon.instagram'] = $this->salon->id > 0 ? "nullable|max:100|unique:salons,instagram,{$this->salon->id}" : 'nullable|max:100|unique:salons,instagram';
            $this->rules['salon.youtube'] = $this->salon->id > 0 ? "nullable|max:100|unique:salons,youtube,{$this->salon->id}" : 'nullable|max:100|unique:salons,youtube';
            $this->rules['salon.tiktok'] = $this->salon->id > 0 ? "nullable|max:100|unique:salons,tiktok,{$this->salon->id}" : 'nullable|max:100|unique:salons,tiktok';
            $this->rules['salon.phone'] = $this->salon->id > 0 ? "nullable|min:10|max:15|unique:salons,phone,{$this->salon->id}" : 'nullable|min:10|max:15|unique:salons,phone';
            $this->validate($this->rules);
            
        try{
            if($this->salon->phone){
                $this->salon->phone = $this->lada . $this->salon->phone;
            }else{
                $this->salon->phone = null;
            }
            $this->salon->save();

            if($this->photo!=null){
                $tempImg = $this->salon->file;

                //eliminar el archivo físicamente            
                if(isset($this->salon->file) && file_exists('storage/salon/' . $tempImg->file)){
                    $this->emit('refresh');
                    $this->dispatchBrowserEvent('noty', ['msg' => 'SOLICITUD PROCESADA CON ÉXITO']);
                    return;
                }

                //Volver a guardar el archivo nuevo
                $fileName = uniqid() . '_.' . $this -> photo -> extension();
                $this -> photo -> storeAs('public/salon', $fileName);

                //Crear el archivo en la tabla files
                $img = File::create([
                    'model_id' => $this -> salon -> id,
                    'model_type' => 'App\Models\Salon',
                    'file' => $fileName
                ]);

                $this -> salon -> file() -> save($img); 
            } else {
                if(isset($this->salon->file)){
                    unlink('storage/salon/' . $this->salon->file->file);
                    //Eliminar archivo de la base de datos
                    $this -> salon -> file() -> delete();
                }
            }

            $this->dispatchBrowserEvent('noty', ['msg' => 'SOLICITUD PROCESADA CON ÉXITO']);
            $this->emit('refresh');
        }catch(\Throwable $th){
            dd($th);
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 64259Configuration"] );
        }
    }
    
    public function removeFile()
    {
        $this->photo=null;
    }

}
