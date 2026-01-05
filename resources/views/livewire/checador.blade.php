@include('livewire.checador.modals.fingerprint')
@push('my-scripts')
    @include('livewire.checador.modals.getFingerprint')
    @include('livewire.checador.js.comparadorjs')
    @include('livewire.checador.js.recortadorjs')
    @include('livewire.checador.js.adelgazador')
@endpush