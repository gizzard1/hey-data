
<div class="container-fluid">

    <!-- Métricas -->
    <div class="row mb-4">
        <div class="col-lg-2-4 col-md-4 col-sm-6 mb-3">
        <div class="metric-card d-flex justify-content-between">
            <div>
            <div class="text-muted">Ingresos del Periodo</div>
            <h4>${{ number_format($totales['total_incomes'] - $totales['gastos_a'] - $totales['gastos_n'] ?? 0, 2) }}</h4>
            <div class="trend">{{ $comparison_table['incomes'] }} vs periodo anterior</div>
            </div>
            
            <div class="metric-icon">$</div>
        </div>
        </div>
        <div class="col-lg-2-4 col-md-4 col-sm-6 mb-3">
        <div class="metric-card d-flex justify-content-between">
            <div>
            <div class="text-muted">Citas Pendientes</div>
            <h4>{{ $totales['total_citas_pendientes'] ?? 0 }}</h4>
            <div class="trend">{{ $comparison_table['pending_dates'] }} vs periodo anterior</div>
            </div>
            <div class="metric-icon">
                <i title="Agenda"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar-month" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="white" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                <path d="M16 3v4" />
                <path d="M8 3v4" />
                <path d="M4 11h16" />
                <path d="M7 14h.013" />
                <path d="M10.01 14h.005" />
                <path d="M13.01 14h.005" />
                <path d="M16.015 14h.005" />
                <path d="M13.015 17h.005" />
                <path d="M7.01 17h.005" />
                <path d="M10.01 17h.005" />
                </svg></i>
            </div>
        </div>
        </div>
        <div class="col-lg-2-4 col-md-4 col-sm-6 mb-3">
        <div class="metric-card d-flex justify-content-between">
            <div>
            <div class="text-muted">Clientes Nuevos</div>
            <h4>{{ $totales['clientes_nuevos'] ?? 0 }}</h4>
            <div class="trend">{{ $comparison_table['new_custs'] }} vs periodo anterior</div>
            </div>
            <div class="metric-icon">
                <i title="Clientes"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-users" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="white" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                </svg></i>
            </div>
        </div>
        </div>
        <div class="col-lg-2-4 col-md-4 col-sm-6 mb-3">
        <div class="metric-card d-flex justify-content-between">
            <div>
            <div class="text-muted">Servicios del Periodo</div>
            <h4>{{ $totales['total_citas_pagadas'] ?? 0 }}</h4>
            <div class="trend">{{ $comparison_table['dates'] }} vs periodo anterior</div>
            </div>
            <div class="metric-icon">
                <svg width="24" height="24" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M40 8L16.24 31.76M28.94 28.96L40 40M16.24 16.24L24 24M18 12C18 15.3137 15.3137 18 12 18C8.68629 18 6 15.3137 6 12C6 8.68629 8.68629 6 12 6C15.3137 6 18 8.68629 18 12ZM18 36C18 39.3137 15.3137 42 12 42C8.68629 42 6 39.3137 6 36C6 32.6863 8.68629 30 12 30C15.3137 30 18 32.6863 18 36Z" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>
        </div>
        <div class="col-lg-2-4 col-md-4 col-sm-6 mb-3">
        <div class="metric-card d-flex justify-content-between">
            <div>
            <div class="text-muted">Ventas del Periodo</div>
            <h4>{{ $totales['total_ventas_pagadas'] ?? 0 }}</h4>
            <div class="trend">{{ $comparison_table['sales'] }} vs periodo anterior</div>
            </div>
            <div class="metric-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-cash-register"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M21 15h-2.5c-.398 0 -.779 .158 -1.061 .439c-.281 .281 -.439 .663 -.439 1.061c0 .398 .158 .779 .439 1.061c.281 .281 .663 .439 1.061 .439h1c.398 0 .779 .158 1.061 .439c.281 .281 .439 .663 .439 1.061c0 .398 -.158 .779 -.439 1.061c-.281 .281 -.663 .439 -1.061 .439h-2.5" /><path d="M19 21v1m0 -8v1" /><path d="M13 21h-7c-.53 0 -1.039 -.211 -1.414 -.586c-.375 -.375 -.586 -.884 -.586 -1.414v-10c0 -.53 .211 -1.039 .586 -1.414c.375 -.375 .884 -.586 1.414 -.586h2m12 3.12v-1.12c0 -.53 -.211 -1.039 -.586 -1.414c-.375 -.375 -.884 -.586 -1.414 -.586h-2" /><path d="M16 10v-6c0 -.53 -.211 -1.039 -.586 -1.414c-.375 -.375 -.884 -.586 -1.414 -.586h-4c-.53 0 -1.039 .211 -1.414 .586c-.375 .375 -.586 .884 -.586 1.414v6m8 0h-8m8 0h1m-9 0h-1" /><path d="M8 14v.01" /><path d="M8 17v.01" /><path d="M12 13.99v.01" /><path d="M12 17v.01" /></svg>
            </div>
        </div>
        </div>
    </div>

    <div class="row mb-4">

        <!-- Gráfica -->
        <div class="col-lg-7 mb-4">
        <div class="panel">
            <h5 class="mb-4">Ingresos por método de pago</h5>
            <div class="row align-items-center">
            <div class="col-md-6 text-center">
                <canvas id="donutChart" width="260" height="260"></canvas>
            </div>
            <div class="col-md-6" id="legend"></div>
            </div>
        </div>
        </div>

        <!-- Próximas citas -->
        <div class="col-lg-5 mb-4">
        <div class="panel">
            <div class="d-flex justify-content-between mb-3">
            <h5>Próximas citas</h5>
            <div class="dropdown">
                <button class="btn btn-sm dropdown-toggle"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Ver</button>
                <div class="dropdown-menu p-3" aria-labelledby="showFilters">
                    <li class="orderByMenu">
                        <a onclick="showDatesByStatus('Agendada')">Agendadas</a>
                        <a onclick="showDatesByStatus('Pendiente')">Pendientes</a>
                        <a onclick="showDatesByStatus('Pagada')">Pagadas</a>
                        <a onclick="showDatesByStatus('Cancelada')">Canceladas</a>
                    </li>
                </div>
            </div>
            </div>

            <div class="list-group panel-table">
                @forelse($nextDates as $date)
                    @foreach($date->details as $detail)
                        <div class="list-group-item justify-content-between status-{{ $date->status }}" style="display: none">
                            <div><strong>{{ $detail->start }}</strong> {{ $date->customer?->first_name }} {{ $date->customer?->last_name }}<br>
                                <small cl>{{ $detail->servicio->name }} · Con: {{ $detail->empleado?->first_name }} {{ $detail->empleado?->last_name }}</small>
                            </div>
                            <span class="badge-status {{ $date->status }}">{{ $date->status }}</span>
                        </div>
                    @endforeach
                @empty
                    <div class="list-group-item text-center">
                        No hay citas próximas
                    </div>
                @endforelse
            </div>
        </div>
        </div>

    </div>

    <div class="row">

        <!-- HISTÓRICO / GRÁFICA -->
        <div class="col-lg-7 mb-4">
        <div class="panel">
            <h4>Histórico</h4>
            <div class="subtitle">Ingresos del Periodo</div>

            <canvas id="barChart" height="300" width="600"></canvas>
        </div>
        </div>

        <!-- TABLA -->
        <div class="col-lg-5 mb-4">
        <div class="panel">
            <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4>Histórico</h4>
                <div class="subtitle">Servicios más solicitados</div>
            </div>
            <div class="btn-group toggle-btns">
                <button class="btn btn-outline-secondary btn-sm" onclick="setView('service')">Servicio</button>
                <button class="btn btn-outline-secondary btn-sm" onclick="setView('category')">Categoría</button>
            </div>
            </div>
            <div class="ranking-table">
                <table class="table table-fixed-sm">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>%</th>
                        <th>Monto</th>
                    </tr>
                </thead>
                <tbody id="tableBody"></tbody>
                </table>
            </div>

        </div>
        </div>

    </div>
