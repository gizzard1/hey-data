<div>
    <div class="card-body">
        <div class="table-responsive" style="height: auto !important;">

            <table id="cart-view" class="table table-striped table-responsive-sm">
                <thead>
                    <tr class="text-center">
                        <!-- <th width="96"><i class="las la-download"></i>Uso</th> -->
                        <th colspan="2">Horario</th>
                        <th width="280">Servicio</th>
                        <th width="260">Vendedor</th>
                        <th width="70">Color</th>
                    </tr>
                </thead>
                @if(isset($cartS)&&count($cartS)>0)
                <tbody style="height:1rem">
                    @foreach($cartS as $item)
                        <tr class="text-center">
                            <td>
                                <input list="times-start" name="time-start" type="time" value="{{ $item['start'] }}" wire:change.debounce.350ms="$emit('changeStartDuration','{{ $item['id'] }}', $event.target.value)" style="text-align:center;background-color: transparent; border-color: transparent;">
                                <datalist id="times-start">
                                    @foreach($times as $time)
                                        <option value="{{ $time }}">{{ $time }}</option>
                                    @endforeach
                                </datalist>
                            </td>
                            <td>
                                <input list="times-end" name="time-end" type="time" value="{{ $item['end'] }}" wire:change.debounce.350ms="$emit('changeEndDuration','{{ $item['id'] }}', $event.target.value)" style="text-align:center;background-color: transparent; border-color: transparent;">
                                <datalist id="times-end">
                                    @foreach($times as $time)
                                        <option value="{{ $time }}">{{ $time }}</option>
                                    @endforeach
                                </datalist>
                            </td>
                            <td>{{ $item['name'] }}</td>
                            <td>
                            <select wire:change.prevent="$emit('changeEmpleado','servicio','{{ $item['id'] }}', $event.target.value)" style="background-color: transparent;border-color:transparent;">
                                @if(!isset($item['vendedor']))
                                    <option value="null">Seleccione un empleado</option>
                                @endif
                                @foreach($empleados as $empleado)
                                    <option style="text-align: center;" value="{{ $empleado->id }}" {{ $item['vendedor'] == $empleado->id ? 'selected' : '' }}>
                                        {{ $empleado->first_name }}
                                    </option>
                                @endforeach
                                </select>
                            </td>
                            <td>
                            @include('livewire.calendar.dropdown.color-cart')

                            </td>
                        </tr>
                    @endforeach
                </tbody>
                @endif
            </table>
        </div>
    </div>
</div>
<style>
    .table{
        max-width: none !important;
    }
</style>