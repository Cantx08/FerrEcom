@extends('layouts.admin')

@section('page_title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="dashboardTabs()">

    <!-- Header Actions (Tabs & Filter) -->
    <div class="mb-8 flex flex-col sm:flex-row justify-between items-center gap-4">
        <!-- Segmented Tabs Navigation -->
        <div class="bg-white p-1.5 rounded-2xl shadow-sm border border-slate-200 inline-flex space-x-1 relative w-full sm:w-auto overflow-x-auto scrollbar-hide">
            <button @click="tab = 'resumen'" :class="{'bg-sky-900 text-white shadow-md': tab === 'resumen', 'text-slate-500 hover:text-slate-700 hover:bg-slate-50': tab !== 'resumen'}" class="px-6 py-2.5 rounded-xl font-semibold text-sm transition-all duration-300 ease-in-out flex items-center gap-2 whitespace-nowrap">
                <i class="fa-solid fa-chart-pie"></i> Resumen
            </button>
            <button @click="tab = 'ventas'" :class="{'bg-sky-900 text-white shadow-md': tab === 'ventas', 'text-slate-500 hover:text-slate-700 hover:bg-slate-50': tab !== 'ventas'}" class="px-6 py-2.5 rounded-xl font-semibold text-sm transition-all duration-300 ease-in-out flex items-center gap-2">
                <i class="fa-solid fa-cash-register"></i> Ventas
            </button>
            <button @click="tab = 'compras'" :class="{'bg-sky-900 text-white shadow-md': tab === 'compras', 'text-slate-500 hover:text-slate-700 hover:bg-slate-50': tab !== 'compras'}" class="px-6 py-2.5 rounded-xl font-semibold text-sm transition-all duration-300 ease-in-out flex items-center gap-2">
                <i class="fa-solid fa-truck-ramp-box"></i> Pedidos
            </button>
            
        </div>

        <!-- Period Filter -->
        <div class="flex items-center gap-2 shrink-0">
            <label for="periodFilter" class="text-sm font-semibold text-slate-500">Filtrar por:</label>
            <select id="periodFilter" @change="window.location.href = '?period=' + $event.target.value" class="bg-white border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl focus:ring-orange-500 focus:border-orange-500 block p-2 cursor-pointer shadow-sm">
                <option value="hoy" {{ $period == 'hoy' ? 'selected' : '' }}>Hoy</option>
                <option value="semana" {{ $period == 'semana' ? 'selected' : '' }}>Esta Semana</option>
                <option value="mes" {{ $period == 'mes' ? 'selected' : '' }}>Este Mes</option>
                <option value="anio" {{ $period == 'anio' ? 'selected' : '' }}>Este Año</option>
            </select>
        </div>
    </div>

    <!-- TAB: Resumen General -->
    <div x-show="tab === 'resumen'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" style="display: none;">
        
        <!-- Action Buttons -->
        <div class="flex justify-end gap-2 mb-4">
            <a href="{{ route('dashboard.export_ventas', ['period' => $period]) }}" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-green-700 transition shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-file-excel"></i> Exportar Excel
            </a>
            <a href="{{ route('dashboard.export_ventas_pdf', ['period' => $period]) }}" class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-red-700 transition shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-file-pdf"></i> Exportar PDF
            </a>
        </div>
        
        <!-- KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Total Ventas -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm relative overflow-hidden group">
                <div class="relative z-10 flex justify-between items-start">
                    <div>
                        <p class="text-sm font-semibold text-slate-500 mb-1">Total Ventas</p>
                        <h3 class="text-3xl font-black text-slate-800">${{ number_format($totalVentas, 2) }}</h3>
                        <div class="flex items-center mt-2 space-x-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold {{ $ventasTendencia >= 0 ? 'text-green-700' : 'text-red-700' }}">
                                <i class="fa-solid {{ $ventasTendencia >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }} mr-1"></i>
                                {{ number_format(abs($ventasTendencia), 1) }}%
                            </span>
                            <span class="text-xs text-slate-400 font-medium">vs periodo anterior</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 flex items-center justify-center text-green-600">
                        <i class="fa-solid fa-sack-dollar text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Compras -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm relative overflow-hidden group">
                <div class="relative z-10 flex justify-between items-start">
                    <div>
                        <p class="text-sm font-semibold text-slate-500 mb-1">Total Compras</p>
                        <h3 class="text-3xl font-black text-slate-800">${{ number_format($totalCompras, 2) }}</h3>
                    </div>
                    <div class="w-12 h-12 flex items-center justify-center text-blue-600">
                        <i class="fa-solid fa-cart-flatbed text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Margen Bruto -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm relative overflow-hidden group">
                <div class="relative z-10 flex justify-between items-start">
                    <div>
                        <p class="text-sm font-semibold text-slate-500 mb-1">Balance Bruto</p>
                        <h3 class="text-3xl font-black {{ $balance >= 0 ? 'text-slate-800' : 'text-red-600' }}">${{ number_format($balance, 2) }}</h3>
                    </div>
                    <div class="w-12 h-12 flex items-center justify-center text-orange-600">
                        <i class="fa-solid fa-scale-balanced text-xl"></i>
                    </div>
                </div>
            </div>


        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Area Chart -->
            <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-slate-800 text-lg">Flujo de Caja (Últimos 6 periodos)</h3>
                </div>
                <div id="cashflowChart" class="w-full h-80"></div>
            </div>
            
            <!-- Secondary Donut Chart -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-slate-800 text-lg">Distribución de Ventas (Top 5)</h3>
                </div>
                <div id="salesDistributionChart" class="w-full h-80 flex items-center justify-center"></div>
            </div>
        </div>
    </div>

    <!-- TAB: Ventas a Clientes -->
    <div x-show="tab === 'ventas'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" style="display: none;">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
                <h3 class="font-bold text-slate-800 text-lg">Registro de Ventas</h3>
                <div class="flex items-center gap-2" id="ventasButtonsContainer">
                    <!-- DataTables buttons will be injected here -->
                </div>
            </div>
            <div class="p-6 overflow-x-auto">
                <table id="ventasTable" class="w-full text-left text-sm text-slate-600 stripe hover">
                    <thead class="bg-slate-50 text-slate-500 font-semibold uppercase text-xs">
                        <tr>
                            <th>N° Venta</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Vendedor</th>
                            <th>Método Pago</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB: Pedidos a Proveedores -->
    <div x-show="tab === 'compras'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" style="display: none;">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
                <h3 class="font-bold text-slate-800 text-lg">Detalle de Pedidos</h3>
                <div class="flex items-center gap-2" id="comprasButtonsContainer">
                    <!-- DataTables buttons will be injected here -->
                </div>
            </div>
            <div class="p-6 overflow-x-auto">
                <table id="comprasTable" class="w-full text-left text-sm text-slate-600 stripe hover">
                    <thead class="bg-slate-50 text-slate-500 font-semibold uppercase text-xs">
                        <tr>
                            <th>N° Pedido</th>
                            <th>Fecha</th>
                            <th>Proveedor</th>
                            <th>Bodeguero</th>
                            <th>Estado</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>



</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('dashboardTabs', () => ({
            tab: 'resumen',
            init() {
                // Initialize charts once on load
                this.loadCharts();
                // Initialize DataTables
                this.initDataTables();
            },
            loadCharts() {
                const urlParams = new URLSearchParams(window.location.search);
                const period = urlParams.get('period') || 'mes';
                
                fetch('{{ route("dashboard.chart_data") }}?period=' + period)
                    .then(response => response.json())
                    .then(data => {
                        // Area Chart (Cashflow)
                        var areaOptions = {
                            series: data.area.series,
                            chart: { type: 'area', height: 320, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
                            colors: ['#10b981', '#ef4444'], // Green for Ventas, Red for Compras
                            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [50, 100] } },
                            dataLabels: { enabled: false },
                            stroke: { curve: 'smooth', width: 2 },
                            xaxis: { categories: data.area.categories, axisBorder: { show: false }, axisTicks: { show: false } },
                            yaxis: { labels: { formatter: (value) => { return "$" + value.toFixed(0); } } },
                            tooltip: { y: { formatter: (value) => { return "$" + value.toFixed(2); } } },
                            grid: { strokeDashArray: 4, borderColor: '#e2e8f0' }
                        };
                        new ApexCharts(document.querySelector("#cashflowChart"), areaOptions).render();

                        // Donut Chart (Sales Distribution)
                        var donutOptions = {
                            series: data.donut.series,
                            labels: data.donut.labels,
                            chart: { type: 'donut', height: 320, fontFamily: 'Inter, sans-serif' },
                            colors: ['#0369a1', '#ea580c', '#8b5cf6', '#10b981', '#f59e0b'],
                            stroke: { width: 0 },
                            dataLabels: { enabled: false },
                            plotOptions: { pie: { donut: { size: '75%', labels: { show: true, name: { show: true }, value: { show: true, formatter: (val) => "$" + Number(val).toFixed(2) } } } } },
                            legend: { position: 'bottom' }
                        };
                        new ApexCharts(document.querySelector("#salesDistributionChart"), donutOptions).render();
                    });
            },
            initDataTables() {
                // Common DataTable Config
                $.extend(true, $.fn.dataTable.defaults, {
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                    },
                    dom: '<"flex justify-between items-center mb-4"lf>rt<"flex justify-between items-center mt-4"ip>',
                    pageLength: 10
                });

                // Ventas DataTable
                var ventasTable = $('#ventasTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route("dashboard.ventas_data") }}',
                    columns: [
                        { data: 'id', name: 'id' },
                        { data: 'created_at', name: 'created_at' },
                        { data: 'cliente_nombre', name: 'cliente_nombre', searchable: false },
                        { data: 'vendedor_nombre', name: 'vendedor_nombre', searchable: false },
                        { data: 'metodo_pago', name: 'metodo_pago' },
                        { data: 'total', name: 'total' }
                    ],
                    order: [[1, 'desc']],
                    buttons: [
                        { extend: 'excelHtml5', text: '<i class="fa-solid fa-file-excel mr-1"></i> Excel', className: 'bg-green-600 text-white px-3 py-1.5 rounded-lg text-sm font-semibold hover:bg-green-700 focus:outline-none transition border-none shadow-sm' },
                        { extend: 'pdfHtml5', text: '<i class="fa-solid fa-file-pdf mr-1"></i> PDF', className: 'bg-red-600 text-white px-3 py-1.5 rounded-lg text-sm font-semibold hover:bg-red-700 focus:outline-none transition border-none shadow-sm' }
                    ]
                });
                ventasTable.buttons().container().appendTo('#ventasButtonsContainer');

                // Compras DataTable
                var comprasTable = $('#comprasTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route("dashboard.compras_data") }}',
                    columns: [
                        { data: 'id', name: 'id' },
                        { data: 'created_at', name: 'created_at' },
                        { data: 'proveedor_nombre', name: 'proveedor_nombre', searchable: false },
                        { data: 'bodeguero_nombre', name: 'bodeguero_nombre', searchable: false },
                        { data: 'estado', name: 'estado' },
                        { data: 'total', name: 'total' }
                    ],
                    order: [[1, 'desc']],
                    buttons: [
                        { extend: 'excelHtml5', text: '<i class="fa-solid fa-file-excel mr-1"></i> Excel', className: 'bg-green-600 text-white px-3 py-1.5 rounded-lg text-sm font-semibold hover:bg-green-700 focus:outline-none transition border-none shadow-sm' },
                        { extend: 'pdfHtml5', text: '<i class="fa-solid fa-file-pdf mr-1"></i> PDF', className: 'bg-red-600 text-white px-3 py-1.5 rounded-lg text-sm font-semibold hover:bg-red-700 focus:outline-none transition border-none shadow-sm' }
                    ]
                });
                comprasTable.buttons().container().appendTo('#comprasButtonsContainer');
            }
        }));
    });
</script>
<style>
    /* DataTables Custom Tailwind Styling Adjustments */
    .dataTables_wrapper .dataTables_length select, .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        padding: 0.25rem 0.75rem;
        font-size: 0.875rem;
        outline: none;
    }
    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #ea580c;
        box-shadow: 0 0 0 1px #ea580c;
    }
    table.dataTable.no-footer {
        border-bottom: 1px solid #e2e8f0;
    }
    table.dataTable thead th {
        border-bottom: 2px solid #e2e8f0;
    }
    .dt-buttons {
        display: flex;
        gap: 0.5rem;
    }
    .dt-button { margin-bottom: 0 !important; }
</style>
@endpush
@endsection
