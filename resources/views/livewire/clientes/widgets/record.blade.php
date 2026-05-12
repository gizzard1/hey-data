{{-- Expediente de cliente --}}

<div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Expediente</h3>
            <button class="btn btn-sm" style="color: white; background-color:#1D3557" data-toggle="modal"
                data-target="#modalRecord"
                onclick="initQuill();setQuillContent(@js($customerSelected->record))">Editar</button>
        </div>
        <div class="card-body card-body-record card-body-data">
            <div class="col-md-12">
                <div class="form-group" id="recordReadOnly" wire:ignore></div>
            </div>
        </div>
    </div>
</div>