<div wire:ignore.self class="modal fade none-border" id="modalCitasForm" data-backdrop="static" data-keyboard="false" >
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content" style="height: auto; overflow: auto;">
            @include('livewire.calendar.modal.header')
            @include('livewire.calendar.cliente')
            @include('livewire.calendar.modal.cart-view')
            <div wire:ignore hidden class="card-body" id="tags">
                <div class="form-group">
                    <label>Etiquetas(s)</label>
                    <input type="text" placeholder="Buscar etiqueta" autocomplete="off" id="tomTags" class="form-control" wire:model.defer="listTags">
                </div>
            </div>

            <div class="modal-footer">
                <div style="display:-webkit-box">
                    <div style="margin-right: 14rem;" id="remember">
                        <label for="rememberDate">Recordar cita </label>
                        <input type="checkbox" id="rememberDate" name="rememberDate" wire:model.defer="remember">
                    </div>
                    <div style="display:inline-flex;column-gap:1rem">
                        <button class="btn btn-sm btn-dark" data-dismiss="modal" data-toggle="modal" wire:click.defer="cancelarCaptura" id="closeNewDate">Regresar</button>
                        <button id="agendar" class="btn-sm input-group-text" wire:click="Agendar" onclick="next()" {{ $asignacion_id ? 'hidden' : '' }}>Agendar</button>
                        @if($asignacion_id!==null)
                        <button id="endoredit" class="btn-sm input-group-text" wire:click="storeDate" data-dismiss="modal">{{ $itemSelected->status !== 'Pagada' ? 'Editar cita' : 'Finalizar cita' }}</button>
                        @endif
                        <button class="btn-sm input-group-text" wire:click.prevent="showAdvanced">Vista avanzada</button>
                        @if($asignacion_id!==null)
                        <button onclick="CancelDate()" class="btn tp-btn btn-xxs btn-danger "><i class="fa fa-trash fa-lg"></i></button>
                        @endif
                        <a class="m-auto" wire:click="$set('uploadFiles',{{ !$uploadFiles }})"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-upload" width="25" height="25" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                            <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                            <path d="M12 11v6" />
                            <path d="M9.5 13.5l2.5 -2.5l2.5 2.5" />
                        </svg></a>
                        <a class="m-auto" id="tagsButton" onclick="addTags()"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-upload" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="24" height="24" stroke-width="2">
                            <path d="M3 8v4.172a2 2 0 0 0 .586 1.414l5.71 5.71a2.41 2.41 0 0 0 3.408 0l3.592 -3.592a2.41 2.41 0 0 0 0 -3.408l-5.71 -5.71a2 2 0 0 0 -1.414 -.586h-4.172a2 2 0 0 0 -2 2z"></path>
                            <path d="M18 19l1.592 -1.592a4.82 4.82 0 0 0 0 -6.816l-4.592 -4.592"></path>
                            <path d="M7 10h-.01"></path>
                            </svg></a>
                    </div>
                </div>
        </div>
@include('livewire.calendar.jsCitas')
    <livewire:clientes :action="3"/>
    </div>
    </div>

<style>
    .color-selector{
        width: 1.5rem;
        height: 1.5rem;
        padding: revert;
    }
</style>