</div>

<script>
    /* ==============================
    TABLA DINÁMICA
    ============================== */

    let currentView = 'service';
    let serviceData = [];
    let categoryData = [];
        
    function renderTable(data) {
        const tbody = document.getElementById('tableBody');
        tbody.innerHTML = '';

        data.forEach((item, index) => {
        tbody.innerHTML += `
            <tr>
            <td>${index + 1}</td>
            <td class="nombre-column">${item.name}</td>
            <td>${item.percent.toFixed(2)} %</td>
            <td>$${item.qty}</td>
            </tr>
        `;
        });
    }

    let lastStatus = null;

    function setView(type) {
        currentView = type;
        var data = type === 'service' ? serviceData : categoryData;
        renderTable(data || []);
    }

    function showDatesByStatus(status) {
        const dates = document.querySelectorAll('.status-'+status);
        dates.forEach(d => {
            d.style.display = 'flex';
        });

        const datesToHide = document.querySelectorAll('.status-'+lastStatus);
        datesToHide.forEach(d => {
            d.style.display = 'none'
        });

        lastStatus = status;
    }
</script>



<style>
.ranking-table {
    height: 34dvh;
    overflow: auto;
    display: flex;
    margin: 2dvh 0 0 0;
}
.panel {
    background:#fff;
    border-radius:18px;
    border:1px solid #e4e6ef;
    padding:24px;
    height: 100%;
}

.subtitle { color:#666; font-size:15px; }
.date { color:#999; font-size:13px; margin-bottom:20px; }

canvas { max-width:100%; }

.table tbody tr:nth-child(odd) { background:#f7fbfb; }

.toggle-btns .btn {
    font-size:13px;
}
.metric-card {
    background:#fff;
    border-radius:14px;
    padding:20px;
    border:1px solid #e4e6ef;
    height: 100%;
}

.metric-icon {
    width:42px;
    height:42px;
    border-radius:10px;
    background:#6b7a99;
    color:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:500;
}

.trend {
    font-size:13px;
    color:#6c8cff;
}

.list-group-item.d-flex.justify-content-between.text-center {
    align-items: anchor-center;
}
.badge-status {
    border-radius: 30px;
    padding: 0;
    font-size: 12px;
    font-weight: 500;
    text-align: center;
    align-content: center;
    color: #fff;
    min-width: 10dvh;
    height: 3dvh;
}
.Agendada { background:#4978BC; }
.Pagada { background:#28A745; }
.Confirmada { background:#4978BC; }
.Pendiente { background:#FFAB2D; }
.Cancelada { background:#E63946; }

.legend-item {
    display:flex;
    align-items:center;
    justify-content:space-between;
    margin-bottom:10px;
    font-size:14px;
}

.legend-left {
    display:flex;
    align-items:center;
    gap:8px;
}

.legend-color {
    width:18px;
    height:10px;
    border-radius:6px;
}

/* Limitar ancho máximo de la columna nombre */
.nombre-column {
    max-width: 15dvh;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Clase personalizada para 5 columnas iguales */
@media (min-width: 1200px) {
    .col-lg-2-4 {
        flex: 0 0 20%;
        max-width: 20%;
    }
}

@media (max-width: 1199px) and (min-width: 768px) {
    .col-md-4 {
        flex: 0 0 33.333333%;
        max-width: 33.333333%;
    }
}

@media (max-width: 767px) {
    .col-sm-6 {
        flex: 0 0 50%;
        max-width: 50%;
    }
}
</style>