@extends('layouts.admin')

@section('page_title', 'Gestión de Pedido #' . str_pad($pedido->id, 5, '0', STR_PAD_LEFT))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
<div class="mb-6">
    <a href="{{ route('pedidos.index') }}" class="text-slate-500 hover:text-sky-600 text-sm font-semibold transition-colors flex items-center gap-1">
        <i class="fa-solid fa-arrow-left"></i> Volver a Pedidos
    </a>
</div>

<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
    <div>
        <h3 class="font-black text-slate-800 text-2xl">
            Pedido #{{ str_pad($pedido->id, 5, '0', STR_PAD_LEFT) }}
            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-widest align-middle ml-2
                @if($pedido->estado->color() === 'success') bg-emerald-100 text-emerald-700
                @elseif($pedido->estado->color() === 'warning') bg-yellow-100 text-yellow-700
                @elseif($pedido->estado->color() === 'info') bg-sky-100 text-sky-700
                @elseif($pedido->estado->color() === 'primary') bg-blue-100 text-blue-700
                @elseif($pedido->estado->color() === 'danger') bg-red-100 text-red-700
                @else bg-slate-200 text-slate-600 @endif">
                {{ $pedido->estado->etiqueta() }}
            </span>
        </h3>
        <p class="text-sm text-slate-500 mt-2">
            Proveedor: <span class="font-bold text-slate-700">{{ $pedido->proveedor->nombre_compania ?? 'Eliminado' }}</span> | 
            Emitido: <span class="font-bold text-slate-700">{{ $pedido->created_at->format('d/m/Y H:i') }}</span>
        </p>
    </div>
    <div class="text-right">
        <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">Total</p>
        <p class="font-black text-3xl text-sky-900">${{ number_format($pedido->total, 2) }}</p>
    </div>
</div>

