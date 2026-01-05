@if(Auth::user()->salon->simulador)

<div class="form-group m-auto d-flex " style="background-color: transparent;border-color:transparent">
    <input type="date" class="form-control" wire:model="customDate">
    <div class="input-group-append">
        <a class="input-group-text save" style="color:white" wire:click.prevent="returnToday">
            Hoy
        </a>
    </div>
</div>
@endif