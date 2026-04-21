<a class="dropdown-item" style="padding:0 1rem!important; align-content:center" data-toggle="dropdown" onclick="toggleDropdown(event,this)">
    <div 
        class="color-box m-auto" 
        style="background-color: {{ $color }}; width: 24px; height: 24px; border: 1px solid #ccc; cursor: pointer;" >
    </div>
</a>
<div class="dropdown-menu p-0">
    @php
        $presetColors = ['#6f42c1', '#e83e8c', '#e63946', '#1d3557', '#278d46', '#3A82EF', '#FFAB2D'];
    @endphp

    @foreach($presetColors as $color)
        <a class="dropdown-item p-1" wire:click.prevent="updateColor('{{ $color }}')">
            <div 
                class="color-box m-auto" 
                style="background-color: {{ $color }}; width: 24px; height: 24px; border: 1px solid #ccc; cursor: pointer;" >
            </div>
        </a>
    @endforeach

    <a class="dropdown-item p-1">
        <input type="color" class="color-selector color-box m-auto" value="{{ $color }}" wire:change.prevent="updateColor($event.target.value)">
    </a>
</div>
<style>
.dropdown-menu.show {
    display: flex;
    position: sticky;
}
.dropdown-item {
    width: auto !important;
}
</style>