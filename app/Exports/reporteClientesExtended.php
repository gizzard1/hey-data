<?php

namespace App\Exports;

use App\Models\Empleado;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class reporteClientesExtended implements WithMultipleSheets
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public Collection $clientes;
    public Collection $filtros;

    public function __construct($clientes, $filtros)
    {
        $this->clientes = collect($clientes);
        $this->filtros = $this->recuperarEmpleados($filtros);
    }

    public function sheets(): array
    {
        return [
            new ReporteClientesExtendedResumenSheet($this->clientes, $this->filtros),
            new ReporteClientesExtendedCitasSheet($this->clientes),
            new ReporteClientesExtendedComprasSheet($this->clientes),
        ];
    }

    private function recuperarEmpleados($filtros)
    {
        $filtros = collect($filtros);

        foreach ($filtros as $filtro) {
            if ($filtro['type'] == 'atencion') {
                $empleado = Empleado::find($filtro['query']);
                $name = $empleado ? $empleado->first_name : 'N/A';
                $uid = $filtro['uid'];

                $oldItem  = $filtros->where('uid', $uid)->first();
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

class ReporteClientesExtendedResumenSheet implements FromView, ShouldAutoSize, WithColumnWidths, WithColumnFormatting, WithTitle
{
    private Collection $clientes;
    private Collection $filtros;

    public function __construct(Collection $clientes, Collection $filtros)
    {
        $this->clientes = $clientes;
        $this->filtros = $filtros;
    }

    public function view(): View
    {
        return view('livewire.clientes.exportables.clientes_extended', [
            'clientes' => $this->clientes,
            'filtros' => $this->filtros,
        ]);
    }

    public function title(): string
    {
        return 'Resumen';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_NUMBER,
        ];
    }
}

class ReporteClientesExtendedCitasSheet implements FromView, ShouldAutoSize, WithTitle
{
    private Collection $clientes;

    public function __construct(Collection $clientes)
    {
        $this->clientes = $clientes;
    }

    public function view(): View
    {
        return view('livewire.clientes.exportables.clientes_extended_citas', [
            'clientes' => $this->clientes,
        ]);
    }

    public function title(): string
    {
        return 'Citas';
    }
}

class ReporteClientesExtendedComprasSheet implements FromView, ShouldAutoSize, WithTitle
{
    private Collection $clientes;

    public function __construct(Collection $clientes)
    {
        $this->clientes = $clientes;
    }

    public function view(): View
    {
        return view('livewire.clientes.exportables.clientes_extended_compras', [
            'clientes' => $this->clientes,
        ]);
    }

    public function title(): string
    {
        return 'Compras';
    }
}
