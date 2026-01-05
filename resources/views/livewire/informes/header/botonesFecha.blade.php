<div style="display: inline-flex;cursor:pointer;" class="flatpickrInd" >
    <div id="currentDate">
    {{ $currentDate }}
    </div>
    <div id="currentDateEnd">
    @if($is_interval) <div>- {{ $currentDateEnd }}</div>@endif
    </div>

</div>
<div style="width:fit-content;display:inline">
    <i type="button" class="las la-calendar dropdown-toggle" data-toggle="dropdown"></i>
    <div class="dropdown-menu">
        <a style="cursor: pointer;" class="dropdown-item dropright flatpickr" data-toggle="dropdown">Elegir periodo</a>
        <a style="cursor: pointer;" class="dropdown-item processing-info" wire:click="returnToday">Hoy</a>
        <a style="cursor: pointer;" class="dropdown-item processing-info" wire:click="returnYesterday">Ayer</a>
        <a style="cursor: pointer;" class="dropdown-item processing-info" wire:click="setWeek">Semanal</a>
        <a style="cursor: pointer;" class="dropdown-item processing-info" wire:click="setMonth">Mensual</a>
        <a style="cursor: pointer;" class="dropdown-item processing-info" wire:click="setYear">Anual</a>
    </div>
</div>