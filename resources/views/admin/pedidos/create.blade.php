@extends('layouts.admin')

@section('page_title', 'Nuevo Pedido de Compra')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-800 text-lg">
                Generar Nuevo Pedido
            </h3>
            <a href="{{ route('pedidos.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700 transition">
                <i class="fa-solid fa-chevron-left mr-1"></i> Volver
            </a>
        </div>
        
        <form action="{{ route('pedidos.store') }}" method="POST" class="p-6" id="pedido-form">
            @csrf

            <div class="space-y-6">
                <!-- Información del Proveedor -->
                <div>
                    <h4 class="font-bold text-sky-900 border-b border-slate-100 pb-2 mb-4">Información del Proveedor</h4>
                    <div class="max-w-md">
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Seleccionar Proveedor *</label>
                        <select name="proveedor_id" id="proveedor_id" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm">
                            <option value="">-- Seleccione un proveedor --</option>
                            @foreach($proveedores as $prov)
                                <option value="{{ $prov->id }}" {{ old('proveedor_id', request('proveedor_id')) == $prov->id ? 'selected' : '' }}>{{ $prov->nombre_compania }}</option>
                            @endforeach
                        </select>
                        @error('proveedor_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Detalles del Pedido -->
                <div>
                    <div class="flex justify-between items-center border-b border-slate-100 pb-2 mb-4">
                        <h4 class="font-bold text-sky-900">Productos a Pedir</h4>
                        <button type="button" id="btn-add-producto" class="text-xs bg-sky-100 text-sky-700 hover:bg-sky-200 px-3 py-1.5 rounded-lg font-bold transition">
                            <i class="fa-solid fa-plus mr-1"></i> Añadir Fila
                        </button>
                    </div>

                    @error('productos') <div class="text-xs text-red-500 mb-3">{{ $message }}</div> @enderror

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm" id="productos-table">
                            <thead>
                                <tr class="border-b border-slate-200 bg-slate-50">
                                    <th class="p-3 font-semibold text-slate-600">Producto</th>
                                    <th class="p-3 font-semibold text-slate-600 w-32">Cantidad</th>
                                    <th class="p-3 font-semibold text-slate-600 w-40">Precio Unit. ($)</th>
                                    <th class="p-3 font-semibold text-slate-600 w-32 text-right">Subtotal</th>
                                    <th class="p-3 font-semibold text-slate-600 w-16 text-center">Acción</th>
                                </tr>
                            </thead>
                            <tbody id="productos-tbody">
                                <!-- Filas dinámicas irán aquí -->
                                <tr class="border-b border-slate-100 producto-row">
                                    <td class="p-2">
                                        <select name="productos[0][id]" required class="w-full rounded-lg border-slate-300 text-sm py-1.5 select-producto">
                                            <option value="">Seleccione producto</option>
                                            @foreach($productos as $prod)
                                                <option value="{{ $prod->id }}" data-proveedor="{{ $prod->proveedor_id }}" data-precio="{{ $prod->costo_unitario }}" style="display:none;">{{ $prod->nombre }} (SKU: {{ $prod->sku }})</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="p-2">
                                        <input type="number" name="productos[0][cantidad]" required min="1" step="1" value="1" class="w-full rounded-lg border-slate-300 text-sm py-1.5 input-cantidad">
                                    </td>
                                    <td class="p-2">
                                        <input type="number" name="productos[0][precio_unitario]" required min="0" step="0.01" value="0.00" class="w-full rounded-lg border-slate-300 text-sm py-1.5 input-precio">
                                    </td>
                                    <td class="p-2 text-right font-mono font-bold text-slate-700 subtotal-text">$0.00</td>
                                    <td class="p-2 text-center">
                                        <button type="button" class="text-red-400 hover:text-red-600 btn-remove-row"><i class="fa-solid fa-times-circle"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="p-3 text-right font-bold text-slate-700">TOTAL PEDIDO:</td>
                                    <td class="p-3 text-right font-bold text-sky-900 text-lg font-mono" id="total-text">$0.00</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-5 border-t border-slate-200 flex justify-end gap-3">
                <a href="{{ route('pedidos.index') }}" class="px-5 py-2.5 text-sm font-bold text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition shadow-sm">Cancelar</a>
                <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-sky-600 rounded-lg hover:bg-sky-700 transition shadow-sm flex items-center gap-2">
                    <i class="fa-solid fa-save"></i> Guardar Pedido
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Template para nueva fila (Oculto) -->
<template id="row-template">
    <tr class="border-b border-slate-100 producto-row">
        <td class="p-2">
            <select name="productos[__INDEX__][id]" required class="w-full rounded-lg border-slate-300 text-sm py-1.5 select-producto">
                <option value="">Seleccione producto</option>
                @foreach($productos as $prod)
                    <option value="{{ $prod->id }}" data-proveedor="{{ $prod->proveedor_id }}" data-precio="{{ $prod->costo_unitario }}" style="display:none;">{{ $prod->nombre }} (SKU: {{ $prod->sku }})</option>
                @endforeach
            </select>
        </td>
        <td class="p-2">
            <input type="number" name="productos[__INDEX__][cantidad]" required min="1" step="1" value="1" class="w-full rounded-lg border-slate-300 text-sm py-1.5 input-cantidad">
        </td>
        <td class="p-2">
            <input type="number" name="productos[__INDEX__][precio_unitario]" required min="0" step="0.01" value="0.00" class="w-full rounded-lg border-slate-300 text-sm py-1.5 input-precio">
        </td>
        <td class="p-2 text-right font-mono font-bold text-slate-700 subtotal-text">$0.00</td>
        <td class="p-2 text-center">
            <button type="button" class="text-red-400 hover:text-red-600 btn-remove-row"><i class="fa-solid fa-times-circle"></i></button>
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
            const precio = parseFloat(row.querySelector('.input-precio').value) || 0;
            const sub = cant * precio;
            row.querySelector('.subtotal-text').textContent = '$' + sub.toFixed(2);
            total += sub;
        });
        totalText.textContent = '$' + total.toFixed(2);
    }

    const proveedorSelect = document.getElementById('proveedor_id');

    function filterProductsBySupplier() {
        const proveedorId = proveedorSelect.value;
        document.querySelectorAll('.select-producto').forEach(select => {
            let hasValidSelection = false;
            Array.from(select.options).forEach(option => {
                if (option.value === "") {
                    option.style.display = 'block'; // Always show placeholder
                } else if (option.getAttribute('data-proveedor') === proveedorId) {
                    option.style.display = 'block';
                    if (option.selected) hasValidSelection = true;
                } else {
                    option.style.display = 'none';
                }
            });
            if (!hasValidSelection && select.value !== "") {
                select.value = ""; // Reset if current selection doesn't belong to new supplier
                const row = select.closest('tr');
                if (row) {
                    row.querySelector('.input-precio').value = '0.00';
                }
            }
        });
        calculateTotal();
    }

    proveedorSelect.addEventListener('change', filterProductsBySupplier);

    function attachEvents(row) {
        row.querySelector('.select-producto').addEventListener('change', function() {
            const option = this.options[this.selectedIndex];
            const precio = option.getAttribute('data-precio') || 0;
            row.querySelector('.input-precio').value = precio;
            calculateTotal();
        });

        row.querySelector('.input-cantidad').addEventListener('input', calculateTotal);
        row.querySelector('.input-precio').addEventListener('input', calculateTotal);

        row.querySelector('.btn-remove-row').addEventListener('click', function() {
            if (document.querySelectorAll('.producto-row').length > 1) {
                row.remove();
                calculateTotal();
            } else {
                alert('Debe haber al menos un producto en el pedido.');
            }
        });
    }

    // Attach to initial row if no prefilled
    document.querySelectorAll('.producto-row').forEach(attachEvents);

    // Handle Prefilled Products from backend
    const prefilledProducts = @json($prefilledProducts ?? []);
    if (prefilledProducts.length > 0) {
        // Trigger supplier change to show correctly
        filterProductsBySupplier();
        
        // Remove the default empty row
        document.querySelectorAll('.producto-row').forEach(row => row.remove());
        
        // Add rows for each prefilled product
        prefilledProducts.forEach(prod => {
            const tr = document.createElement('tr');
            tr.className = 'border-b border-slate-100 producto-row';
            tr.innerHTML = template.replace(/__INDEX__/g, rowIndex++);
            tbody.appendChild(tr);
            
            attachEvents(tr);
            
            // Set values
            const select = tr.querySelector('.select-producto');
            select.value = prod.id;
            
            const cantInput = tr.querySelector('.input-cantidad');
            cantInput.value = prod.cantidad;
            
            const precioInput = tr.querySelector('.input-precio');
            precioInput.value = prod.precio;
            
            // Force display option for this specific product to be visible since filter ran before it was added
            Array.from(select.options).forEach(opt => {
                if (opt.value == prod.id) {
                    opt.style.display = 'block';
                }
            });
        });
        
        calculateTotal();
    }

    btnAdd.addEventListener('click', function() {
        const newRowHtml = template.replace(/__INDEX__/g, rowIndex);
        tbody.insertAdjacentHTML('beforeend', newRowHtml);
        const newRow = tbody.lastElementChild;
        attachEvents(newRow);
        
        // Filter options for the new row based on current supplier
        const proveedorId = proveedorSelect.value;
        const select = newRow.querySelector('.select-producto');
        Array.from(select.options).forEach(option => {
            if (option.value === "") {
                option.style.display = 'block';
            } else if (option.getAttribute('data-proveedor') === proveedorId) {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        });

        rowIndex++;
    });
    
    // Initial filter if supplier is already selected (e.g. old input on error)
    if (proveedorSelect.value) {
        filterProductsBySupplier();
    }
});
</script>
@endsection
