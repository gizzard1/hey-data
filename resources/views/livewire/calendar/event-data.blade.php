<div x-show="hover" class="square-popover" id="pop-{{ $cita->id }}">
    <div class="title-pop-over">
        Horario: 09:00 - 12:45
    </div>
    <div class="body-pop-over">
        <span>Norma Avelar</span>
        <br>
        <span>Servicio: corte de pelo</span>
        <br>
        <span>Descripcion del cliente: recurrente</span>
    </div>
    <div class="footer-pop-over">
        Total: $515.00
    </div>
</div>

<style>
    
.square-popover {
    border: solid 1px #1d3557;
    border-radius: 5px;
    width: max-content;
    height: min-content;
    position: absolute;
    font-size: 14px;
    background-color: white;
    left: 50rem;
}

.title-pop-over {
    background-color: #ced0d35d;
    padding: 1dvh;
    text-align: center;
    border-bottom: solid 1px black;
}

.body-pop-over {
    display: flex;
    flex-direction: column;
    padding: 2dvh;
    row-gap: 0.5dvh;
}

.footer-pop-over {
    background-color: #ced0d35d;
    padding: 1dvh;
    text-align: right;
    border-top: solid 1px black;
}

</style>