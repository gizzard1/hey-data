@if(Auth::user()->role!=='estilista')
<div class="card">
    <div class="card-header ">
        <div class="d-flex">
            <div class="separator"></div>
            <div class="mr-auto">
                <h4 class="card-title"><a wire:click="regresarListado">Clientes</a>/<a wire:click="viewCust({{ $customerSelected->id }})">{{ $customerSelected->first_name }} {{ $customerSelected->last_name }}</a>/Informe</h4>
                <p class="fs-14 mb-0"> Informe</p>
            </div>
        </div>
        @include('livewire.clientes.header.periods')
    </div>

            
    <div class="card-body cuerpo-informe" style="margin-left: 2rem;margin-right:2rem;">
    
        
    
    @if($productsAverage>0 || $servicesAverage>0)
    
    <div class="card">
        <div class="card-header">
            <h4 class="text-center">Compra de productos vs servicios (%)</h4> 
        </div>
        <div class="card-body">
            <div class="ct-chart-PS ct-perfect-fourth" style="max-height:18rem;text-align:center;margin-top:2rem"></div>
        </div>
    @endif
        
        
    <div class="card-header">
    <h4 class="text-center">Top 10 productos vendidos</h4> 
    </div>
        
        <div class="card-body">
            <table class="table table-responsive-md   text-center" style="margin-top:2rem; margin-bottom:3rem;width:-webkit-fill-available">
                <thead>
                    <tr style="color:#9D1466;font-weight:bold">
                        <td></td>
                        <td>Descripción</td>
                        <td>Cantidad</td>
                        <td>Precio</td>
                        <td>Total</td>
                    </tr>
                </thead>
                <tbody>
                    
                    @if ($dataServices && count($dataServices) > 0)
                        @forelse($dataProducts as $index => $product)
                            <tr>
                                <td>{{ $index+1 }}</td>
                                <td>{{ $product->name ?? $product['name'] }}</td>
                                <td>{{ number_format($product->total_qty ?? 0,2,'.',',') }}</td>
                                <td>${{ number_format($product->avg_price ?? 0,2,'.',',') }}</td>
                                <td>${{ number_format($product->total_price ?? 0,2,'.',',') }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5">No hay datos para mostrar</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="card-header">
            <h4 class="text-center">Top 10 servicios vendidos</h4> 
        </div>
        <div class="card-body">
            <table class="table table-responsive-md   text-center" style="margin-top:2rem; margin-bottom:3rem;width:-webkit-fill-available">
                <thead>
                    <tr style="color:#9D1466;font-weight:bold">
                        <td></td>
                        <td>Descripción</td>
                        <td>Cantidad</td>
                        <td>Precio</td>
                        <td>Total</td>
                    </tr>
                </thead>
                <tbody>
                    @if ($dataServices && count($dataServices) > 0)
                        @foreach ($dataServices as $index => $service)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $service->name ?? $service['name'] }}</td>
                                <td>{{ number_format($service->total_assignments ?? 0, 2, '.', ',') }}</td>
                                <td>${{ number_format($service->avg_price ?? 0, 2, '.', ',') }}</td>
                                <td>${{ number_format($service->total_price ?? 0, 2, '.', ',') }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5">No hay datos para mostrar</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        </div>
    </div>
</div>
<style>
    
.tags-container {
    justify-content: center;
}
/* Estilos para el gráfico ct-chart-PS */
.ct-chart-PS .ct-label {
    color: white;
    fill: white !important;
}

.ct-chart-PS .ct-series.ct-series-a .ct-slice-donut {
    stroke: #BFD9C7;
    stroke-linecap: round;
}

.ct-chart-PS .ct-series.ct-series-b .ct-slice-donut {
    stroke: #F6D9EB;
    stroke-linecap: round;
}

.ct-chart-PS {
    fill: transparent; /* Asegúrate de que no haya fondo aplicado */
}

/* Estilos para el gráfico ct-chart-R */
.ct-chart-R .ct-label {
    color: white;    
    fill: white !important;
}

.ct-chart-R .ct-series.ct-series-a .ct-slice-donut {
    stroke: #BFD9C7;
    stroke-linecap: round;
}

.ct-chart-R .ct-series.ct-series-b .ct-slice-donut {
    stroke: #F6D9EB;
    stroke-linecap: round;
}

.ct-chart-R {
    fill: transparent; /* Asegúrate de que no haya fondo aplicado */
}

.details:hover{
    text-decoration: underline !important;
    color:#9D1466 !important;
    cursor:pointer !important;
}
</style>
@else
@include('livewire.sinPermisos')
@endif
