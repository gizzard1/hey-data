
<div class="input-group m-auto" >
    <div x-data="{ open: false }" @click.away="open = false">
        <div style="display:flex">
            <!-- Input de búsqueda enlazado dinámicamente con wire:model -->
            <input autocomplete="off" wire:keydown.enter="$emit('search', $event.target.value)" wire:model="queryGlobal" type="text" class="form-control step-input" @focus="open = true" @click="open = true" @keydown.enter="open = false;" placeholder="Buscar..." id="searchInput">
            
            <div class="input-group-append">
                <a class="input-group-text save" style="color:white" onclick="simulateEnter()">
                    <i class="flaticon-381-search-2"></i>
                </a>
            </div>
        </div>
        
        <!-- Desplegable de resultados -->
        <div>
            <ul x-show="open" class="list-group float-right" style="position: absolute; z-index:1; max-height: 300px; overflow-y: auto;">
                @if(isset($queryGlobal) && $queryGlobal!=null)
                <li wire:click="searchGlobal(1)" @click="open = false;" class="list-group-item list-group-item-action" style="cursor: pointer; color: #6E6E6E;">Buscar: "<strong>{{$queryGlobal}}</strong>" en Productos</li>
                <li wire:click="searchGlobal(2)" @click="open = false;" class="list-group-item list-group-item-action" style="cursor: pointer; color: #6E6E6E;">Buscar: "<strong>{{$queryGlobal}}</strong>" en Servicios</li>
                <li wire:click="searchGlobal(3)" @click="open = false;" class="list-group-item list-group-item-action" style="cursor: pointer; color: #6E6E6E;">Buscar: "<strong>{{$queryGlobal}}</strong>" en Empleados</li>
                <li wire:click="searchGlobal(4)" @click="open = false;" class="list-group-item list-group-item-action" style="cursor: pointer; color: #6E6E6E;">Buscar: "<strong>{{$queryGlobal}}</strong>" en Clientes</li>
                <li wire:click="searchGlobal(5)" @click="open = false;" class="list-group-item list-group-item-action" style="cursor: pointer; color: #6E6E6E;">Buscar: "<strong>{{$queryGlobal}}</strong>" en Proveedores</li>
                @endif
            </ul>
        </div>
    </div>
    @push('my-scripts')
    <script>
        livewire.on('search', event=>{
            document.getElementById('searchInput').value=''
        })
        
        Livewire.on('redirect', url => {
            window.open(url, '_blank')
        })

        function simulateEnter() {
            // Selecciona el input por su ID
            const input = document.getElementById('searchInput');
            
            // Crea un evento keydown con la tecla Enter
            const event = new KeyboardEvent('keydown', {
                bubbles: true,
                cancelable: true,
                key: 'Enter',
                code: 'Enter',
                keyCode: 13,
                which: 13
            });

            // Emite el evento en el input
            input.dispatchEvent(event);
        }

    </script>
    @endpush
</div>
