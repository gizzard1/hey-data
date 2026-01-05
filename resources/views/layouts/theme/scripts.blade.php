<script src="{{ asset('vendor/global/global.min.js') }}"></script> 
<script src="{{ asset('vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('js/popper.min.js') }}"></script>
{{-- <script src="{{ asset('vendor/chart.js/Chart.bundle.min.js') }}"></script> --}}

<script src="{{ asset('vendor/peity/jquery.peity.min.js') }}"></script>


<!-- Rating -->
<script src="{{ asset('vendor/star-rating/jquery.star-rating-svg.js') }}"></script>

<!-- <script src="{{ asset('vendor/apexchart/apexchart.js') }}"></script>

<script src="{{ asset('js/dashboard/dashboard-2.js') }}"></script> -->

<script src="{{ asset('js/custom.min.js') }}"></script>
<script src="{{ asset('js/alpine.min.js') }}"></script>

{{-- <script src="{{ asset('js/chart.min.js') }}"></script>
<script src="{{ asset('js/chartist.min.js') }}"></script> --}}

<script src="{{ asset('js/deznav-init.js') }}"></script>

<script src="{{ asset('vendor/toastr/js/toastr.min.js') }}"></script>

<script src="{{ asset('js/sweetalert2.js') }}"></script>

<script src="{{ asset('js/tom-select.complete.min.js') }}"></script>

<!-- Step's scripts -->
<script src="{{ asset('js/jquery.steps.js') }}"></script>
<script src="{{ asset('js/main.js') }}"></script>




<script src="{{ asset('js/notify.min.js') }}"></script>

<script src="{{ asset('js/slick-loader.min.js') }}"></script>

<script src="{{ asset('plugins/flatpickr.js') }}"></script>


<script src="{{ asset('vendor/moment/moment.min.js') }}"></script>

<script src="{{ asset('vendor/pickadate/picker.js') }}"></script>
<script src="{{ asset('vendor/pickadate/picker.date.js') }}"></script>
<script src="{{ asset('js/plugins-init/pickadate-init.js') }}"></script>

<!--  <script src="./js/plugins-init/fullcalendar-init.js"></script> -->
<script src="{{ asset('js/driver.js.iife.js') }}"></script>

<!-- Fingerprint functions -->
<!-- <script src="{{ asset('js/es6-shim.js') }}"></script>
<script src="{{ asset('js/fingerprint.sdk.min.js') }}"></script>
<script src="{{ asset('js/websdk.client.bundle.min.js') }}"></script> -->


<script>

    
document.addEventListener('keydown', function(event) {
    // Comprobar si el elemento activo es un input o textarea
    if (document.activeElement.tagName === 'INPUT' || document.activeElement.tagName === 'TEXTAREA') {
        return; // No hacer nada si se está escribiendo en un input
    }
});
    //full loaded
    window.addEventListener('load', () => {

        document.addEventListener('ok', (event) => {
            Swal.fire({
                title: "<span>"+ "info" + "</span>",
                html: event.detail.msg,
                timer: 3000,
                showConfirmButton: !1,
                confirmButtonColor: '#EAE0D8',
            }).then((result) => {
                // do something
            })
        })


        document.addEventListener('noty-error', (event) => {
            toastr.error(event.detail.msg, "Algo salió mal :(",{
                positionClass: "toast-bottom-right",
                closeButton: true,
                progressBar: true, 
            })
        })

        document.addEventListener('stop-loader', (event) => {
            hideProcessing();
        })

        document.addEventListener('loader', (event) => {
            showProcessing();
        })

        document.addEventListener('noty', (event) => {
            toastr.info(event.detail.msg, "Mensaje:",{
                
                positionClass: "toast-bottom-right",
                closeButton: !0,
                progressBar: !0, 
                confirmButtonColor: '#EAE0D8',
            })
        })

    })


document.addEventListener('livewire:load', function () {
    const mainWrapper = document.getElementById('main-wrapper');

    const handlers = [
        {
            trigger: document.querySelector('.deznav'),
            onEnter: () => mainWrapper?.classList.remove('menu-toggle'),
            onLeave: () => mainWrapper?.classList.add('menu-toggle'),
        },
        {
            trigger: document.getElementById('user-profile-dropdown'),
            target: document.getElementById('user-profile-menu'),
            class: 'show',
        },
        {
            trigger: document.getElementById('notifications-dropdown'),
            target: document.getElementById('notifications-menu'),
            class: 'show',
        },
    ];

    handlers.forEach(({ trigger, target, class: cls, onEnter, onLeave }) => {
        if (!trigger) return;

        trigger.addEventListener('mouseenter', () => {
            if (onEnter) return onEnter();
            target?.classList.add(cls);
        });

        trigger.addEventListener('mouseleave', () => {
            if (onLeave) return onLeave();
            target?.classList.remove(cls);
        });
    });
});


document.querySelector('.save').addEventListener('click', function() {   
        showProcessing()
});
document.querySelector('.processing-info').addEventListener('click', function() {   
        showProcessing()
});

document.addEventListener('click', function (event) {    
    if (event.target.classList.contains('save') || event.target.classList.contains('processing-info')) {
        showProcessing()
    }
})


function showProcessing() {
    SlickLoader.setText('HEY DATA','PROCESANDO SOLICITUD')
    SlickLoader.enable()
}


function hideProcessing() {    
    SlickLoader.disable()
}


</script>