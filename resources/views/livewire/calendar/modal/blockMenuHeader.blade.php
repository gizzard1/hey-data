
<div class="card-header flex-wrap">
    <div class="d-flex">
        <div class="separator" style="background-color:#E2BBB4"></div>
        <div class="mr-auto">
            <h4>Bloqueo ({{ $minutes_qty }} min.)</h4>
            <a id="fechaCita" type="button" wire:ignore.self wire:model="currentDateC" class="flatpickr" wire:change="dateSelected"><h5 class="fs-14 mb-0" style="justify-content: space-between;" >{{ $currentDate }}</h5>
        </a>
        </div>
    </div>
</div>