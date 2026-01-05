<div style="width:100%; margin:20px auto;">
    <div class="d-flex" style="justify-content:space-between; margin-bottom:10px;">
        <div>
            <strong>Servicios consumidos:</strong> {{ count($rankingS['items'] ?? 0) }}<br>
            <span>💡 ${{ number_format($rankingS['total'],2,'.',',') }}</span>
        </div>
        <div>
            <strong>Total consumido:</strong><br>
            <span>${{ number_format($rankingP['total']+$rankingS['total'],2,'.',',') }}</span>
        </div>
        <div style="text-align:right;">
            <strong>Productos comprados:</strong> {{ count($rankingP['items'] ?? 0) }}<br>
            <span>🛍️ ${{ number_format($rankingP['total'],2,'.',',') }}</span>
        </div>
    </div>

    <div class="d-flex" style="width:100%; height:20px; background:#eee; border-radius:4px; overflow:hidden;justify-content: space-between;">
        <div style="width:{{ $totalS }}%; background:#E83E8C;" class="bar"></div>
        <div style="width:{{ $totalP }}%; background:#6F42C1;" class="bar"></div>
    </div>

    <div class="d-flex" style="justify-content:space-between; margin-top:6px; font-size:14px;">
        <span>{{ number_format($totalS ?? 0,2,'.') }}% de participación</span>
        <span>{{ number_format($totalP ?? 0,2,'.') }}% de participación</span>
    </div>
</div>

<style>
    .bar{
        transition: width 0.5s ease-in; /* animación suave */
    }
</style>