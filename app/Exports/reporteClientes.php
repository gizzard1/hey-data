<?php

namespace App\Exports;

use App\Models\Empleado;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class reporteClientes implements FromView, ShouldAutoSize, WithColumnWidths, WithColumnFormatting
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public $clientes=[],$filtros=[];

    public function __construct($clientes,$filtros) {
        $this->clientes = $clientes;
        $this->filtros = $this->recuperarEmpleados($filtros);
    }

    public function view(): View
    {
        return view('livewire.clientes.exportables.clientes',[
            'clientes' => $this->clientes,'filtros' => $this->filtros
        ]);
    }
    public function columnWidths(): array{
        return[
            'A' => 25,
        ];
    }
    private function recuperarEmpleados($filtros)
    {
        foreach($filtros as $filtro){
            if($filtro['type']=='atencion'){
                $name = Empleado::find($filtro['query'])->first_name;
                $uid = $filtro['uid'];
                
                $oldItem  = $filtros->where('uid',$uid)->first();
                $newItem = $oldItem;
                $newItem['query'] = $name;
                $filtros  = $filtros->reject(function ($filtro) use ($uid) {
                    return  $filtro['uid'] === $uid;
                });
                $filtros->push(Arr::add($newItem, null, null));
            }
        }
        return $filtros;
    }
    
    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_NUMBER, // Aplica formato numérico estándar a la columna B
        ];
    }
}
