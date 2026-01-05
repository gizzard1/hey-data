            @foreach($metodosPago as $metodo)
                <tr>
                    <td class="cantidad"></td>
                    <td class="producto">{{ $metodo->metodoPago->Payment_method }}</td>
                    <td class="precio">@if($metodo->tipo=='Porcentaje') {{ number_format($metodo->amount,2,'.') }}% @elseif($metodo->tipo=='Cantidad') ${{ number_format($metodo->amount,2,'.',',') }} @endif</td>
                </tr>
            @endforeach
        </tbody>
        @if(isset($this->info->customer->tarjetaPuntos))
        <p class="centrado">
            Usted cuenta con: {{ $this->info->customer->tarjetaPuntos->balance ?? 0 }} puntos
        </p>
        @endif
    </table>
    <p class="centrado" style="margin-top: 1rem;">Fecha: {{ date_format($this->info->created_at,('d-m-Y H:i')) }}</p>
    <p class="centrado" style="margin-bottom: 10rem;">¡GRACIAS POR SU COMPRA!
        @if($info->salon->webPage !== null)
        <br>Visita nuestra web: {{ $info->salon->webPage }}
        @endif
        @if($info->salon->facebook !== null)
        <br>Facebook: {{  $info->salon->facebook }}
        @endif
        @if($info->salon->instagram !== null)
        <br>Instagram: {{  $info->salon->instagram }}
        @endif
        @if($info->salon->webPage !== null)
        <br>Youtube: {{  $info->salon->webPage }}
        @endif
        @if($info->salon->tiktok !== null)
        <br>Tiktok: {{  $info->salon->tiktok}}
        @endif
    </p>
</div>
