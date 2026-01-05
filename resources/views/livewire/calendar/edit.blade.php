
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
                    </ul>

                
                <div div class="tab-content categories-panel">
                    @if($pestaña == 1)
                        @include('livewire.calendar.form-cita-edit')
                    @elseif($pestaña == 2)
                        <livewire:material-uso :type="1"/>
                    @endif
                </div>
            </div>
        </div>
    </div>

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

<script>
    
    
    function changeTo(type){
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
    }
</script>