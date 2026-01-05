<?php

namespace App\Http\Livewire;

use App\Models\calificacion_cliente_empleado;
use App\Models\cliente;
use App\Models\procedencia;
use App\Models\respuesta;
use App\Models\Salon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class EncuestaClientes extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public cliente $cliente; // propiedad de tipo cliente
    public $qt1,$qt2=[], $qt3, $qt4, $calificacion,$review,$lada;
    protected $listeners = ['refresh' => '$refresh'];
    public $procedencias=[];
    protected $rules =
    [
        'cliente.first_name' => "required|min:3|max:35",
        'cliente.last_name' => "nullable|min:3|max:35",
        'cliente.birth_date' => 'nullable',
        'cliente.postcode' => "nullable|min:5|max:5",
        'cliente.sexo' => 'nullable|in:masculino,femenino,noBinario',
        'cliente.procedencia_id' => 'nullable',
        'cliente.want_offers' => 'nullable',
        'cliente.email' => "nullable|max:65|email|unique:clientes,email",
        'cliente.phone' => "required|min:7|max:15|unique:clientes,phone",
        'qt1' => 'required',
        'qt2' => 'required',
        'qt3' => 'required',
        'qt4' => 'required',
    ];

    private function loadProcedencias()
    {
        return procedencia::where('salon_id',null)->orWhere('salon_id',1)->get();
    }
    public function mount()
    {
        $this->procedencias = $this->loadProcedencias();
        $this->lada = '+52';
        $this->cliente = new cliente; // hacemos que la propiedad cliente sea una instancia del modelo
    }
    public function render()
    {
        return view('livewire.encuesta-clientes')->layout('layouts.web-page');
    }
    public function setCalificacion($value)
    {
        $this->calificacion = $value; // Asignar el valor de calificación
    }

    private function eliminarRelacion($items)
    {
        // eliminar los deliveries asociados al cliente de la tabla deliveries
        foreach ($items as $item) {
            $item->delete();
        }
    }

    public function Store()
    {

        $posible_cliente = cliente::where(function($query) {
            // Agrupamos las condiciones de nombre y apellido
            $query->where(function($subQuery) {
                $subQuery->where('first_name', $this->cliente->first_name)
                            ->where('last_name', $this->cliente->last_name);
            })
            // Agrupamos las condiciones de teléfono y email
            ->orWhere(function($subQuery) {
                $subQuery->where('phone', $this->cliente->phone != null ? $this->lada . $this->cliente->phone : 0)
                            ->orWhere('phone', $this->cliente->phone != null ? $this->cliente->phone : 0)
                            ->orWhere('email', $this->cliente->email != null ? $this->cliente->email : '@');
            });
        })->first();
        if($posible_cliente!=null){
            $this->compararCliente($posible_cliente);

            $this->rules['cliente.phone'] = "nullable|min:10|max:15|unique:clientes,phone,{$posible_cliente->id}";
            $this->rules['cliente.email'] = "nullable|max:65|email|unique:clientes,email,{$posible_cliente->id}";
        }else{
            $this->rules['cliente.phone'] = "nullable|min:10|max:15|unique:clientes,phone";
            $this->rules['cliente.email'] = "nullable|max:65|email|unique:clientes,email";
        }
        $this->rules = [
            'cliente.first_name' => "required|min:3|max:35",
            'cliente.last_name' => "nullable|min:3|max:35",
            'cliente.email' => "nullable|max:65|email",
            'cliente.birth_date' => 'nullable',
            'cliente.postcode' => 'nullable',
            'cliente.description' => "nullable|min:7|max:100",
            'cliente.sexo' => 'nullable|in:masculino,femenino,noBinario',
            'cliente.procedencia_id' => 'nullable',
            'cliente.want_offers' => 'nullable',
            'cliente.want_custom_messages' => 'nullable',
            'qt1' => 'required',
            'qt2' => 'required',
            'qt3' => 'required',
            'qt4' => 'required',
        ];

        $this->validate($this->rules);
        try{
            $this->cliente->salon_id = 1;
            if($this->cliente->form_answered){
                $this->eliminarRelacion($this->cliente->respuestas);
            }else{
                $this->cliente->form_answered = 1;
            }
            $this->cliente->procedencia_id = $this->qt1;
            $this->cliente->want_offers = $this->qt4;
            $this->cliente->save();
    
            
            $respuesta1 = new respuesta;
            $respuesta1->cliente_id = $this->cliente->id;
            $respuesta1->eleccion = $this->qt1;
            $respuesta1->pregunta_id = 1;
            $respuesta1->save();
    
            foreach($this->qt2 as $servicio){
                $respuestas2 = new respuesta;
                $respuestas2->cliente_id = $this->cliente->id;
                $respuestas2->eleccion = $servicio;
                $respuestas2->pregunta_id = 2;
                $respuestas2->save();
            }
            
            $respuesta3 = new respuesta;
            $respuesta3->cliente_id = $this->cliente->id;
            $respuesta3->eleccion = $this->qt3;
            $respuesta3->pregunta_id = 3;
            $respuesta3->save();
            
            $respuesta4 = new respuesta;
            $respuesta4->cliente_id = $this->cliente->id;
            $respuesta4->eleccion = $this->qt4;
            $respuesta4->pregunta_id = 4;
            $respuesta4->save();
    
            if($this->calificacion !== null || $this->review !==null){
                $calificacion = new calificacion_cliente_empleado;
                $calificacion->puntaje = $this->calificacion; 
                $calificacion->cliente_id = $this->cliente->id; 
                $calificacion->comentario = $this->review; 
                $calificacion->save(); 
            }
    
            return redirect()->to('encuesta-contestada');

        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' => 'Algo salió mal :/']);
        }
    }
    private function compararCliente($posible_cliente){
        
        $oldCliente = $this->cliente;
        $this->cliente = $posible_cliente;

        $this->cliente->first_name = $oldCliente->first_name ?? $this->cliente->first_name;
        $this->cliente->last_name = $oldCliente->last_name ?? $this->cliente->last_name;
        $this->cliente->phone = $oldCliente->phone ??  $this->cliente->phone;
        $this->cliente->email = $oldCliente->email ?? $this->cliente->email;
        $this->cliente->birth_date = $oldCliente->birth_date ?? $this->cliente->birth_date;
        $this->cliente->postcode = $oldCliente->postcode ?? $this->cliente->postcode;
        $this->cliente->sexo = $oldCliente->sexo ?? $this->cliente->sexo;


    }
}
