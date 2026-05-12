{{-- Servicios más solicitados --}}

<div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Servicios más solicitados</h3>
            <div class="btn-group toggle-btns">
                <button class="btn btn-outline-secondary btn-sm"
                    wire:click="$set('orderRankingTable', 'service')">Servicio</button>
                <button class="btn btn-outline-secondary btn-sm"
                    wire:click="$set('orderRankingTable', 'category')">Categoría</button>
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
                                    <th wire:click="$set('orderRankingTable', 'times_consumed')">Veces
                                        consumido</th>
                                    <th wire:click="$set('orderRankingTable', 'total')">Total</th>
                                </tr>
                            </thead>
                            <tbody id="tableServices"></tbody>
                            @foreach($orderRankingTable == 'service' ?
                            $customerSelected->top10ServicesConsumed()->get() :
                            $customerSelected->top10ServicesCategoriesConsumed()->get() as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="nombre-column">{{ $orderRankingTable == 'service' ?
                                    ($item->servicio->name ?? 'Servicio Desconocido') :
                                    ($item->category_name ?? 'Sin Categoría') }}</td>
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