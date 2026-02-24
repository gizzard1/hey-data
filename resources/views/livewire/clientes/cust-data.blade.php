<div class="card">
    <div class="card-header ">
        <div class="d-flex">
            <div class="separator"></div>
            <div class="mr-auto">
                <h4 class="card-title mb-1"><a wire:click="regresarListado">Clientes</a>/{{
                    $customerSelected->first_name }} {{ $customerSelected->last_name }} </h4>
                <p class="fs-14 mb-0"> Expediente Registrado</p>
            </div>
        </div>

        <div class="rating-widget d-flex">
            <!-- Rating Stars Box -->
            <div class="rating-stars">
                <ul id="stars">
                    <li class="star {{ $calificacion>=1 ? 'selected' : '' }}">
                        <i class="fa fa-star fa-fw"></i>
                    </li>
                    <li class="star {{ $calificacion>=2 ? 'selected' : '' }}">
                        <i class="fa fa-star fa-fw"></i>
                    </li>
                    <li class="star {{ $calificacion>=3 ? 'selected' : '' }}">
                        <i class="fa fa-star fa-fw"></i>
                    </li>
                    <li class="star {{ $calificacion>=4 ? 'selected' : '' }}">
                        <i class="fa fa-star fa-fw"></i>
                    </li>
                    <li class="star {{ $calificacion>=5 ? 'selected' : '' }}">
                        <i class="fa fa-star fa-fw"></i>
                    </li>
                </ul>
            </div>
            <h3 class="card-title">Calificación: {{ number_format($calificacion ?? 0,2,'.') }}</h3>
        </div>


        <div class="d-flex" style="column-gap:2rem">
            <button class="btn btn-sm" style="color: white; background-color:#1D3557" data-toggle="modal"
                data-target="#modalCategories">Añadir al grupo</button>
            <button class="btn-sm btn-danger input-group-text" onclick="confirmDelete()">Eliminar</button>
            <button class="btn-sm btn" wire:click.prevent="Edit">Editar</button>
        </div>
    </div>


    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Datos del cliente</h3>
                    </div>
                    <div class="card-body card-body-data">
                        <div class="row">
                            <div class="col-md-6">
                                <p><svg xmlns="http://www.w3.org/2000/svg"
                                        class="icon icon-tabler icon-tabler-device-mobile" width="25" height="25"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path
                                            d="M6 5a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2v-14z" />
                                        <path d="M11 4h2" />
                                        <path d="M12 17v.01" />
                                    </svg> {{ $customerSelected->phone ?? 'N/A' }}</p>
                                <p><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-mail"
                                        width="25" height="25" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path
                                            d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" />
                                        <path d="M3 7l9 6l9 -6" />
                                    </svg> {{ $customerSelected->email ?? 'N/A' }}</p>

                                <p><svg xmlns="http://www.w3.org/2000/svg"
                                        class="icon icon-tabler icon-tabler-gender-female" width="25" height="25"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12 9m-5 0a5 5 0 1 0 10 0a5 5 0 1 0 -10 0" />
                                        <path d="M12 14v7" />
                                        <path d="M9 18h6" />
                                    </svg> <small>Sexo:</small> {{ $customerSelected->sexo ?? 'N/A' }}</p>

                                <p><svg xmlns="http://www.w3.org/2000/svg"
                                        class="icon icon-tabler icon-tabler-brand-whatsapp" width="25" height="25"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 21l1.65 -3.8a9 9 0 1 1 3.4 2.9l-5.05 .9" />
                                        <path
                                            d="M9 10a.5 .5 0 0 0 1 0v-1a.5 .5 0 0 0 -1 0v1a5 5 0 0 0 5 5h1a.5 .5 0 0 0 0 -1h-1a.5 .5 0 0 0 0 1" />
                                    </svg> <small>Mensajes personalizados:</small>
                                    <input type="checkbox" disabled @if($customerSelected->want_custom_messages) checked
                                    @endif>
                                </p>

                                <p><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-receipt-tax"
                                        width="25" height="25" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M9 14l6 -6" />
                                        <circle cx="9.5" cy="8.5" r=".5" fill="currentColor" />
                                        <circle cx="14.5" cy="13.5" r=".5" fill="currentColor" />
                                        <path
                                            d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-2 -2l-2 2l-2 -2l-3 2" />
                                    </svg> <small>Ofertas:</small>
                                    <input type="checkbox" disabled @if($customerSelected->want_offers) checked @endif>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-cake"
                                        width="25" height="25" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 20h18v-8a3 3 0 0 0 -3 -3h-12a3 3 0 0 0 -3 3v8z" />
                                        <path
                                            d="M3 14.803c.312 .135 .654 .204 1 .197a2.4 2.4 0 0 0 2 -1a2.4 2.4 0 0 1 2 -1a2.4 2.4 0 0 1 2 1a2.4 2.4 0 0 0 2 1a2.4 2.4 0 0 0 2 -1a2.4 2.4 0 0 1 2 -1a2.4 2.4 0 0 1 2 1a2.4 2.4 0 0 0 2 1c.35 .007 .692 -.062 1 -.197" />
                                        <path d="M12 4l1.465 1.638a2 2 0 1 1 -3.015 .099l1.55 -1.737z" />
                                    </svg> {{ $customerSelected->edad!=null && $customerSelected->edad>0 ?
                                    $customerSelected->edad . ' años' : '' }} {{ $customerSelected->birth_date ? '(' .
                                    $customerSelected->birth_date . ')' : 'N/A' }}</p>
                                @if(isset($customerSelected->tarjetaPuntos))
                                <a wire:click.prevent="editCard('{{ $customerSelected->tarjetaPuntos->intern_barcode }}')">
                                    <p><svg xmlns="http://www.w3.org/2000/svg"
                                            class="icon icon-tabler icon-tabler-heart-dollar" width="25" height="25"
                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M13 19l-1 1l-7.5 -7.428a5 5 0 1 1 7.5 -6.566a5 5 0 0 1 8.785 4.254" />
                                            <path d="M21 15h-2.5a1.5 1.5 0 0 0 0 3h1a1.5 1.5 0 0 1 0 3h-2.5" />
                                            <path d="M19 21v1m0 -8v1" />
                                        </svg> {{ isset($customerSelected->tarjetaPuntos) ?
                                        ($customerSelected->tarjetaPuntos->balance!=null ?
                                        $customerSelected->tarjetaPuntos->balance : 0 ). ' pts.' : 'Tarjeta no activa' }}
                                    </p>
                                </a>
                                @else
                                <p><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-heart-dollar"
                                        width="25" height="25" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M13 19l-1 1l-7.5 -7.428a5 5 0 1 1 7.5 -6.566a5 5 0 0 1 8.785 4.254" />
                                        <path d="M21 15h-2.5a1.5 1.5 0 0 0 0 3h1a1.5 1.5 0 0 1 0 3h-2.5" />
                                        <path d="M19 21v1m0 -8v1" />
                                    </svg> {{ isset($customerSelected->tarjetaPuntos) ?
                                    ($customerSelected->tarjetaPuntos->balance!=null ?
                                    $customerSelected->tarjetaPuntos->balance
                                    : 0 ). ' pts.' : 'Tarjeta no activa' }}</p>
                                @endif
                                <p><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user-pin"
                                        width="25" height="25" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                        <path d="M6 21v-2a4 4 0 0 1 4 -4h2.5" />
                                        <path
                                            d="M21.121 20.121a3 3 0 1 0 -4.242 0c.418 .419 1.125 1.045 2.121 1.879c1.051 -.89 1.759 -1.516 2.121 -1.879z" />
                                        <path d="M19 18v.01" />
                                    </svg> <small>Procedencia:</small> {{ $customerSelected->procedencia->name ?? 'N/A' }}
                                </p>

                                <a style="color:black"
                                    href="https://www.google.com/maps/search/?api=1&query={{ $customerSelected->postcode }}">
                                    <p><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-map-2"
                                            width="25" height="25" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50"
                                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M12 18.5l-3 -1.5l-6 3v-13l6 -3l6 3l6 -3v7.5" />
                                            <path d="M9 4v13" />
                                            <path d="M15 7v5.5" />
                                            <path
                                                d="M21.121 20.121a3 3 0 1 0 -4.242 0c.418 .419 1.125 1.045 2.121 1.879c1.051 -.89 1.759 -1.516 2.121 -1.879z" />
                                            <path d="M19 18v.01" />
                                        </svg> <small>Código postal:</small> {{ $customerSelected->postcode ?? 'N/A' }}</p>
                                </a>

                                <p><svg xmlns="http://www.w3.org/2000/svg"
                                        class="icon icon-tabler icon-tabler-calendar-month" width="25" height="25"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path
                                            d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                                        <path d="M16 3v4" />
                                        <path d="M8 3v4" />
                                        <path d="M4 11h16" />
                                        <path d="M7 14h.013" />
                                        <path d="M10.01 14h.005" />
                                        <path d="M13.01 14h.005" />
                                        <path d="M16.015 14h.005" />
                                        <path d="M13.015 17h.005" />
                                        <path d="M7.01 17h.005" />
                                        <path d="M10.01 17h.005" />
                                    </svg> <small>Creado el:</small> {{ $customerSelected->created_at ?? 'N/A' }}</p>
                            </div>
                        </div>
                        
                        <div class="tags-container">
                            @if(count($customerSelected->categorias)>0)
                            @foreach($customerSelected->categorias as $categoria)
                            <div class="tag">
                                <span class="tag-name">{{ $categoria->name }}</span>
                                <input type="button" value="x" class="remove-tag" wire:click="unsetCat('{{ $categoria->id }}')">
                            </div>
                            @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Servicios más solicitados --}}
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Servicios más solicitados</h3>
                        <div class="btn-group toggle-btns">
                            <button class="btn btn-outline-secondary btn-sm" wire:click="$set('orderRankingTable', 'service')">Servicio</button>
                            <button class="btn btn-outline-secondary btn-sm" wire:click="$set('orderRankingTable', 'category')">Categoría</button>
                        </div>
                    </div>
                    <div class="card-body card-body-data">
                        <div class="row">
                            <div class="col-md-12 d-flex flex-col">
                                <div class="ranking-table-container">
                                    <table class="table table-fixed-sm">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nombre</th>
                                            <th wire:click="$set('orderRankingTable', 'times_consumed')">Veces consumido</th>
                                            <th wire:click="$set('orderRankingTable', 'total')">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableServices"></tbody>
                                    @foreach($orderRankingTable == 'service' ? $customerSelected->top10ServicesConsumed($orderRankingTable)->get() : $customerSelected->top10ServicesCategoriesConsumed($orderRankingTable)->get() as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td class="nombre-column">{{ $orderRankingTable == 'service' ? ($item->servicio->name ?? 'Servicio Desconocido') : ($item->category_name ?? 'Sin Categoría') }}</td>
                                            <td>{{ $item->times_consumed }}</td>
                                            <td>${{ number_format($item->total_spent,2) }}</td>
                                        </tr>
                                    @endforeach
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Visitas finalizadas: {{
                            count($customerSelected->citas->where('status','Pagada')) }}</h3>
                            
                        <a href="{{ route('historico-cliente',['search'=>$customerSelected->id,'pestaña'=>3]) }}">Ver
                            más</a>
                    </div>
                    @if(count($customerSelected->citas->where('status','Pagada'))==0)
                    <div class="card-body">
                        El cliente no tiene visitas finalizadas
                    </div>
                    @endif
                    <div class="card-body card-body-data">
                        <ul class="list-group">
                            @foreach($customerSelected->citas->where('status','Pagada')->take(3) as $cita)
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-10 d-flex flex-col">
                                        <div>
                                            @foreach($cita->details as $detail)
                                            {{ $detail->servicio!=null ? $detail->servicio->name : 'Servicio
                                            Desconocido' }};
                                            @endforeach
                                        </div>
                                        <div>
                                            <small>{{ $cita->start . ' ' }}
                                                @foreach($cita->details as $detail)
                                                {{ $detail->empleado->first_name }};
                                                @endforeach
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-2 d-flex flex-col">
                                        <div>
                                            <a
                                                href="{{ route('citas', ['cita_id' => $cita->id,'pestaña' => 1,'action'=>2]) }}">Ver</a>
                                        </div>
                                        <div>
                                            <small>${{ $cita->total }}</small>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Visitas programadas: {{
                            count($customerSelected->citas->where('status','Agendada')) }}</h3>
                            
                        <a href="{{ route('historico-cliente',['search'=>$customerSelected->id,'pestaña'=>5]) }}">Ver
                            más</a>
                    </div>
                    @if(count($customerSelected->citas->where('status','Agendada'))==0)
                    <div class="card-body">
                        El cliente no tiene visitas programadas
                    </div>
                    @endif
                    <div class="card-body card-body-data">
                        <ul class="list-group">
                            @foreach($customerSelected->citas->where('status','Agendada')->take(3) as $cita)
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-10 d-flex flex-col">
                                        <div>
                                            @foreach($cita->details as $detail)
                                            {{ $detail->servicio!=null ? $detail->servicio->name : 'Servicio
                                            Desconocido' }};
                                            @endforeach
                                        </div>
                                        <div>
                                            <small>{{ $cita->start . ' ' }}
                                                @foreach($cita->details as $detail)
                                                {{ $detail->empleado->first_name }};
                                                @endforeach
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-2 d-flex flex-col">
                                        <div>
                                            <a
                                                href="{{ route('citas', ['cita_id' => $cita->id,'pestaña' => 1,'action'=>2]) }}">Ver</a>
                                        </div>
                                        <div>
                                            <small>${{ $cita->total }}</small>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card-body-data {
        padding-bottom: 3rem;
    }

    .col-md-6-info {
        padding-right: 3rem;
    }

    .rating-widget {
        flex-direction: column;
        align-items: anchor-center;
    }

    .flex-col {
        flex-direction: column;
    }
    .table tbody tr td {
        white-space: break-spaces;
        max-width: 22dvh;
    }
</style>