@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="checkoutPage()">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-black text-sky-900">Checkout</h1>
            <p class="text-slate-500">Completa tu pago y retira tus productos en tienda.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('cart.checkout.process') }}" @submit.prevent="submitForm" id="checkout-form">
        @csrf
        <input type="hidden" name="cart_data" id="cart_data_input">
        <input type="hidden" name="metodo_pago" value="tarjeta">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Datos de Facturación --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <h3 class="text-lg font-bold text-sky-900 mb-4 border-b pb-2"><i class="fa-solid fa-file-invoice mr-2"></i>Datos de Facturación</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Cédula / RUC</label>
                            <input type="text" name="ruc_cedula" value="{{ $profile->identificacion ?? '' }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-200 transition-all" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Nombres / Razón Social</label>
                            <input type="text" name="razon_social" value="{{ $profile ? trim($profile->nombres . ' ' . $profile->apellidos) : '' }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-200 transition-all" required>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Dirección</label>
                            <input type="text" name="direccion" value="{{ $profile->direccion ?? '' }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-200 transition-all" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Teléfono</label>
                            <input type="text" name="telefono" value="{{ $profile->telefono ?? '' }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-200 transition-all" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Correo Electrónico</label>
                            <input type="email" name="correo" value="{{ $user->email ?? '' }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-200 transition-all" required>
                        </div>
                    </div>
                </div>

                {{-- Método de Entrega --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <h3 class="text-lg font-bold text-sky-900 mb-4 border-b pb-2"><i class="fa-solid fa-truck-fast mr-2"></i>Método de Entrega</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="relative flex cursor-pointer rounded-xl border bg-white p-4 shadow-sm focus-within:ring-2 focus-within:ring-sky-500 transition-all"
                               :class="metodoEntrega === 'retiro_en_caja' ? 'border-sky-500 ring-1 ring-sky-500 bg-sky-50/30' : 'border-slate-200 hover:bg-slate-50'">
                            <input type="radio" name="metodo_entrega" value="retiro_en_caja" x-model="metodoEntrega" class="sr-only">
                            <span class="flex flex-1">
                                <span class="flex flex-col">
                                    <span class="block text-sm font-bold text-slate-900">Retiro en tienda</span>
                                    <span class="mt-1 flex items-center text-xs text-slate-500">Gratis - Inmediato</span>
                                </span>
                            </span>
                            <i class="fa-solid fa-store text-2xl transition-colors" :class="metodoEntrega === 'retiro_en_caja' ? 'text-sky-600' : 'text-slate-300'"></i>
                        </label>
                        
                        <label class="relative flex cursor-pointer rounded-xl border bg-white p-4 shadow-sm focus-within:ring-2 focus-within:ring-sky-500 transition-all"
                               :class="metodoEntrega === 'envio_domicilio' ? 'border-sky-500 ring-1 ring-sky-500 bg-sky-50/30' : 'border-slate-200 hover:bg-slate-50'">
                            <input type="radio" name="metodo_entrega" value="envio_domicilio" x-model="metodoEntrega" class="sr-only">
                            <span class="flex flex-1">
                                <span class="flex flex-col">
                                    <span class="block text-sm font-bold text-slate-900">Envío a domicilio</span>
                                    <span class="mt-1 flex items-center text-xs text-slate-500">Aplica recargo ($4.50)</span>
                                </span>
                            </span>
                            <i class="fa-solid fa-motorcycle text-2xl transition-colors" :class="metodoEntrega === 'envio_domicilio' ? 'text-sky-600' : 'text-slate-300'"></i>
                        </label>
                    </div>
                </div>

                {{-- Pasarela Simulación --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <h3 class="text-lg font-bold text-sky-900 mb-4 border-b pb-2"><i class="fa-solid fa-credit-card mr-2"></i>Pago Seguro</h3>
                    
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-200 mb-4">
                        <div class="flex items-center gap-3 mb-4">
                            <i class="fa-brands fa-cc-visa text-3xl text-blue-800"></i>
                            <i class="fa-brands fa-cc-mastercard text-3xl text-orange-600"></i>
                            <span class="font-bold text-slate-600 text-sm">Tarjetas de Crédito / Débito</span>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Número de tarjeta</label>
                                <input type="text" value="4111 1111 1111 1111" class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 font-mono text-slate-700 focus:outline-none focus:border-sky-500" required>
                            </div>
                            <div class="flex gap-4">
                                <div class="flex-1">
                                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Vencimiento</label>
                                    <input type="text" value="12/28" class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-slate-700 focus:outline-none focus:border-sky-500" required>
                                </div>
                                <div class="flex-1">
                                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">CVC</label>
                                    <input type="text" value="123" class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-slate-700 focus:outline-none focus:border-sky-500 tracking-widest" required>
                                </div>
                            </div>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-4 text-center"><i class="fa-solid fa-lock text-emerald-500 mr-1"></i> Transacción cifrada. Esto es una simulación de pago.</p>
                    </div>
                </div>
            </div>

            {{-- Resumen de la Orden --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sticky top-6">
                    <h3 class="text-lg font-bold text-sky-900 mb-4 border-b pb-2"><i class="fa-solid fa-bag-shopping mr-2"></i>Tu Orden</h3>
                    
                    <div class="space-y-3 mb-4 max-h-60 overflow-y-auto pr-2">
                        <template x-for="item in cartItems" :key="item.id">
                            <div class="flex justify-between items-center text-sm border-b border-slate-100 pb-2">
                                <div class="flex-1 pr-2">
                                    <span class="font-bold text-slate-700" x-text="item.qty + 'x '"></span>
                                    <span class="text-slate-600 truncate inline-block max-w-[120px] align-bottom" x-text="item.nombre"></span>
                                </div>
                                <span class="font-bold text-slate-800" x-text="'$' + (item.precio * item.qty).toFixed(2)"></span>
                            </div>
                        </template>
                    </div>

                    <div class="space-y-2 text-sm text-slate-600 border-t border-slate-200 pt-4">
                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span class="font-bold" x-text="'$' + subtotal.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between text-emerald-600 font-bold" x-show="descuentoTotal > 0">
                            <span>Ahorro Mayorista</span>
                            <span x-text="'-$' + descuentoTotal.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Envío (Gratis > $50)</span>
                            <span class="font-bold text-orange-600" x-text="'$' + envio.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between">
                            <span>IVA (15%)</span>
                            <span class="font-bold" x-text="'$' + iva.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between text-lg text-sky-900 font-black mt-2 pt-2 border-t border-sky-100">
                            <span>Total</span>
                            <span x-text="'$' + total.toFixed(2)"></span>
                        </div>
                    </div>

                    <button type="submit" class="mt-6 w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-orange-200 transition-all">
                        <i class="fa-solid fa-lock mr-2"></i> Pagar y Confirmar
                    </button>
                    
                    <template x-if="cartItems.length === 0">
                        <p class="text-red-500 text-xs text-center mt-3 font-bold">Tu carrito está vacío.</p>
                    </template>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function checkoutPage() {
        return {
            metodoEntrega: 'retiro_en_caja',
            cartItems: JSON.parse(localStorage.getItem('ferremax_cart')) || [],
            get subtotal() {
                return this.cartItems.reduce((t, i) => t + (i.precio * i.qty), 0);
            },
            get descuentoTotal() {
                return this.cartItems.reduce((t, i) => {
                    if (i.cantidad_por_mayor && i.qty >= i.cantidad_por_mayor && i.descuento_por_mayor) {
                        return t + (i.precio * (i.descuento_por_mayor / 100) * i.qty);
                    }
                    return t;
                }, 0);
            },
            get subtotalNeto() {
                return this.subtotal - this.descuentoTotal;
            },
            get envio() {
                if (this.metodoEntrega === 'retiro_en_caja') return 0;
                return (this.subtotalNeto >= 50 || this.subtotalNeto === 0) ? 0 : 4.50;
            },
            get iva() {
                return this.subtotalNeto * 0.15;
            },
            get total() {
                return this.subtotalNeto + this.envio + this.iva;
            },
            submitForm(e) {
                if (this.cartItems.length === 0) {
                    Swal.fire({icon: 'error', title: 'Oops...', text: 'El carrito está vacío'});
                    return;
                }
                
                // Populate hidden input before submit
                document.getElementById('cart_data_input').value = JSON.stringify(this.cartItems);

                e.target.submit();
            }
        }
    }
</script>

@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('error') }}'
            });
        }
    });
</script>
@endif
@endpush
@endsection