{{-- Stepper visual --}}
<div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 mb-6 overflow-x-auto scrollbar-hide">
    <div class="min-w-[600px]">
        @php
            $steps = [
                'emitido' => ['label' => 'Emitido', 'icon' => 'fa-file-invoice'],
                'aceptado' => ['label' => 'Revisado', 'icon' => 'fa-clipboard-check'],
                'en_preparacion' => ['label' => 'En Preparación', 'icon' => 'fa-thumbs-up'],
                'en_transito' => ['label' => 'Enviado', 'icon' => 'fa-truck-fast'],
                'recibido_completado' => ['label' => 'Entregado', 'icon' => 'fa-boxes-packing']
            ];
            $keys = array_keys($steps);
            $currentStepIndex = array_search($pedido->estado->value, $keys);
            if ($currentStepIndex === false && in_array($pedido->estado->value, ['pagado'])) { $currentStepIndex = 4; }
        @endphp
        <div class="flex items-center justify-between relative">
            <div class="absolute left-8 right-8 top-5 -translate-y-1/2 h-1 bg-slate-100 z-0 rounded-full"></div>
            <div class="absolute left-8 top-5 -translate-y-1/2 h-1 bg-orange-500 z-0 rounded-full transition-all duration-500" style="width: calc({{ ($currentStepIndex / 4) * 100 }}% - {{ $currentStepIndex == 0 ? 0 : ($currentStepIndex == 4 ? 64 : 32) }}px)"></div>
            
            @foreach($steps as $key => $data)
                @php 
                    $index = $loop->index;
                    $isCompleted = $index <= $currentStepIndex;
                    $isCurrent = $index == $currentStepIndex;
                @endphp
                <div class="relative z-10 flex flex-col items-center gap-2">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 border-2 {{ $isCompleted ? 'bg-orange-500 border-orange-500 text-white shadow-md' : 'bg-white border-slate-200 text-slate-300' }} {{ $isCurrent ? 'ring-4 ring-orange-100' : '' }}">
                        <i class="fa-solid {{ $data['icon'] }} {{ $isCurrent ? 'animate-bounce' : '' }}"></i>
                    </div>
                    <span class="text-xs font-bold {{ $isCompleted ? 'text-slate-800' : 'text-slate-400' }}">{{ $data['label'] }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <div class="lg:col-span-2 space-y-6">
        @if(session('success'))
            <div class="bg-emerald-50 text-emerald-700 p-4 rounded-xl font-bold flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-xl"></i> {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
                <h4 class="font-bold text-slate-700">Detalle de Productos</h4>
            </div>
            
            <form action="{{ route('pedidos.updateStatus', $pedido->id) }}" method="POST" id="status-form">
                @csrf
                @method('PUT')
                
                <input type="hidden" name="estado" id="input-estado" value="">

                <div class="p-0">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 text-xs text-slate-500 uppercase tracking-wider border-b border-slate-200">
                                <th class="p-4 font-bold">Producto</th>
                                <th class="p-4 font-bold text-center">Sol.</th>
                                <th class="p-4 font-bold text-center">Disp.</th>
                                <th class="p-4 font-bold text-right">Precio</th>
                                <th class="p-4 font-bold text-right">Subtotal</th>
                                @if($pedido->estado->value == 'aceptado' && auth()->user()->hasRole('Bodeguero'))
                                    <th class="p-4 font-bold text-center">Descartar</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($pedido->detalles as $detalle)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-white rounded border border-slate-200 flex items-center justify-center p-1 shrink-0 overflow-hidden">
                                                @php
                                                    $prodLocal = $detalle->productoCatalogo ? \App\Models\Producto::where('nombre', $detalle->productoCatalogo->nombre)->first() : null;
                                                @endphp
                                                @if($prodLocal && $prodLocal->imagen)
                                                    <img src="{{ asset($prodLocal->imagen) }}" class="max-w-full max-h-full object-contain">
                                                @else
                                                    <i class="fa-solid fa-box text-slate-300"></i>
                                                @endif
                                            </div>
                                            <span class="font-bold text-sm text-slate-700">{{ $detalle->productoCatalogo->nombre ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td class="p-4 text-center font-bold text-slate-600">
                                        {{ $detalle->cantidad_solicitada }}
                                    </td>
                                    <td class="p-4 text-center">
                                        @if($pedido->estado->value == 'emitido' && auth()->user()->hasRole('Proveedor'))
                                            <input type="number" name="detalles[{{ $detalle->id }}]" value="{{ $detalle->cantidad_solicitada }}" class="w-20 text-center border-slate-300 rounded-lg shadow-sm focus:border-sky-500 focus:ring-sky-500 font-bold" min="0" required>
                                        @else
                                            <span class="font-black {{ $detalle->cantidad_disponible < $detalle->cantidad_solicitada ? 'text-orange-600' : 'text-emerald-600' }}">
                                                {{ $detalle->cantidad_disponible ?? '-' }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-right text-sm text-slate-600 font-medium">
                                        ${{ number_format($detalle->precio, 2) }}
                                    </td>
                                    <td class="p-4 text-right text-sm font-black text-sky-900">
                                        ${{ number_format(($detalle->cantidad_disponible ?? $detalle->cantidad_solicitada) * $detalle->precio, 2) }}
                                    </td>
                                    @if($pedido->estado->value == 'aceptado' && auth()->user()->hasRole('Bodeguero'))
                                        <td class="p-4 text-center">
                                            <label class="relative inline-flex items-center cursor-pointer justify-center">
                                                <input type="checkbox" name="remove_detalles[]" value="{{ $detalle->id }}" class="sr-only peer">
                                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-500"></div>
                                            </label>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($pedido->estado->value == 'emitido' && auth()->user()->hasRole('Proveedor'))
                    <div class="p-6 border-t border-slate-100 bg-slate-50">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Observaciones / Respuesta (Opcional)</label>
                        <textarea name="observaciones_proveedor" rows="3" class="w-full border-slate-300 rounded-xl shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm p-3" placeholder="Si hay algún producto sin stock completo o requieres indicar algo al bodeguero, escríbelo aquí..."></textarea>
                    </div>
                @endif
                
                @if(!empty($pedido->observaciones_proveedor))
                    <div class="p-6 border-t border-slate-100 bg-orange-50/50">
                        <h4 class="text-sm font-bold text-orange-800 mb-1"><i class="fa-solid fa-message mr-1"></i> Mensaje del Proveedor:</h4>
                        <p class="text-sm text-orange-900 italic">{{ $pedido->observaciones_proveedor }}</p>
                    </div>
                @endif
            </form>
        </div>
    </div>

    {{-- Panel de Acciones --}}
    <div class="space-y-6">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
                <h4 class="font-bold text-slate-700">Acciones</h4>
            </div>
            <div class="p-6 flex flex-col gap-3">
                @if($pedido->estado->value == 'borrador')
                    @hasanyrole('Bodeguero|Administrador|Dueño')
                        <p class="text-sm text-slate-500 mb-2">Una vez que hayas terminado de agregar los productos necesarios, solicita la aprobación para que el Administrador valide la compra.</p>
                        <button onclick="submitStatus('en_aprobacion')" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl shadow-sm transition-colors flex items-center justify-center gap-2">
                            <i class="fa-solid fa-paper-plane"></i> Solicitar Aprobación
                        </button>
                    @endhasanyrole
                @elseif($pedido->estado->value == 'en_aprobacion')
                    @hasanyrole('Administrador|Dueño')
                        <p class="text-sm text-slate-500 mb-2">Revisa el pedido. Al aprobarlo, se emitirá automáticamente al proveedor y no podrán agregarse más productos.</p>
                        <button onclick="submitAprobar()" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-4 rounded-xl shadow-sm transition-colors flex items-center justify-center gap-2">
                            <i class="fa-solid fa-check"></i> Aprobar y Emitir Pedido
                        </button>
                    @else
                        <p class="text-sm text-amber-600 bg-amber-50 p-3 rounded-lg border border-amber-100 font-semibold text-center"><i class="fa-solid fa-hourglass-half mr-1"></i> Esperando aprobación del Administrador</p>
                    @endhasanyrole
                @elseif($pedido->estado->value == 'emitido')
                    @role('Proveedor')
                        <p class="text-sm text-slate-500 mb-2">Por favor, revisa el stock solicitado y ajusta la cantidad disponible si es necesario antes de enviar la revisión.</p>
                        <button onclick="submitStatus('aceptado')" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-xl shadow-sm transition-colors flex items-center justify-center gap-2">
                            <i class="fa-solid fa-clipboard-check"></i> Enviar Revisión a Bodega
                        </button>
                    @else
                        <p class="text-sm text-amber-600 bg-amber-50 p-3 rounded-lg border border-amber-100 font-semibold text-center"><i class="fa-solid fa-hourglass-half mr-1"></i> Esperando revisión del Proveedor</p>
                    @endrole
                @elseif($pedido->estado->value == 'aceptado')
                    @role('Bodeguero')
                        <p class="text-sm text-slate-500 mb-2">El proveedor ha revisado el stock. Si hay productos incompletos que ya no deseas, márcalos para descartarlos y luego confirma el pedido final.</p>
                        <button onclick="submitStatus('en_preparacion')" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl shadow-sm transition-colors flex items-center justify-center gap-2">
                            <i class="fa-solid fa-thumbs-up"></i> Confirmar Pedido Final
                        </button>
                    @else
                        <p class="text-sm text-amber-600 bg-amber-50 p-3 rounded-lg border border-amber-100 font-semibold text-center"><i class="fa-solid fa-hourglass-half mr-1"></i> Esperando validación del Bodeguero</p>
                    @endrole
                @elseif($pedido->estado->value == 'en_preparacion')
                    @role('Proveedor')
                        <p class="text-sm text-slate-500 mb-2">El bodeguero ha confirmado el pedido. Procede a preparar y despachar la mercadería.</p>
                        <button onclick="submitStatus('en_transito')" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-4 rounded-xl shadow-sm transition-colors flex items-center justify-center gap-2">
                            <i class="fa-solid fa-truck-fast"></i> Marcar como Despachado
                        </button>
                    @else
                        <p class="text-sm text-amber-600 bg-amber-50 p-3 rounded-lg border border-amber-100 font-semibold text-center"><i class="fa-solid fa-hourglass-half mr-1"></i> Esperando que el Proveedor despache</p>
                    @endrole
                @elseif($pedido->estado->value == 'en_transito')
                    @role('Bodeguero')
                        <p class="text-sm text-slate-500 mb-2">El pedido está en camino. Una vez recibido físicamente y comprobado, regístralo para actualizar el inventario, o repórtalo como no recibido.</p>
                        <div class="flex flex-col sm:flex-row gap-2">
                            <button onclick="submitStatus('recibido_completado')" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-4 rounded-xl shadow-sm transition-colors flex items-center justify-center gap-2">
                                <i class="fa-solid fa-boxes-packing"></i> Recibió el pedido
                            </button>
                            <button onclick="submitCancelar()" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-xl shadow-sm transition-colors flex items-center justify-center gap-2">
                                <i class="fa-solid fa-circle-xmark"></i> No fue recibido (Cancelar)
                            </button>
                        </div>
                    @else
                        <p class="text-sm text-amber-600 bg-amber-50 p-3 rounded-lg border border-amber-100 font-semibold text-center"><i class="fa-solid fa-hourglass-half mr-1"></i> Esperando recepción en Bodega</p>
                    @endrole
                @elseif(in_array($pedido->estado->value, ['recibido_completado', 'recibido_parcialmente', 'pagado']))
                    <p class="text-sm text-emerald-600 bg-emerald-50 p-3 rounded-lg border border-emerald-100 font-bold text-center flex items-center justify-center gap-2">
                        <i class="fa-solid fa-check-double text-xl"></i> Proceso Completado
                    </p>
                    <p class="text-xs text-slate-500 text-center mt-3">El stock ha sido actualizado en el inventario.</p>
                @elseif($pedido->estado->value == 'cancelado')
                    <p class="text-sm text-red-600 bg-red-50 p-3 rounded-lg border border-red-100 font-bold text-center flex items-center justify-center gap-2">
                        <i class="fa-solid fa-ban text-xl"></i> Pedido Cancelado
                    </p>
                    @if($pedido->motivo_cancelacion)
                        <p class="text-xs text-slate-500 text-center mt-3">
                            Motivo: {{ $pedido->motivo_cancelacion }}
                            @if($pedido->canceladoPor) · por {{ $pedido->canceladoPor->name }} @endif
                        </p>
                    @endif
                @endif

                @if(in_array($pedido->estado->value, ['emitido', 'aceptado', 'en_preparacion']))
                    @hasanyrole('Bodeguero|Administrador|Dueño')
                        <div class="mt-2 pt-4 border-t border-slate-200">
                            <button type="button" onclick="submitCancelar()" class="w-full bg-red-50 hover:bg-red-100 text-red-600 font-bold py-2.5 px-4 rounded-xl transition-colors flex items-center justify-center gap-2 border border-red-200">
                                <i class="fa-solid fa-ban"></i> Cancelar Pedido
                            </button>
                        </div>
                    @endhasanyrole
                @endif
            </div>
        </div>
        

    </div>
</div>

<script>
    function submitStatus(estado) {
        if(confirm('¿Estás seguro de avanzar con esta acción?')) {
            document.getElementById('input-estado').value = estado;
            document.getElementById('status-form').submit();
        }
    }
    function submitAprobar() {
        if(confirm('¿Estás seguro de aprobar este pedido? Se enviará directamente al proveedor.')) {
            document.getElementById('aprobar-form').submit();
        }
    }
    function submitCancelar() {
        const motivo = prompt('¿Por qué se cancela este pedido? (obligatorio)');
        if (motivo === null) return;
        if (motivo.trim() === '') {
            alert('Debes indicar un motivo de cancelación.');
            return;
        }
        document.getElementById('input-motivo-cancelacion').value = motivo.trim();
        document.getElementById('cancelar-form').submit();
    }
</script>
<form id="aprobar-form" action="{{ route('pedidos.aprobar', $pedido->id) }}" method="POST" class="hidden">
    @csrf
</form>
<form id="cancelar-form" action="{{ route('pedidos.cancelar', $pedido->id) }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="motivo_cancelacion" id="input-motivo-cancelacion" value="">
</form>
</div>
@endsection
