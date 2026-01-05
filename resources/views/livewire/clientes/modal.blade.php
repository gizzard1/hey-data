<div wire:ignore.self class="modal fade none-border" id="modalClientesForm" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content" style="height: 35rem; overflow: auto;">
            <div class="modal-header">
                <h4>Datos del Cliente</h4>
                <button type="button" class="close" onclick="closeModal()">
                    <span>x</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container" style="margin-top:20px">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" id="cust-name">
                                <label>Nombre(s)</label>
                                <input wire:model="cliente.first_name" type="text" class="form-control" placeholder="Nombre(s)" autocomplete="nope">
                                @error('cliente.first_name') <span class="text-danger">*Corrige este campo* </span> @enderror
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input wire:model="cliente.email" type="text" class="form-control" placeholder="Email">
                                @error('cliente.email') <span class="text-danger">*Corrige este campo* </span> @enderror
                            </div>
                            <div class="form-group">
                                <label>Sexo</label>
                                <select wire:model="cliente.sexo" class="form-control">
                                    <option value="">Seleccione una opción</option>
                                    <option value="femenino">Femenino</option>
                                    <option value="masculino">Masculino</option>
                                    <option value="noBinario">No binario</option>
                                </select>
                                @error('cliente.sexo') <span class="text-danger">*Corrige este campo* </span> @enderror
                            </div>
                            <div class="form-group">
                                <label>Cumpleaños</label>
                                <input wire:model="cliente.birth_date" type="date" style="background-color: white;" class="form-control" placeholder="<?php echo date('Y-m-d'); ?>">
                                @error('cliente.birth_date') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="showmemore" style="display: none;" wire:ignore>
                                <hr>
                                <div class="form-group" id="cust-noti">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" wire:model="cliente.want_custom_messages" value="1" id="customMessages">
                                        <label class="form-check-label" for="customMessages">
                                            Mensajes Personalizados
                                        </label>
                                    </div>
                                    @error('cliente.want_custom_messages') <span class="text-danger">*Corrige este campo* </span> @enderror
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" wire:model="cliente.want_offers" value="1" id="offers">
                                        <label class="form-check-label" for="offers">
                                            Ofertas
                                        </label>
                                    </div>
                                    @error('cliente.want_offers') <span class="text-danger">*Corrige este campo* </span> @enderror
                                </div>
                            </div>
                            <div class="mt-5" id="showmemorebutton" wire:ignore>
                                <a onclick="showmemore()">Ver más</a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Apellido</label>
                                <input wire:model="cliente.last_name" type="text" class="form-control" placeholder="Apellido">
                                @error('cliente.last_name') <span class="text-danger">*Corrige este campo* </span> @enderror
                            </div>
                            <div class="form-group d-flex" style="column-gap:1rem">
                                <div>
                                    <label for="prefijos">Lada </label>
                                    <select wire:model="lada" style="appearance: none;" id="prefijos" name="prefijos" class="form-control" style="width:5rem">
                                        <option value="+52">+52 - México</option>
                                        <option value="+1">+1 - Estados Unidos, Canadá</option>
                                        <option value="+44">+44 - Reino Unido</option>
                                        <option value="+49">+49 - Alemania</option>
                                        <option value="+33">+33 - Francia</option>
                                        <option value="+34">+34 - España</option>
                                        <option value="+39">+39 - Italia</option>
                                        <option value="+81">+81 - Japón</option>
                                        <option value="+61">+61 - Australia</option>
                                        <option value="+86">+86 - China</option>
                                        <option value="+91">+91 - India</option>
                                        <option value="+55">+55 - Brasil</option>
                                        <option value="+7">+7 - Rusia</option>
                                        <option value="+82">+82 - Corea del Sur</option>
                                        <option value="+66">+66 - Tailandia</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="phone">Teléfono</label>
                                    <input placeholder="Teléfono o celular" wire:model="cliente.phone" type="number" required class="form-control" id="phone" style="width:16rem">
                                    @error('cliente.phone') <span class="text-danger">*Favor de llenar este campo* </span> @enderror
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Procedencia</label>
                                <select wire:model="cliente.procedencia_id" class="form-control">
                                    <option value="">Seleccionar una opción</option>
                                    @foreach ($procedencias as $procedencia)
                                        <option value="{{ $procedencia->id }}">{{ $procedencia->name }}</option>
                                    @endforeach
                                </select>
                                @error('cliente.procedencia') <span class="text-danger">*Corrige este campo* </span> @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="postcode">Código Postal</label>
                                <select wire:model="cliente.postcode" type="text" class="form-control" placeholder="Código postal">
                                    @foreach(['Seleccione una opción', '42100','44037','44100','44130','44140','44150','44160','44200','44210','44220','44230','44240','44250','44260','44270','44280','44290','44300','44320','44330','44340','44350','44360','44370','44380','44390','44400','44410','44420','44430','44440','44450','44460','44470','44490','44500','44510','44520','44540','44550','44560','44580','44600','44610','44620','44630','44640','44648','44650','44660','44670','44680','44690','44700','44710','44720','44730','44740','44750','44760','44770','44780','44790','44800','44810','44820','44840','44860','44870','44890','44900','44910','44920','44930','44940','44950','44960','44967','44969','44970','44980','44984','44985','44987','44990','45000','45010','45013','45016','45017','45018','45019','45020','45027','45029','45030','45036','45037','45038','45040','45046','45047','45049','45050','45051','45054','45055','45056','45059','45060','45066','45067','45068','45069','45070','45071','45079','45080','45081','45085','45088','45100','45110','45116','45117','45118','45119','45120','45127','45130','45132','45134','45136','45138','45140','45146','45147','45148','45149','45150','45160','45167','45168','45169','45170','45180','45184','45185','45186','45188','45190','45196','45197','45199','45200','45201','45203','45205','45221','45230','45235','45236','45237','45239','45300','45323','45330','45400','45402','45403','45404','45405','45406','45407','45408','45409','45410','45412','45413','45414','45416','45417','45418','45419','45420','45422','45424','45425','45426','45427','45428','45429','45465','45500','45510','45529','45530','45560','45580','45590','45600','45609','45610','45615','45618','45620','45621','45623','45630','45635','45640','45641','45643','45644','45645','45646','45647','45648','45650','45653','45654','45655','45656','45659','45660','45665','45670','45672','45675','45679','45680','45681','45684','45685','45690','45693','45694','45696','45710','45854','46000','46500','46510','46640','46680'] as $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                                @error('cliente.postcode') <span class="text-danger">*Corrige este campo* </span> @enderror
                            </div>
                            <div class="showmemore" style="display: none;" wire:ignore>
                                
                                <div class="form-group" wire:ignore>
                                    <label>Categoría(s)</label>
                                <input wire:model="listCategories" type="text" placeholder="Buscar categoría" autocomplete="off" id="tomCategory" class="form-control">
                                </div>
                            </div>
                        
                            <div class="mt-5 d-flex " style="column-gap:1rem;justify-content:end">
                                <button class="btn btn-sm float-right" onclick="closeModal()">Cancelar</button>
                                <button id="save-info" onclick="next()" class="save btn btn-sm btn-info float-right" wire:click="Store" >Guardar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<style>
   .ts-control  > input{
       color:black !important
   }
   .item {
       background-color: #278d46  !important;
   }
   
   .ts-control {
        padding: 0px !important;
        border-style: none;
        border-width: 0px !important;
        background-color: #0E0803
    }
    .form-control {
        height: auto;
    }
</style>

<script>
    function showmemore()
    {
        var area = document.getElementsByClassName('showmemore');
        var button = document.getElementById('showmemorebutton');
        button.style.display = 'none';

        Array.from(area).forEach(element => {
            element.style.display = 'block';
        });
    }
</script>