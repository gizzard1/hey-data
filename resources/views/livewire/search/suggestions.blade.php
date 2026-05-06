
<div class="input-group m-auto" >
    <div x-data="{ open: false }" @click.away="open = false">
        <div style="display:flex">
            <!-- Input de búsqueda enlazado dinámicamente con wire:model -->
            <input autocomplete="off" wire:model="queryGlobal" type="text" class="form-control step-input" @focus="open = true" @click="open = true" @keydown.enter="open = false;" placeholder="Buscar..." id="searchInput">
            
            <div class="input-group-append">
                <a class="input-group-text save" style="color:white" onclick="simulateEnter()">
                    <i class="flaticon-381-search-2"></i>
                </a>
            </div>
        </div>
        
        <!-- Desplegable de resultados -->
        <div>
            <ul x-show="open" class="list-group float-right" style="position: absolute; z-index:1; max-height: 300px; overflow-y: auto;">
                @foreach ($sugerencias as $sugerencia)
                    @if(isset($sugerencia->first_name))
                        <li wire:click="selectedItem('{{ $sugerencia->id }}')" @click="open = false;" class="list-group-item list-group-item-action" style="cursor: pointer; color: #6E6E6E;">{{ $sugerencia->first_name }} {{ $sugerencia->last_name }} | {{ $sugerencia->phone }}</li>
                    @else
                        <li wire:click.prevent="selectedItem('{{ $sugerencia->id }}')" @click="open = false;" class="list-group-item list-group-item-action" style="cursor: pointer; color: #6E6E6E;">{{ $sugerencia->name }} | <span class="{{ $sugerencia->disccount_price > 0 ? "line-t text-gray-400" : "text-green" }}">{{ number_format($sugerencia->gross_price,2,'.',',') }}</span> @if($sugerencia->disccount_price>0) <span class="text-green">{{ number_format($sugerencia->disccount_price,2,'.',',') }}</span> @endif</li>

                    @endif
                @endforeach 
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
            Livewire.emit('searchGlobal');
        }

    </script>
    @endpush
</div>
