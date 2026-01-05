<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class ErroresExport implements FromArray, WithTitle
{
    protected $datos;
    protected $titulo;

    public function __construct(array $datos, string $titulo)
    {
        $this->datos = $datos;
        $this->titulo = $titulo;
    }

    public function array(): array
    {
        return $this->datos;
    }

    public function title(): string
    {
        return $this->titulo;
    }
}
