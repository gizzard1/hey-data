<?php

namespace App\Http\Livewire;

use App\Models\abono;
use App\Models\abonoPropina;
use App\Models\caja_apertura;
use App\Models\caja_corte;
use App\Models\cita;
use App\Models\gasto;
use App\Models\venta;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Corte extends Component
{
    public $tarjetaCorteReal=[],$tarjetaCortePropinaReal=[],$tarjetaCorte=[],$tarjetaCortePropina=[],$propinasEfectivo,$propinasMsi,$propinasTarjeta,$ventasCorte,$serviciosCorte,$efectivoCorte,$cardCorte,$msiCorte,$puntosCorte;
    public $comissionsCorte;
    public $totalCorte,$totalTips;
    public $tipsCorte;
    public $totalCashReal,$totalNFreal,$propinasEfectivoReal,$propinasMsiReal,$totalCardReal,$propinasCardReal,$totalCorteReal,$totalTipsReal;
    public $propinasCard,$iva;
    public $description='', $total_cash_real, $total_NF_real, $propinas_efectivo_real, $propinas_msi_real, $total_fp, $total_p;
    public $isOpened;
    public $totalCorte_neto,$terminales=[],$terminales_propina=[],$total_real,$total_propinas_real,$total_propinas;
    public Collection $cartP,$cartPendingMethods,$cartPendingPropinas;
    public $totalCalculated;
    private caja_apertura $apertura;
    public $cajaChica = 0, $caja_chica,$caja_chica_real, $gastos = 0, $gastos_qty = [];
    public $diferencia = 0,$ventas=[],$citas=[];
    public function mount()
    {
        $this->verificarApertura();
        if($this->isOpened){
            $this->corteCaja();
        }
    }
    public function render()
    {
        return view('livewire.caja-corte',[
            'efectivoCorte' => $this->efectivoCorte,
            'propinasEfectivo' => $this->propinasEfectivo,
            'total_gastos' => $this->gastos_qty,
            'caja_chica' => $this->caja_chica,
            'gastos' => $this->gastos,
        ]);
    }

    public function apertura()
    {
        $this->aperturarCaja();
        $this->dispatchBrowserEvent('noty', ['msg' => 'CAJA APERTURADA CON ÉXITO']);
    }
    private function aperturarCaja()
    {
        try{
            $this->cajaChica = $this->eliminarCaracteres($this->cajaChica);
            $this->apertura = new caja_apertura;
            $this->apertura->caja_chica = $this->cajaChica;
            $this->apertura->user_id = Auth()->user()->id;
            $this->apertura->save();
            return redirect('informe-caja');
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 11456Cortes"] );
        }
    }
    private function sumMethod($metodo,$qty,$isPropina)
    {
        try{
            if($metodo->payment_method_id===1){
                if(!$isPropina){
                    $this->efectivoCorte+=$qty;
                }else{
                    $this->propinasEfectivo+=$qty;
                }
            }
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 743271Ventas"] );
        }
    }
    private function acumularMetodos($metodos,$isPending,$type,$isDate)
    {
        try{
            $total_methods = 0;
            foreach($metodos as $metodo){
                if ($metodo->amount == 0) {
                    continue; // Saltar métodos de pago con cantidad cero
                }
                $qty=$metodo->amount-$metodo->change;
                $total_methods+=$qty;
                if($isPending){
                    if($isDate){
                        $coll = collect([
                            'payment_method_id' => $metodo->payment_method_id,
                            'payed_qty' => -($qty),
                            'cita_id' => $metodo->cita_id,
                        ]);  
                    } else{
                        $coll = collect([
                            'payment_method_id' => $metodo->payment_method_id,
                            'payed_qty' => -($qty),
                            'venta_id' => $metodo->venta_id,
                        ]);  
                    }
                    $itemCart = Arr::add($coll, null, null);
                    if($type){
                        $this->cartPendingPropinas->push($itemCart);
                    }else{
                        $this->cartPendingMethods->push($itemCart);
                    }
                }
                $this->sumMethod($metodo,$qty,$type);
            }
            return $total_methods;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 12457Ventas"] );
        }
    }
    private function calculateTaxes($transaccion)
    {
        $disccount_per_item = 0;
        $total_taxes = 0;
        //Se divide el descuento de la transacción entre los items existentes
        if($transaccion->disccount>0){
            $items = count($transaccion->details);
            $disccount_per_item = $items>0 ? $transaccion->disccount/$items : $transaccion->disccount;
        }
        foreach($transaccion->details as $detail){
            //Por detalle se multiplica la cantidad descontada anteriormente por la cantidad de items o en su defecto se mantiene
            $extra_disccount = $disccount_per_item * $detail->quantity ?? 1; 
            //La cantidad descontada es restada sobre el precio total de ese detalle y se divide entre el 100% + su respectivo impuesto en %
            if($detail->disccount_price>0){
                $total_taxes+=($detail->disccount_price-$extra_disccount)/($detail->iva+1);
            }else{
                $total_taxes+=($detail->current_price-$extra_disccount)/($detail->iva+1);
            }
            //Aprovechamos la rutina para obtener la comisión por detalle
            $this->comissionsCorte+=$detail->comission;
        }
        return $total_taxes;
    }
    private function acumularTransacciones($transacciones,$isDate)
    {
        try{
            $totalTransaccion=0;
            foreach($transacciones as $transaccion){
                //Acumulamos las cantidades reales obtenidas en las diferentes formas de pago (las predeterminadas y las que el salón agregue)
                $methods_data = $this->acumularMetodos($transaccion->metodosPago,$transaccion->status == 'Pendiente',0,$isDate);
                //Acumulamos las cantidades reales obtenidas en las diferentes formas de pago para propinas (las predeterminadas y las que el salón agregue)
                $this->totalTips += $this->acumularMetodos($transaccion->propinas,$transaccion->status == 'Pendiente',1,$isDate);

                //Calculamos la cantidad de impuestos pagados
                $total_taxes = $this->calculateTaxes($transaccion);
                if($transaccion->status == 'Pendiente'){
                    $total_transaccion = $transaccion->total-$transaccion->discount;
                    $totalTransaccion += $methods_data;

                    if($isDate){
                        $abonosCart = $this->cartPendingMethods->where('cita_id',$transaccion->id);
                    } else{
                        $abonosCart = $this->cartPendingMethods->where('venta_id',$transaccion->id);
                    }
                    
                    //Modificamos el la colección para que cada abono tenga su deuda, impuestos y relación con la transacción
                    foreach($abonosCart as $abono){
                        $percent_payed = $abono['payed_qty']/$total_transaccion;
                        $abono['total_debt'] = $total_transaccion;
                        $abono['taxes_payed'] = $percent_payed*$total_taxes;
                    }
                    //Se obtiene una porcentaje en relación a la cantidad real recibida entre el total de la transacción
                    $percent_payed = $methods_data/$total_transaccion;
                    //En base al porcentaje se obtiene una proporción de los impuestos pagados
                    $total_taxes = $percent_payed*$total_taxes;
                }else{
                    $totalTransaccion += $transaccion->total-$transaccion->disccount;
                }

                //Verificación de los abonos existentes. Se resta las cantidades para evitar que aparezcan las actualizaciones en el corte después de recibir un abono
                $metodos_prev=[];
                $abonos = $transaccion->abonos()->orderBy('payed_qty', 'asc')->get();
                foreach($abonos as $abono){
                    if(!in_array($abono->payment_method_id,$metodos_prev)){
                        //A los impuestos totales se le sumarán la cantidad negativa ya pagada de los abonos en impuestos
                        $total_taxes += $abono->taxes_payed;
                        //Al total global se le sumará la cantidad negativa ya pagada de los abonos
                        $totalTransaccion += $abono->payed_qty;
                        $this->sumMethod($abono,$abono->payed_qty,0);
                        $metodos_prev[] = $abono->payment_method_id; // Agregar el atributo al arreglo
                    }
                }
                //Verificación de los abonos de propinas existentes. Se resta las cantidades para evitar que aparezcan las actualizaciones en el corte después de recibir una propina
                $metodos_prev=[];
                $abonos = $transaccion->abonoPropinas()->orderBy('created_at', 'desc')->get();

                foreach($abonos as $abono){
                    if(!in_array($abono->payment_method_id,$metodos_prev)){
                        $this->sumMethod($abono,$abono->payed_qty,1);
                        $metodos_prev[] = $abono->payment_method_id; // Agregar el atributo al arreglo
                        $this->totalTips += $abono->payed_qty;
                    }
                }

                $this->totalCorte_neto += $total_taxes;
            }
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 15558Ventas"] );
        }
    }
    
    private function obtenerDatosCorte()
    {
        try{
            $now = Carbon::now()->format('Y-m-d H:i:s');
            $salon_id = Auth::user()->salon_id;
            $apertura = $this->getLatestOpening();
            $aperturaTime = $apertura->created_at->format('Y-m-d H:i:s');
            $this->caja_chica = $apertura->caja_chica;
            $gastos = gasto::where('salon_id',$salon_id)
                ->where('payment_method','Caja chica')
                ->whereBetween('created_at', [$aperturaTime, $now]);
            $this->gastos_qty = $gastos->sum('total');
            $this->gastos = $gastos->get();
            $this->ventas = venta::where('salon_id',$salon_id)
                ->whereBetween('created_at', [$aperturaTime, $now])
                ->where(function($query) {
                    $query->where('status', 'Pagada')
                          ->orWhere('status', 'Pendiente');
                })
                ->with('abonos','details','metodosPago.metodoPago','propinas.metodoPago','customer')->get();
            $this->citas = cita::where('salon_id', $salon_id)
                ->whereBetween('updated_at', [$aperturaTime, $now])
                ->where(function($query) {
                    $query->where('status', 'Pagada')
                          ->orWhere('status', 'Pendiente');
                })
                ->with('abonos', 'details', 'metodosPago.metodoPago', 'propinas.metodoPago','customer')
                ->get();
            $this->acumularTransacciones($this->ventas,0);
            $this->acumularTransacciones($this->citas,1);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 19760Ventas"] );
        }
    }

    public function corteCaja()
    {
        $this->clearCorte();
        $this->obtenerDatosCorte();
    }
    private function eliminarCaracteres($data)
    {
        try{
            // Elimina todos los caracteres que no sean números, puntos o comas
            $valorSinCaracter = preg_replace('/[^0-9.]/', '', $data);
            
            // Convierte el resultado a un float
            $valorNumerico = (float) $valorSinCaracter;
            
            // Verifica si el resultado es numérico
            if (!is_numeric($valorNumerico)) {
                $this->dispatchBrowserEvent('noty-error', ['msg' => "Corrija el valor numérico"]);
                return;
            } else {
                return $valorNumerico;
            }
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 51312Agenda"] );
        }
    }

    public function changeCash($qty)
    {
        try{
            $qty = $this->eliminarCaracteres($qty);
            $this->totalCashReal = $qty;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 308335Ventas"] );
        }
    }

    public function guardarCorte()
    {
        $this->setCorte();
        $this->clearCorte();
        $this->dispatchBrowserEvent('noty', ['msg' => 'CORTE DE CAJA GENERADO | APERTURE LA CAJA PARA VENDER PRODUCTOS Y/O SERVICIOS']);
        return redirect('informe-caja');
    }
    private function clearCorte()
    {
        try{
            $this->description='';
            $this->totalCorte=0;
            $this->totalCorte_neto=0;
            $this->ventasCorte=0;
            $this->serviciosCorte=0;
            $this->efectivoCorte=0;
            $this->tarjetaCorte=[];
            $this->tarjetaCortePropina=[];
            $this->cardCorte=0;
            $this->msiCorte=0;
            $this->puntosCorte=0;
            $this->tipsCorte=0;
            $this->comissionsCorte=0;
            $this->iva=0;
            $this->propinasEfectivo=0;
            $this->propinasMsi=0;
            $this->propinasTarjeta=0;
            $this->totalTips=0;

            $this->totalCashReal=0;
            $this->totalCardReal=0;
            $this->totalNFreal=0;
            $this->tarjetaCorteReal=[];
            $this->propinasEfectivoReal=0;
            $this->propinasCardReal=0;
            $this->propinasMsiReal=0;
            $this->tarjetaCortePropinaReal=[];
            $this->totalCorteReal=0;
            $this->totalTipsReal=0;
            $this->cartPendingMethods = new Collection;
            $this->cartPendingPropinas = new Collection;

            $this->caja_chica = 0;
            $this->caja_chica_real = 0;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 24062Ventas"] );
        }
    }
    
    private function setCorte()
    {
        try{
            foreach($this->cartPendingMethods as $pendingMethod){
                abono::create([
                    'venta_id' => $pendingMethod['venta_id'] ?? null,
                    'cita_id' => $pendingMethod['cita_id'] ?? null,
                    'payed_qty' => $pendingMethod['payed_qty'],
                    'total_debt' => $pendingMethod['total_debt'],
                    'taxes_payed' => $pendingMethod['taxes_payed'],
                    'payment_method_id' => $pendingMethod['payment_method_id'],
                ]);
            }
            foreach($this->cartPendingPropinas as $pendingMethod){
                abonoPropina::create([
                    'venta_id' => $pendingMethod['venta_id'] ?? null,
                    'cita_id' => $pendingMethod['cita_id'] ?? null,
                    'payed_qty' => $pendingMethod['payed_qty'],
                    'payment_method_id' => $pendingMethod['payment_method_id'],
                ]);
            }
            $corte = new caja_corte;
            $corte->description = $this->description;
            $corte->total_bruto = $this->totalCorte;
            $corte->total_neto = $this->totalCorte_neto;
            $corte->ganancia = $this->totalCorte_neto-$this->puntosCorte;
            $corte->total_ventas = $this->ventasCorte;
            $corte->total_servicios = $this->serviciosCorte;
            $corte->total_cash = $this->efectivoCorte;
            $corte->total_cash_real = $this->totalCashReal;
            $corte->total_cash = $this->efectivoCorte;
            $corte->total_cash_real = $this->totalCashReal;
            $corte->total_tarjeta = $this->cardCorte;
            $corte->total_tarjeta_real = $this->totalCardReal;
            $corte->total_NF = $this->msiCorte;
            $corte->total_NF_real = $this->totalNFreal;
            $corte->total_points = $this->puntosCorte;
            $corte->tips = $this->tipsCorte;
            $corte->propinas_efectivo = $this->propinasEfectivo;
            $corte->propinas_efectivo_real = $this->propinasEfectivoReal;
            $corte->propinas_banorte = $this->propinasMsi;
            $corte->propinas_banorte_real = $this->propinasMsiReal;
            $corte->propinas_tarjeta = $this->propinasTarjeta;
            $corte->propinas_tarjeta_real = $this->propinasCardReal;
            $corte->comissions = $this->comissionsCorte;
            $corte->user_id = Auth()->user()->id;
            $corte->caja_chica = $this->caja_chica;
            $corte->caja_chica_real = $this->caja_chica_real;
            $corte->gastos = $this->gastos_qty;
            $corte->save();
            $apertura = $this->getLatestOpening();
            $apertura->caja_corte_id = $corte->id;
            $apertura->save();
        }catch(\Throwable $th){
        }
    }
    public function cancelarCorte()
    {
        $this->clearCorte();
    }
    private function getLatestOpening()
    {
        try{
            $salon_id = Auth::user()->salon_id;
            return caja_apertura::whereHas('user', function ($query) use ($salon_id) {
                $query->where('salon_id', $salon_id);
            })
            ->latest('id')
            ->first();
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 9655Cortes"] );
        }
    }
    private function verificarApertura()
    {
        try{
            $apertura = $this->getLatestOpening();
            if ($apertura!=null && $apertura->caja_corte_id==null) {
                $this->isOpened = true;
            } else {
                $this->isOpened = false;
                $corte = $apertura->corteCaja;
                $totalCashReal = $corte->total_cash_real; 
                $efectivoCorte = $corte->total_cash;
                $propinasEfectivo = $corte->propinas_efectivo;
                $gastos = $corte->gastos;
                $this->cajaChica = $totalCashReal-$efectivoCorte-$propinasEfectivo+$gastos;
            }
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 9655Cortes"] );
        }
    }
    public function changeTerminalQty($type,$terminal,$qty)
    {
        try{
            if($type == 'terminal'){
                $this->terminales=$this->obtenerArreglo($terminal,$qty,$this->terminales);
            }elseif($type == 'terminal_propina'){
                $this->terminales_propina=$this->obtenerArreglo($terminal,$qty,$this->terminales_propina);
            }
            $this->calculateTotal();
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1442369Agenda"] );
        }
    }
    
    public function cleanFormasPago()
    {
        $this->description='';
        $this->total_cash_real=0;
        $this->total_NF_real=0;
        $this->propinas_efectivo_real=0;
        $this->propinas_msi_real=0;
        $this->total_fp=0;
        $this->total_p=0;
    }
    private function obtenerArreglo($terminal,$qty,$arr)
    {
        try{
            if (array_key_exists($terminal, $arr)) {
                $arr[$terminal]['real'] = $qty;
                return $arr;
            } else {
                $this->dispatchBrowserEvent('noty-error', ['msg' => "No existe la terminal"]);
                return;
            }
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1454369Agenda"] );
        }
    }
    public function changeQty($type,$forma_pago,$qty)
    {
        try{
            if($type == 'forma_pago'){
                if($forma_pago == 'cash'){
                    $this->total_cash_real = $qty;
                }elseif($forma_pago == 'NF'){
                    $this->total_NF_real = $qty;  
                } 
            }elseif($type == 'propina'){
                if($forma_pago == 'cash'){
                    $this->propinas_efectivo_real = $qty;
                }elseif($forma_pago == 'NF'){
                    $this->propinas_msi_real = $qty;  
                } 
            }
            $this->calculateTotal();
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1468369Agenda"] );
        }
    }
    
    private function calculateTotal()
    {
        try{
            $this->total_fp=0;
            $this->total_p=0;
            $this->total_fp += $this->total_cash_real;
            $this->total_fp += $this->total_NF_real;
            foreach($this->terminales as $nombre_terminal => $cantidades){
                $this->total_fp += $cantidades['real'];
            }

            $this->total_p += $this->propinas_efectivo_real;
            $this->total_p += $this->propinas_msi_real;
            foreach($this->terminales_propina as $nombre_terminal => $cantidades){
                $this->total_p += $cantidades['real'];
            }
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1500369Agenda"] );
        }
    }
    public function StoreCorte()
    {
        $this->setCorte();
    }
}