
@if(Auth::user()->role !== 'estilista')
<div>
    <div class="row" id="cardTable">
        @if($pestaña == 1)
        <div class="col-sm-3" style="height: fit-content;" id="categoriesCard">
            @include('livewire.servicios.sidebar.categorias')  
        </div>
        <div class="col-sm-9">
        @else
        <div class="col-sm-12">
        @endif
            <div class="card h-auto">
                <div class="default-tab">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link {{ $pestaña == 1 ? 'active' : '' }}" name="pestaña-prod" onclick="changeTo(1)"><i class="la la-box mr-2"></i> Productos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $pestaña == 2 ? 'active' : '' }}" id="ventas-w" name="pestaña-ven" onclick="changeTo(2)"><i class="la la-store mr-2"></i> Venta</a>
                        </li>
                        <li class="nav-item" id="uso">
                            <a class="nav-link {{ $pestaña == 3 || $pestaña == 6 ? 'active' : '' }}" name="pestaña-uso" onclick="changeTo(3)"><i class="la la-download mr-2"></i> Uso</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $pestaña == 4 ? 'active' : '' }}" name="pestaña-ent" onclick="changeTo(4)"><i class="la la-truck mr-2"></i> Entregas</a>
                        </li>
                        <li id="pendientes" class="nav-item">
                            <a class="nav-link {{ $pestaña == 5 ? 'active' : '' }}" name="pestaña-ent" onclick="changeTo(5)"><i class="la la-user-clock mr-2"></i> Pendientes</a>
                        </li>
                    </ul>
                </div>
                
                    @if($pestaña == 1)
                        @include('livewire.productos.listado')
                    @elseif($pestaña == 2)
                        <livewire:ventas/>
                    @elseif($pestaña == 3)
                        <livewire:material-uso/>
                        <livewire:clientes :action="3" />
                    @elseif($pestaña == 4)
                        <livewire:compras/>
                    @elseif($pestaña == 5)
                        <livewire:pendientes :type="1"/>
                    @elseif($pestaña == 6)
                        <livewire:material-uso :view="2"/>
                    @endif
            </div>
        </div>
        
        @if($pestaña == 1)
        <div class="col-sm-3" style="height: fit-content;display:none" id="categoriesCardAux">
            @include('livewire.servicios.sidebar.categorias')  
        </div>
        @endif
    </div>


    
    {{-- card form --}}
    @include('livewire.ventas.reseña')
    @include('livewire.productos.view')
    @include('livewire.compras.create-product')
    @include('livewire.servicios.modals.changeReward')
    @include('livewire.clientes.categories')
    <livewire:categoria-productos :configuration="0"/>
    @include('livewire.productos.modals.mergeItems')

    @push('my-scripts')
        @include('livewire.productos.js')
    @endpush

</div>

@else
@include('livewire.sinPermisos')
@endif

<script>
    

function changeTo(type){
    console.log(type)
    var pestañas = {
    1: document.getElementsByName('pestaña-prod'),
    2: document.getElementsByName('pestaña-ven'),
    3: document.getElementsByName('pestaña-uso'),
    4: document.getElementsByName('pestaña-ent')
};

Object.keys(pestañas).forEach(function(key) {
    pestañas[key].forEach(function(pestaña) {
        if (key == type) {
            pestaña.classList.add('active');
        } else {
            pestaña.classList.remove('active');
        }
    });
});

    Livewire.emit('changeWindow', type)
    next()
}
</script>