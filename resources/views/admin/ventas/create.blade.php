@extends('layouts.admin')

@section('page_title', 'Registrar Nueva Venta (POS)')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-800 text-lg">
                Punto de Venta (POS)
            </h3>
            <a href="{{ route('ventas.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700 transition">
                <i class="fa-solid fa-chevron-left mr-1"></i> Volver a Ventas
            </a>
        </div>
        
        <form action="{{ route('ventas.store') }}" method="POST" class="p-6" id="venta-form">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Columna Izquierda: Productos -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <div>
                        <div class="flex justify-between items-center border-b border-slate-100 pb-2 mb-4">
                            <h4 class="font-bold text-sky-900">Productos de la Venta</h4>
                            <button type="button" id="btn-add-producto" class="text-xs bg-orange-100 text-orange-700 hover:bg-orange-200 px-3 py-1.5 rounded-lg font-bold transition shadow-sm">
                                <i class="fa-solid fa-plus mr-1"></i> Añadir Ítem
                            </button>
                        </div>

                        @error('productos') <div class="text-xs text-red-500 mb-3">{{ $message }}</div> @enderror

                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm" id="productos-table">
                                <thead>
                                    <tr class="border-b border-slate-200 bg-slate-50">
                                        <th class="p-3 font-semibold text-slate-600">Producto</th>
                                        <th class="p-3 font-semibold text-slate-600 w-24">Stock Disp.</th>
                                        <th class="p-3 font-semibold text-slate-600 w-32">Cantidad</th>
                                        <th class="p-3 font-semibold text-slate-600 w-32 text-right">Precio ($)</th>
                                        <th class="p-3 font-semibold text-slate-600 w-32 text-right">Subtotal ($)</th>
                                        <th class="p-3 font-semibold text-slate-600 w-16 text-center">Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="productos-tbody">
                                    <tr class="border-b border-slate-100 producto-row">
                                        <td class="p-2">
                                            <select name="productos[0][id]" required class="w-full rounded-lg border-slate-300 text-sm py-1.5 select-producto">
                                                <option value="">Buscar producto...</option>
                                                @foreach($productos as $prod)
                                                    <option value="{{ $prod->id }}" data-precio="{{ $prod->precio_venta }}" data-stock="{{ $prod->stock_actual }}">
                                                        {{ $prod->nombre }} - {{ $prod->sku }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="p-2 text-center text-xs font-bold text-slate-500 stock-text">
                                            -
                                        </td>
                                        <td class="p-2">
                                            <input type="number" name="productos[0][cantidad]" required min="1" step="1" value="1" class="w-full rounded-lg border-slate-300 text-sm py-1.5 input-cantidad text-center font-bold">
                                        </td>
                                        <td class="p-2 text-right text-slate-600 font-mono precio-text">
                                            0.00
                                        </td>
                                        <td class="p-2 text-right font-mono font-bold text-sky-700 subtotal-text">
                                            0.00
                                        </td>
                                        <td class="p-2 text-center">
                                            <button type="button" class="text-red-400 hover:text-red-600 btn-remove-row"><i class="fa-solid fa-times-circle"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha: Resumen y Pago -->
                <div class="space-y-6">
                    <div class="bg-slate-50 p-5 rounded-xl border border-slate-200">
                        <h4 class="font-bold text-sky-900 border-b border-slate-200 pb-2 mb-4">Datos de Facturación</h4>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Cédula / Documento *</label>
                                <div class="flex gap-2">
                                    <input type="text" id="cliente_documento" name="cliente_documento" required value="{{ old('cliente_documento') }}" placeholder="Ej. 1700000000" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm">
                                    <button type="button" id="btn-buscar-cliente" class="bg-sky-600 hover:bg-sky-700 text-white px-3 py-2 rounded-lg text-sm font-semibold transition">
                                        <i class="fa-solid fa-search"></i> Buscar
                                    </button>
                                </div>
                                <span id="search-msg" class="text-xs text-sky-600 mt-1 hidden"></span>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Razón Social / Nombres *</label>
                                <input type="text" id="cliente_nombre" name="cliente_nombre" required value="{{ old('cliente_nombre') }}" placeholder="Ej. Consumidor Final" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Dirección</label>
                                <input type="text" id="cliente_direccion" name="cliente_direccion" value="{{ old('cliente_direccion') }}" placeholder="Ej. Av. Principal" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Teléfono</label>
                                    <input type="text" id="cliente_telefono" name="cliente_telefono" value="{{ old('cliente_telefono') }}" placeholder="Ej. 0999999999" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Correo Electrónico</label>
                                    <input type="email" id="cliente_correo" name="cliente_correo" value="{{ old('cliente_correo') }}" placeholder="correo@ejemplo.com" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 p-5 rounded-xl border border-slate-200">
                        <h4 class="font-bold text-sky-900 border-b border-slate-200 pb-2 mb-4">Resumen de Venta</h4>
                        
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-slate-600 font-semibold">Total a Pagar:</span>
                            <span class="text-3xl font-bold text-orange-600 font-mono" id="total-text">$0.00</span>
                        </div>

                        <div class="space-y-3">
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Método de Pago *</label>
                            <select name="metodo_pago" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm font-bold text-slate-700">
                                <option value="efectivo" selected>💵 Efectivo</option>
                                <option value="transferencia">🏦 Transferencia Bancaria</option>
                            </select>
                        </div>

                        <div class="mt-6">
                            <button type="submit" class="w-full px-5 py-3 text-base font-bold text-white bg-sky-600 rounded-lg hover:bg-sky-700 transition shadow-md flex items-center justify-center gap-2">
                                <i class="fa-solid fa-check-circle"></i> Confirmar Venta
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Template para nueva fila (Oculto) -->
<template id="row-template">
    <tr class="border-b border-slate-100 producto-row bg-sky-50/30">
        <td class="p-2">
            <select name="productos[__INDEX__][id]" required class="w-full rounded-lg border-slate-300 text-sm py-1.5 select-producto">
                <option value="">Buscar producto...</option>
                @foreach($productos as $prod)
                    <option value="{{ $prod->id }}" data-precio="{{ $prod->precio_venta }}" data-stock="{{ $prod->stock_actual }}">
                        {{ $prod->nombre }} - {{ $prod->sku }}
                    </option>
                @endforeach
            </select>
        </td>
        <td class="p-2 text-center text-xs font-bold text-slate-500 stock-text">-</td>
        <td class="p-2">
            <input type="number" name="productos[__INDEX__][cantidad]" required min="1" step="1" value="1" class="w-full rounded-lg border-slate-300 text-sm py-1.5 input-cantidad text-center font-bold">
        </td>
        <td class="p-2 text-right text-slate-600 font-mono precio-text">0.00</td>
        <td class="p-2 text-right font-mono font-bold text-sky-700 subtotal-text">0.00</td>
        <td class="p-2 text-center">
            <button type="button" class="text-red-400 hover:text-red-600 btn-remove-row"><i class="fa-solid fa-trash"></i></button>
        </td>
    </tr>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let rowIndex = 1;
    const tbody = document.getElementById('productos-tbody');
    const template = document.getElementById('row-template').innerHTML;
    const btnAdd = document.getElementById('btn-add-producto');
    const totalText = document.getElementById('total-text');

    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('.producto-row').forEach(row => {
            const cant = parseFloat(row.querySelector('.input-cantidad').value) || 0;
            const select = row.querySelector('.select-producto');
            const option = select.options[select.selectedIndex];
            
            let precio = 0;
            if(option && option.value) {
                precio = parseFloat(option.getAttribute('data-precio')) || 0;
            }
            
            const sub = cant * precio;
            row.querySelector('.subtotal-text').textContent = sub.toFixed(2);
            total += sub;
        });
        totalText.textContent = '$' + total.toFixed(2);
    }

    function attachEvents(row) {
        const select = row.querySelector('.select-producto');
        const inputCant = row.querySelector('.input-cantidad');

        select.addEventListener('change', function() {
            const option = this.options[this.selectedIndex];
            if(option && option.value) {
                const precio = option.getAttribute('data-precio') || '0.00';
                const stock = option.getAttribute('data-stock') || '0';
                
                row.querySelector('.precio-text').textContent = parseFloat(precio).toFixed(2);
                row.querySelector('.stock-text').textContent = stock;
                inputCant.max = stock; // Set max for validation
            } else {
                row.querySelector('.precio-text').textContent = '0.00';
                row.querySelector('.stock-text').textContent = '-';
            }
            calculateTotal();
        });

        inputCant.addEventListener('input', function() {
            const option = select.options[select.selectedIndex];
            if(option && option.value) {
                const maxStock = parseFloat(option.getAttribute('data-stock')) || 0;
                if(parseFloat(this.value) > maxStock) {
                    alert('La cantidad no puede superar el stock disponible (' + maxStock + ')');
                    this.value = maxStock;
                }
            }
            calculateTotal();
        });

        row.querySelector('.btn-remove-row').addEventListener('click', function() {
            if (document.querySelectorAll('.producto-row').length > 1) {
                row.remove();
                calculateTotal();
            } else {
                alert('Debe haber al menos un producto en la venta.');
            }
        });
    }

    // Attach to initial row
    document.querySelectorAll('.producto-row').forEach(attachEvents);

    btnAdd.addEventListener('click', function() {
        const newRowHtml = template.replace(/__INDEX__/g, rowIndex);
        tbody.insertAdjacentHTML('beforeend', newRowHtml);
        const newRow = tbody.lastElementChild;
        attachEvents(newRow);
        rowIndex++;
    });
    // Lógica para buscar cliente por cédula
    const btnBuscar = document.getElementById('btn-buscar-cliente');
    const inputCedula = document.getElementById('cliente_documento');
    const msgBox = document.getElementById('search-msg');

    btnBuscar.addEventListener('click', async function() {
        const cedula = inputCedula.value.trim();
        if (!cedula) return;

        try {
            btnBuscar.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
            const response = await fetch(`/api/clientes/${cedula}`);
            const data = await response.json();
            
            msgBox.classList.remove('hidden');
            if (data.encontrado) {
                document.getElementById('cliente_nombre').value = data.datos.nombre || '';
                document.getElementById('cliente_direccion').value = data.datos.direccion || '';
                document.getElementById('cliente_telefono').value = data.datos.telefono || '';
                document.getElementById('cliente_correo').value = data.datos.correo || '';
                
                msgBox.textContent = "Cliente encontrado. Puedes modificar los datos.";
                msgBox.classList.remove('text-red-500');
                msgBox.classList.add('text-sky-600');
            } else {
                msgBox.textContent = "Cliente no registrado. Ingresa los datos manualmente.";
                msgBox.classList.remove('text-sky-600');
                msgBox.classList.add('text-red-500');
            }
        } catch (error) {
            console.error('Error buscando cliente:', error);
        } finally {
            btnBuscar.innerHTML = '<i class="fa-solid fa-search"></i> Buscar';
        }
    });

    // Validar en submit (no facturar monto 0 o sin items)
    document.getElementById('venta-form').addEventListener('submit', function(e) {
        if (document.querySelectorAll('.producto-row').length === 0) {
            e.preventDefault();
            alert('Debes agregar al menos un producto a la venta.');
        }
    });
});
</script>
@endsection
