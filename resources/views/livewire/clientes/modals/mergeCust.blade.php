<div wire:ignore.self id="modalMergeCust" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" >
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <!-- Contenido del modal-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" wire:click="endMerge">×</button>
                <h4 class="modal-title">Unión de Clientes</h4>
            </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            Elige la información definitiva que quieres almacenar del cliente. Lo que no se seleccione se perderá permanentemente.
                            <br>
                            @if(count($merge)==3 or count($merge)==2)
                            <div class="table-responsive">
                                <table class="table table-responsive-md table-hover  text-left">
                                    <thead class="thead-primary">
                                        <tr>
                                            <th style="background-color:transparent;color:#1D3557 !important"></th>
                                            @foreach($merge as $cust)
                                                <th style="background-color:transparent;color:#1D3557 !important">{{ $cust->first_name }} {{ $cust->last_name }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Nombre 
                                                @error('cliente.first_name') <span class="text-danger">*Corrige este campo* </span> @enderror 
                                            </td>
                                
                                            @foreach($merge as $cust)
                                                <td>
                                                    <input class="form-check-input" type="radio" name="first_name" wire:model.defer="cliente.first_name" value="{{ $cust->first_name }}" id="first_name{{$cust->id}}">
                                                    <label class="form-check-label" for="first_name{{$cust->id}}">{{ $cust->first_name }}</label>
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td>Apellido
                                                @error('cliente.last_name') <span class="text-danger">*Corrige este campo* </span> @enderror
                                            </td>
                                            @foreach($merge as $cust)
                                                <td>
                                                    <input class="form-check-input" type="radio" name="last_name" wire:model.defer="cliente.last_name" value="{{ $cust->last_name }}" id="last_name{{$cust->id}}">
                                                    <label class="form-check-label" for="last_name{{$cust->id}}">{{ $cust->last_name }}</label>
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td>Cumpleaños
                                                @error('cliente.birth_date') <span class="text-danger">*Corrige este campo* </span> @enderror
                                            </td>
                                            @foreach($merge as $cust)
                                                <td>
                                                    <input class="form-check-input" type="radio" name="birth_date" wire:model.defer="cliente.birth_date" value="{{ $cust->birth_date }}" id="birth_date{{$cust->id}}">
                                                    <label class="form-check-label" for="birth_date{{$cust->id}}">{{ $cust->birth_date }}</label>
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td>Email
                                                @error('cliente.email') <span class="text-danger">*Corrige este campo* </span> @enderror
                                            </td>
                                            @foreach($merge as $cust)
                                                <td>
                                                    <input class="form-check-input" type="radio" name="email" wire:model.defer="cliente.email" value="{{ $cust->email }}" id="email{{$cust->id}}">
                                                    <label class="form-check-label" for="email{{$cust->id}}">{{ $cust->email }}</label>
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td>Código postal
                                                @error('cliente.postcode') <span class="text-danger">*Corrige este campo* </span> @enderror
                                            </td>
                                            @foreach($merge as $cust)
                                                <td>
                                                    <input class="form-check-input" type="radio" name="postcode" wire:model.defer="cliente.postcode" value="{{ $cust->postcode }}" id="postcode{{$cust->id}}">
                                                    <label class="form-check-label" for="postcode{{$cust->id}}">{{ $cust->postcode }}</label>
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td>Teléfono
                                                @error('cliente.phone') <span class="text-danger">*Corrige este campo* </span> @enderror
                                            </td>
                                            @foreach($merge as $cust)
                                                <td>
                                                    <input class="form-check-input" type="radio" name="phone" wire:model.defer="cliente.phone" value="{{ $cust->phone }}" id="phone{{$cust->id}}">
                                                    <label class="form-check-label" for="phone{{$cust->id}}">{{ $cust->phone }}</label>
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td>Mensajes personalizados
                                                @error('cliente.want_custom_messages') <span class="text-danger">*Corrige este campo* </span> @enderror
                                            </td>
                                            @foreach($merge as $cust)
                                                <td>
                                                    <input class="form-check-input" type="radio" name="want_custom_messages" wire:model.defer="cliente.want_custom_messages" value="{{ $cust->want_custom_messages ? 1 : 0 }}" id="want_custom_messages{{$cust->id}}">
                                                    <label class="form-check-label" for="want_custom_messages{{$cust->id}}">{{ $cust->want_custom_messages ? 'Acepta' : 'No acepta' }}</label>
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td>Ofertas
                                                @error('cliente.want_offers') <span class="text-danger">*Corrige este campo* </span> @enderror
                                            </td>
                                            @foreach($merge as $cust)
                                                <td>
                                                    <input class="form-check-input" type="radio" name="want_offers" wire:model.defer="cliente.want_offers" value="{{ $cust->want_offers ? 1 : 0 }}" id="want_offers{{$cust->id}}">
                                                    <label class="form-check-label" for="want_offers{{$cust->id}}">{{ $cust->want_offers ? 'Acepta' : 'No acepta' }}</label>
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td>Sexo
                                                @error('cliente.sexo') <span class="text-danger">*Corrige este campo* </span> @enderror
                                            </td>
                                            @foreach($merge as $cust)
                                                <td>
                                                    <input class="form-check-input" type="radio" name="sexo" wire:model.defer="cliente.sexo" value="{{ $cust->sexo }}" id="sexo{{$cust->id}}">
                                                    <label class="form-check-label" for="sexo{{$cust->id}}">{{ $cust->sexo }}</label>
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td>Procedencia
                                                @error('cliente.procedencia') <span class="text-danger">*Corrige este campo* </span> @enderror
                                            </td>
                                            @foreach($merge as $cust)
                                                <td>
                                                    <input class="form-check-input" type="radio" name="procedencia" wire:model.defer="cliente.procedencia_id" value="{{ $cust->procedencia_id }}" id="procedencia{{$cust->id}}">
                                                    <label class="form-check-label" for="procedencia{{$cust->id}}">{{ $cust->procedencia()->name ?? '-' }}</label>
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td>Fecha de creación
                                                @error('cliente.created_at') <span class="text-danger">*Corrige este campo* </span> @enderror
                                            </td>
                                            @foreach($merge as $cust)
                                                <td>
                                                    <input class="form-check-input" type="radio" name="created_at" wire:model.defer="cliente.created_at" value="{{ $cust->created_at }}" id="created_at{{$cust->id}}">
                                                    <label class="form-check-label" for="created_at{{$cust->id}}">{{ $cust->created_at }}</label>
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td>Procedencia
                                                @error('cliente.procedencia') <span class="text-danger">*Corrige este campo* </span> @enderror
                                            </td>
                                            @foreach($merge as $cust)
                                                <td>
                                                    <input class="form-check-input" type="radio" name="procedencia" wire:model.defer="cliente.procedencia_id" value="{{ $cust->procedencia_id }}" id="procedencia{{$cust->id}}">
                                                    <label class="form-check-label" for="procedencia{{$cust->id}}">{{ $cust->procedencia()->name ?? '-' }}</label>
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td>Expediente
                                                @error('cliente.record') <span class="text-danger">*Corrige este campo* </span> @enderror
                                            </td>
                                            @foreach($merge as $cust)
                                                <td>
                                                    <input class="form-check-input" type="radio" name="record" wire:model.defer="cliente.record" value="{{ $cust->record }}" id="record{{$cust->id}}">
                                                    <div class="form-group" id="recordReadOnly{{$cust->id}}" for="record{{$cust->id}}"></div>
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td>Puntos acumulados
                                            </td>
                                                <td colspan="2">
                                                    <li>
                                                        <ul>
                                                            <input class="form-check-input" type="radio" name="puntaje" wire:model.defer="puntaje" value="{{ $sumPoints }}" id="puntaje">
                                                            <label class="form-check-label" for="puntaje">Sumar puntos: {{ number_format($sumPoints, 2, '.', ',') }}</label></ul>
                                                        <ul></ul>
                                                    @foreach($merge as $cust)
                                                        <ul>
                                                            <input class="form-check-input" type="radio" name="puntaje" wire:model.defer="puntaje" value="{{ $cust->tarjetaPuntos ? $cust->tarjetaPuntos->balance : 0 }}" id="puntaje{{$cust->id}}">
                                                            <label class="form-check-label" for="puntaje{{$cust->id}}">{{ $cust->tarjetaPuntos ? number_format($cust->tarjetaPuntos->balance, 2, '.', ',') : 0 }}</label>
                                                        </ul>
                                                    @endforeach
                                                    </li>
                                                </td>
                                            </td>
                                        </tr>
                                        @if($clientes_tarjeta>1)
                                        <tr>
                                            <td>Tarjeta de puntos</td>
                                            @foreach($merge as $cust)
                                                <td>
                                                    <input class="form-check-input" type="radio" name="barcode" wire:model.defer="barcode" value="{{ $barcode }}" id="barcode{{$cust->id}}">
                                                    <label class="form-check-label" for="barcode{{$cust->id}}">{{ $cust->first_name }} {{ $cust->last_name }}</label>
                                                </td>
                                            @endforeach
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            <div class="modal-footer">
                <button class="btn input-group-text" wire:click="mergeCustomers">Unir</button>
                <button type="button" class="btn btn-dark" data-dismiss="modal" wire:click="endMerge">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@include('livewire.clientes.modals.mergeStyles')