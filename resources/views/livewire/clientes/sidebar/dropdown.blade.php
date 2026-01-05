<div class="d-flex mb-3"  data-toggle="dropdown">
    <input type="button" class="form-control" value="{{ $filtro['type_range'] }}">
</div>
<div class="dropdown-menu">
    <a class="dropdown-item" onclick="loadFlat('{{ $filtro['uid'] }}')" data-toggle="dropdown">Elegir periodo</a>
    <div class="dropdown-menu">
        <div class="dropdown-item flatpickr" style="display: none;"></div>
    </div>
    <a class="dropdown-item" wire:click="deleteRange('{{ $filtro['uid'] }}')">Sin fecha</a>
    <a class="dropdown-item" wire:click="updateToday('{{$filtro['uid']}}')">Hoy</a>
    <a class="dropdown-item" wire:click="updateYesterday('{{$filtro['uid']}}')">Ayer</a>
    <a class="dropdown-item" wire:click="updateWeek('{{$filtro['uid']}}')">Semanal</a>
    <a class="dropdown-item" wire:click="updateMonth('{{$filtro['uid']}}')">Mensual</a>
    @if($filtro['type']!=='birth_date')
    <a class="dropdown-item" wire:click="updateYear('{{$filtro['uid']}}')">Anual</a>
    @endif
</div>