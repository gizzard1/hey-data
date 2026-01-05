
@if(Auth::user()->role !== 'estilista')
<div>
    <div class="row" id="cardTable" style="display: {{ $action == 2 ? 'block' : 'none' }}">
        <div class="col-sm-12">
            <div class="card">
                
                
                <div class="default-tab">
 
                    <ul class="nav nav-tabs" role="tablist" style="width:fit-content">
                        <li class="nav-item">
                            <a class="nav-link {{ $pestaña == 1 ? 'active' : '' }}" name="pestaña-prod" onclick="changeTo(1)"><i class="la la-calendar mr-2" ></i> Cita</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $pestaña == 2 ? 'active' : '' }}" name="pestaña-ven" onclick="changeTo(2)"><i class="la la-download mr-2" ></i>Uso</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $pestaña == 5 ? 'active' : '' }}" name="pestaña-ent" onclick="changeTo(5)"><i class="la la-user-clock mr-2" ></i> Pendientes</a>
                        </li>
                    </ul>

                
                <div div class="tab-content categories-panel">
                    @if($pestaña == 1)
                        @include('livewire.calendar.main')
                    @elseif($pestaña == 2)
                        <livewire:material-uso :type="1"/>
                    @elseif($pestaña == 5)
                        <livewire:pendientes :type="0"/>
                    @elseif($pestaña == 6)
                        <livewire:corte/>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

</div>

@else
@include('livewire.sinPermisos')
@endif

<style>
    .customized:hover{
        background-color: #E2BBB4;
        color: aliceblue !important ;
        cursor:pointer;
    }
    .cust{
        background-color: #1d3557;
        color: aliceblue !important ;
        cursor:pointer;
    }
    a:hover{
        color:#1d3557;
    }
    a{
        color:#1d3557;
    }
    .nav-link{
        cursor:pointer;
    }
</style>
