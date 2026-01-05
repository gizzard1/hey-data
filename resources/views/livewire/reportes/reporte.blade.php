@if(Auth::user()->role!=='estilista' && $info->salon_id == Auth::user()->salon_id)

<div style="margin-top:4rem;margin-left:2.5rem">
<div style="margin-left:2rem">

@include('livewire.reportes.logo')
</div>
@if($action)
<div style="display: block;">

@include('livewire.reportes.ticket')
</div>
@else
@include('livewire.reportes.ticketCita')
@endif
@include('livewire.reportes.footer')
<script>
    
    window.print();
</script>

</div>

@else
@include('livewire.sinPermisos')
@endif

<style>
    body{
        font-size: 0.7rem!important;
    }
    table{
        margin-left: 2.5rem!important;
    }
</style>