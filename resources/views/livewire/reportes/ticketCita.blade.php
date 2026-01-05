<div class="ticket centrado">
    <h2 style="font-weight:normal;margin-bottom:1rem">{{ $this->info->salon->name }}</h2>
    <br><td class="producto">Ticket de venta: #{{ $this->info->id }}</td>
    <br><td class="producto">Cliente: {{ $this->info->customer->first_name }}</td>
    <br><td class="producto">Recepción: {{ $this->info->user->name }}</td>
    <br>
    <table>
        <thead>
            <tr class="centrado" style="
        border-top: 1px solid black;
        border-collapse: collapse;
        margin: 0 auto;">
                <th class="cantidad">Cantidad </th>
                <th class="producto">Concepto </th>
                <th class="precio">Monto</th>
            </tr>
        </thead>
        <tbody>
            @foreach($this->info->details as $item)
                <tr>
                    <td class="cantidad">1</td>
                    <td class="producto">{{ $item->servicio->name }}</td>
                    <td class="precio">${{ number_format($item->disccount_price>0 && $item->disccount_price<$item->current_price ? $item->disccount_price : $item->current_price,2,'.',',') }}</td>
                </tr>
                @if($item->discount_qty>0)
                <tr>
                    <td class="cantidad"><strong></strong></td>
                    <td class="producto"><strong>Descuento</strong></td>
                    <td class="precio">@if($item->discount_type=='Porcentaje') {{ number_format($item->discount_qty,2,'.') }}% @elseif($item->discount_type=='Cantidad') ${{ number_format($item->discount_qty,2,'.',',') }} @endif</td>
                </tr>
                @endif
            @endforeach
            
            @if(isset($this->info->details_product))
            @foreach($this->info->details_product as $item)
                <tr>
                    <td class="cantidad">{{ $item->quantity }}</td>
                    <td class="producto">{{ $item->product->name }}</td>
                    <td class="precio">${{ number_format($item->disccount_price>0 && $item->disccount_price<$item->current_price ? $item->disccount_price*$item->quantity : $item->current_price*$item->quantity,2,'.',',') }}</td>
                </tr>
                @if($item->discount_qty>0)
                <tr>
                    <td class="cantidad"><strong></strong></td>
                    <td class="producto"><strong>Descuento</strong></td>
                    <td class="precio">@if($item->discount_type=='Porcentaje') {{ number_format($item->discount_qty,2,'.') }}% @elseif($item->discount_type=='Cantidad') ${{ number_format($item->discount_qty,2,'.',',') }} @endif</td>
                </tr>
                @endif
            @endforeach
            @endif
            <tr style="
        border-top: 1px solid black;
        border-collapse: collapse;
        margin: 0 auto;">
                <td class="cantidad"></td>
                <td class="producto"><strong>Total</strong></td>
                <td class="precio">${{ number_format($this->total,2,'.',',') }}</td> 
            </tr>
            <tr>
                <td class="cantidad"></td>
                <td class="producto"><strong>Cambio</strong></td>
                <td class="precio">${{ number_format($this->cambio,2,'.',',') }}</td> 
            </tr>
            
    
