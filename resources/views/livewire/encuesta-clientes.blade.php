<div class="container mt-4">
    <div class="row">
        <div class="col-md-12" style="padding:6rem">
            <div class="card">
                <div class="card-body" style="padding:4rem">
                    <h4 class="text-center">¡Gracias por ser parte de nosotros!</h4>
                    <p class="text-center">Ayúdanos a seguir mejorando tu experiencia. Para nosotros es importante conocer tu opinión.</p>

                    <div style="margin:2rem 0 2rem">
                        
                        <!-- SECTION 1 -->
                        <h5>Información Personal</h5>
                        <div class="form-row mb-6" style="row-gap:1rem">
                            <div class="col-md-6">
                                <label for="first_name">Nombre *</label>
                                <input placeholder="nombre" wire:model.defer="cliente.first_name" type="text" required class="form-control" id="first_name">
                                @error('cliente.first_name') <span class="text-danger">*Favor de llenar este campo* </span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="last_name">Apellido</label>
                                <input placeholder="apellido" wire:model.defer="cliente.last_name" type="text" class="form-control" id="last_name">
                            </div>
                            <div class="col-md-1">
                                <label for="prefijos">Lada </label>
                                <select wire:model.defer="lada" style="appearance: none;" id="prefijos" name="prefijos" class="form-control">
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
                            <div class="col-md-5">
                                <label for="phone">Teléfono *</label>
                                <input placeholder="Teléfono o celular" wire:model.defer="cliente.phone" type="number" required class="form-control" id="phone">
                                @error('cliente.phone') <span class="text-danger">*Favor de llenar este campo* </span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email">Correo</label>
                                <input placeholder="ejemplo@mail.com" wire:model.defer="cliente.email" type="email" class="form-control" id="email">
                            </div>
                            <div class="col-md-6">
                                <label for="birth_date">Cumpleaños</label>
                                <input wire:model.defer="cliente.birth_date" class="form-control" type="date" id="birth_date">
                            </div>
                            <div class="col-md-6">
                                <label for="postcode">Código Postal</label>
                                    <select wire:model.defer="cliente.postcode" type="text" class="form-control" placeholder="Código postal">
                                        @foreach(['Seleccione una opción', '42100','44037','44100','44130','44140','44150','44160','44200','44210','44220','44230','44240','44250','44260','44270','44280','44290','44300','44320','44330','44340','44350','44360','44370','44380','44390','44400','44410','44420','44430','44440','44450','44460','44470','44490','44500','44510','44520','44540','44550','44560','44580','44600','44610','44620','44630','44640','44648','44650','44660','44670','44680','44690','44700','44710','44720','44730','44740','44750','44760','44770','44780','44790','44800','44810','44820','44840','44860','44870','44890','44900','44910','44920','44930','44940','44950','44960','44967','44969','44970','44980','44984','44985','44987','44990','45000','45010','45013','45016','45017','45018','45019','45020','45027','45029','45030','45036','45037','45038','45040','45046','45047','45049','45050','45051','45054','45055','45056','45059','45060','45066','45067','45068','45069','45070','45071','45079','45080','45081','45085','45088','45100','45110','45116','45117','45118','45119','45120','45127','45130','45132','45134','45136','45138','45140','45146','45147','45148','45149','45150','45160','45167','45168','45169','45170','45180','45184','45185','45186','45188','45190','45196','45197','45199','45200','45201','45203','45205','45221','45230','45235','45236','45237','45239','45300','45323','45330','45400','45402','45403','45404','45405','45406','45407','45408','45409','45410','45412','45413','45414','45416','45417','45418','45419','45420','45422','45424','45425','45426','45427','45428','45429','45465','45500','45510','45529','45530','45560','45580','45590','45600','45609','45610','45615','45618','45620','45621','45623','45630','45635','45640','45641','45643','45644','45645','45646','45647','45648','45650','45653','45654','45655','45656','45659','45660','45665','45670','45672','45675','45679','45680','45681','45684','45685','45690','45693','45694','45696','45710','45854','46000','46500','46510','46640','46680'] as $value)
                                        <option value="{{ $value }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                            </div>
                            <div class="col-md-12">
                                <label for="sexo">Me identifico como</label>
                                <select wire:model.defer="cliente.sexo" class="form-control" id="sexo">
                                    <option value="null">Seleccione una opción</option>
                                    <option value="femenino">Mujer</option>
                                    <option value="masculino">Hombre</option>
                                    <option value="noBinario">No binario</option>
                                </select>
                            </div>
                        </div>

                    </div>

                    

                    <!-- SECTION 3 -->
                    <h5>Califica tu experiencia</h5>
                    <div class="rating-widget mb-4 text-center" wire:ignore>
                        <div class="rating-stars">
                            <ul id="stars">
                                @for($i = 1; $i <= 5; $i++)
                                    <li class="star" title="{{ $i }}" data-value="{{ $i }}" wire:click="setCalificacion({{ $i }})">
                                        <i class="fa fa-star fa-fw"></i>
                                    </li>
                                @endfor
                            </ul>
                        </div>
                    </div>

                    <div class="form-group"  style="margin-bottom:2rem">
                        <label for="review">Escribe una reseña (opcional)</label>
                        <textarea id="review" wire:model.prevent="review" class="form-control" placeholder="Escribe aquí..." maxlength="100" style="resize: none;"></textarea>
                    </div>


                    <!-- SECTION 2 -->
                    <h5>¿Cómo te enteraste de nosotros?</h5>
                    <div class="form-group" style="margin-bottom: 2rem;">
                        @foreach ($procedencias as $procedencia)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="source" wire:model.defer="qt1" value="{{ $procedencia->id }}" id="{{ $procedencia->name }}">
                                <label class="form-check-label" for="{{ $procedencia->name }}">{{ ucfirst($procedencia->name) }}</label>
                            </div>
                        @endforeach
                        @error('qt1') <span class="text-danger">*Favor de elegir una opción* </span> @enderror
                    </div>

                    <h5>¿Qué servicio te brindaron en el salón?</h5>
                    <div class="form-group" style="margin-bottom:2rem">
                        @foreach(['A' => 'highlights / Balayage / Efecto de Color', 'B' => 'Tinte', 'C' => 'Corte de Cabello', 'D' => 'Peinado', 'E' => 'Tratamiento Capilar', 'F' => 'Manicure / Pedicure', 'G' => 'Aplicación de Gel en uñas', 'H' => 'Maquillaje', 'I' => 'Acrílico'] as $key => $service)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" wire:model.defer="qt2" value="{{ $key }}" id="{{ $key }}">
                                <label class="form-check-label" for="{{ $key }}">{{ $service }}</label>
                            </div>
                        @endforeach
                        @error('qt2') <span class="text-danger">*Favor de elegir una opción* </span> @enderror
                    </div>


                    <!-- SECTION 4 -->
                    <h5>¿Ya nos sigues en redes?</h5>
                    <div class="form-group" style="margin-bottom:2rem">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="social_media" wire:model.defer="qt3" value="1" id="follow_yes">
                            <label class="form-check-label" for="follow_yes">Sí</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="social_media" wire:model.defer="qt3" value="0" id="follow_no">
                            <label class="form-check-label" for="follow_no">No</label>
                        </div>
                        @error('qt3') <span class="text-danger">*Favor de elegir una opción* </span> @enderror
                    </div>

                    <h5>¿Te gustaría recibir promociones exclusivas vía WhatsApp?</h5>
                    <div class="form-group" style="margin-bottom:2rem">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="promotions" wire:model.defer="qt4" value="1" id="promo_yes">
                            <label class="form-check-label" for="promo_yes">Sí</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="promotions" wire:model.defer="qt4" value="0" id="promo_no">
                            <label class="form-check-label" for="promo_no">No</label>
                        </div>
                        @error('qt4') <span class="text-danger">*Favor de elegir una opción* </span> @enderror
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-dark" wire:click="Store">Enviar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


<script>
    
  
    // Obtener todas las estrellas
    const stars = document.querySelectorAll('.star');
  
  // Agregar un controlador de eventos clic a cada estrella
  stars.forEach(star => {
      star.addEventListener('click', function() {
          // Obtener el valor de la estrella seleccionada
          const ratingValue = parseInt(this.getAttribute('data-value'));
  
      });
  });

  
  function initializeFlatpickr() {
        flatpickr(document.getElementsByClassName('flatpickr'), {
            enableTime: false,
            dateFormat: 'Y-m-d',
            locale: {
                firstDateofWeek: 1,
                weekdays: {
                    shorthand: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"],
                    longhand: ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"]
                },
                months: {
                    shorthand: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
                    longhand: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"]
                }
            }
        })
    }

    
    document.addEventListener('livewire:load', function () {
    initializeFlatpickr()
})
  </script>
</div>

<style>
    .margenes{
    display: flex;
    justify-content: center;
    }
    
</style>
