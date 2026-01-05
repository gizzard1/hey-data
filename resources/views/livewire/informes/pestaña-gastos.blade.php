<thead>
    <tr style="font-weight:bold">
        <td>Concepto</td>
        <td>Monto</td>
    </tr>
</thead>
<tbody>
    @foreach($dataExpensesFinal['label'] as $index => $label)
        <tr>
            <td>{{ $label }}</td>
            <td>${{ number_format($dataExpensesFinal['qty'][$index] ?? 0, 2, '.', ',') }}</td>
        </tr>
    @endforeach
    <tr>
        <td>Total</td>
        <td>${{ number_format($totalGastos ?? 0,2,'.',',') }}</td>
    </tr>
</tbody>



<script>
    
function changeTo(type){
var pestaña_all = document.getElementsByName('pestaña-all')
var pestaña_ac = document.getElementsByName('pestaña-ac')
var pestaña_no_ac = document.getElementsByName('pestaña-no-ac')
    if(type==1){
        pestaña_ac.forEach(function(pestaña) {
            pestaña.classList.remove('active');
        });
        pestaña_no_ac.forEach(function(pestaña) {
            pestaña.classList.remove('active');
        });
        pestaña_all.forEach(function(pestaña) {
            pestaña.classList.add('active');
        });  
    }else if(type==2){
        pestaña_ac.forEach(function(pestaña) {
            pestaña.classList.add('active');
        });
        pestaña_all.forEach(function(pestaña) {
            pestaña.classList.remove('active');
        });
        pestaña_no_ac.forEach(function(pestaña) {
            pestaña.classList.remove('active');
        });
    }else if(type==3){
        pestaña_no_ac.forEach(function(pestaña) {
            pestaña.classList.add('active');
        });
        pestaña_all.forEach(function(pestaña) {
            pestaña.classList.remove('active');
        });
        pestaña_ac.forEach(function(pestaña) {
            pestaña.classList.remove('active');
        });
    }
    Livewire.emit('changeWindow', type)
}

</script>