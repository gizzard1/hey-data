
<div class="col-sm-12 col-md-3"> 
    <div class="card h-auto">
        <div class="card-header">
            <span class="h3" style="color:#60060F;margin:auto">Total</span>
        </div>
        <div class="card-body p-4">
            <div class="flex-wrap">
                <span>Items:</span>
                <span class="float-right ">
                    {{ $itemsCart }}
                </span>
            </div>
            <div class="flex-wrap">
                <span>Neto:</span>
                <span class="float-right ">
                    ${{ number_format(floatval($subtotalCart), 2, '.', ',') }}
                </span>
            </div>
            <div class="flex-wrap">
                <span>Impuestos:</span>
                <span class="float-right ">
                    ${{ number_format($taxCart, 2, '.', ',') }}
                </span>
            </div>
            <hr>
            <div class="flex-wrap">
                <span><b>Total</b></span>
                <span class="float-right ">
                    <b>${{ number_format($totalCart, 2, '.', ',') }}</b>
                </span>
            </div>
            <div class="flex-wrap">
                    @if($totalCartBase>0&&$totalCart!=$totalCartBase)<small class="line-t float-right">${{ number_format($totalCartBase,2,'.',',') }}</small>
                    @endif
            </div>
            <hr>
            @if(isset($propinas) && count($propinas)>0)
            <div class="flex-wrap">
                <span>Total con propina</span>
                <span class="float-right ">
                    ${{ number_format($totalCart + $propinasRecibidas, 2, '.', ',') }}
                </span>
            </div>
            @endif
            @if(isset($total_disccount) && $total_disccount>0)
            <div class="flex-wrap">
                <span>Ahorro</span>
                <span class="float-right ">
                    ${{ number_format($total_disccount, 2, '.', ',') }}
                </span>
            </div>
            @endif
            <div class="flex-wrap">
                <span>Puntos</span>
                <span class="float-right ">
                    {{ number_format($generated_points, 2, '.', ',') }}
                </span>
            </div>
        </div>
    </div>
</div>