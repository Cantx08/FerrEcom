{{-- Alpine JS Cart Drawer - Premium Design --}}
<div x-show="open" class="relative z-50" aria-labelledby="cart-title" role="dialog" aria-modal="true" style="display:none;">

    {{-- Backdrop --}}
    <div x-show="open"
         x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="open = false"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm">
    </div>

    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute inset-0 overflow-hidden flex justify-end">
            {{-- Panel --}}
            <div x-show="open"
                 x-transition:enter="transform transition ease-out duration-300"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transform transition ease-in duration-250"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full"
                 class="pointer-events-auto w-full max-w-sm sm:max-w-md flex flex-col h-full bg-white shadow-2xl">

                {{-- Header --}}
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 bg-sky-900 text-white shrink-0">
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-cart-shopping text-lg"></i>
                        <h2 class="text-base font-bold" id="cart-title">Mi carrito</h2>
                        <span x-show="count > 0" x-text="count + ' item' + (count > 1 ? 's' : '')"
                              class="bg-orange-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full">
                        </span>
                    </div>
                    <button @click="open = false"
                            class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                {{-- Empty State --}}
                <div x-show="items.length === 0" class="flex-1 flex flex-col items-center justify-center text-center px-8 py-12">
                    <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mb-5">
                        <i class="fa-solid fa-basket-shopping text-4xl text-slate-300"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-700 mb-1">Tu carrito está vacío</h3>
                    <p class="text-sm text-slate-400 mb-6">Agrega productos para comenzar tu pedido</p>
                    <a href="{{ route('home') }}"
                            class="btn-cta text-white font-bold px-6 py-2.5 rounded-xl text-sm flex items-center gap-2">
                        <i class="fa-solid fa-bag-shopping"></i> Ir a Tienda
                    </a>
                </div>

                {{-- Cart Items --}}
                <div x-show="items.length > 0" class="flex-1 overflow-y-auto">
                    <ul class="divide-y divide-slate-100">
                        <template x-for="item in items" :key="item.id">
                            <li class="flex gap-3 px-5 py-4 hover:bg-slate-50/70 transition-colors">
                                {{-- Image --}}
                                <div class="w-16 h-16 shrink-0 rounded-xl border border-slate-200 bg-slate-100 overflow-hidden flex items-center justify-center">
                                    <template x-if="item.imagen">
                                        <img :src="'/' + item.imagen" :alt="item.nombre"
                                             class="w-full h-full object-cover object-center">
                                    </template>
                                    <template x-if="!item.imagen">
                                        <i class="fa-solid fa-box text-2xl text-slate-300"></i>
                                    </template>
                                </div>

                                {{-- Info --}}
                                <div class="flex-1 min-w-0">
                                    <h3 x-text="item.nombre"
                                        class="text-sm font-semibold text-slate-800 leading-snug line-clamp-2 mb-1.5">
                                    </h3>
                                    <p class="text-xs text-slate-400 mb-2"
                                       x-text="'$' + Number(item.precio).toFixed(2) + ' c/u'"></p>
                                    
                                    {{-- Etiqueta Descuento --}}
                                    <template x-if="item.cantidad_por_mayor && item.qty >= item.cantidad_por_mayor && item.descuento_por_mayor">
                                        <div class="bg-emerald-50 text-emerald-600 text-[10px] font-bold px-2 py-1 rounded mb-2 inline-flex items-center gap-1">
                                            <i class="fa-solid fa-tag"></i> <span x-text="item.descuento_por_mayor + '% descuento'"></span>
                                        </div>
                                    </template>
                                    
                                    <div class="flex items-center justify-between">
                                        {{-- Qty Controls --}}
                                        <div class="flex items-center rounded-lg border border-slate-200 overflow-hidden bg-white">
                                            <button @click="changeQty(item.id, -1)"
                                                    class="w-7 h-7 flex items-center justify-center text-slate-500 hover:bg-red-50 hover:text-red-500 transition-colors font-bold text-sm">
                                                <i class="fa-solid fa-minus text-[10px]"></i>
                                            </button>
                                            <span x-text="item.qty"
                                                  class="w-8 text-center text-sm font-bold text-slate-700 border-x border-slate-200 py-1"></span>
                                            <button @click="changeQty(item.id, 1)"
                                                    class="w-7 h-7 flex items-center justify-center text-slate-500 hover:bg-green-50 hover:text-green-600 transition-colors font-bold text-sm">
                                                <i class="fa-solid fa-plus text-[10px]"></i>
                                            </button>
                                        </div>
                                        {{-- Line total + Remove --}}
                                        <div class="flex items-center gap-2">
                                            <span x-text="'$' + (item.precio * item.qty).toFixed(2)"
                                                  class="text-sm font-black text-sky-900"></span>
                                            <button @click="removeItem(item.id)"
                                                    class="w-6 h-6 flex items-center justify-center rounded-md text-slate-300 hover:bg-red-50 hover:text-red-500 transition-colors">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </template>
                    </ul>
                </div>

                {{-- Footer / Summary --}}
                <div x-show="items.length > 0" class="border-t border-slate-200 bg-slate-50/80 px-5 py-5 shrink-0">
                    {{-- Breakdown --}}
                    <div class="space-y-2 mb-4 text-sm">
                        <div class="flex justify-between text-slate-600">
                            <span>Subtotal</span>
                            <span class="font-semibold text-slate-800" x-text="'$' + subtotal.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between text-emerald-600 font-semibold" x-show="descuentoTotal > 0">
                            <span>Descuento por mayor</span>
                            <span x-text="'-$' + descuentoTotal.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>
                                Envío
                                <span x-show="envio === 0" class="ml-1 bg-emerald-100 text-emerald-700 text-[9px] font-black px-1.5 py-0.5 rounded-full">GRATIS</span>
                            </span>
                            <span class="font-semibold text-slate-800" x-text="envio === 0 ? 'Gratis' : '$' + envio.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>IVA (15%)</span>
                            <span class="font-semibold text-slate-800" x-text="'$' + iva.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between text-sky-900 font-black text-base pt-3 border-t border-slate-200">
                            <span>Total</span>
                            <span x-text="'$' + total.toFixed(2)"></span>
                        </div>
                    </div>

                    {{-- Checkout CTA --}}
                    <button @click="checkout()"
                            class="btn-cta w-full text-white font-bold py-3.5 rounded-xl flex items-center justify-center gap-2 text-sm">
                        <i class="fa-solid fa-lock text-xs"></i>
                        Finalizar compra segura
                    </button>
                    <button @click="open = false"
                            class="w-full mt-2.5 py-2.5 rounded-xl text-sm font-semibold text-slate-500 hover:text-sky-900 hover:bg-slate-100 transition-colors flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-chevron-left text-xs"></i> Seguir comprando
                    </button>
                    <button @click="emptyCart()"
                            class="w-full mt-1 py-2 rounded-xl text-sm font-semibold text-red-500 hover:text-red-700 hover:bg-red-50 transition-colors flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-trash-can text-xs"></i> Vaciar carrito
                    </button>

                    {{-- Trust indicators --}}
                    <div class="flex items-center justify-center gap-3 mt-4 text-xs text-slate-400">
                        <i class="fa-brands fa-cc-visa text-lg"></i>
                        <i class="fa-brands fa-cc-mastercard text-lg"></i>
                        <i class="fa-brands fa-cc-amex text-lg"></i>
                        <span class="text-slate-300">|</span>
                        <i class="fa-solid fa-shield-halved text-emerald-500"></i>
                        <span>Pago seguro</span>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function cartComponent() {
        return {
            open: false,
            items: JSON.parse(localStorage.getItem('ferremax_cart')) || [],
            init() {
                window.addEventListener('cart-clear', () => {
                    this.items = [];
                    localStorage.removeItem('ferremax_cart');
                });
            },

            get count()    { return this.items.reduce((t, i) => t + i.qty, 0); },
            get subtotal() { return this.items.reduce((t, i) => t + (i.precio * i.qty), 0); },
            get descuentoTotal() {
                return this.items.reduce((t, i) => {
                    if (i.cantidad_por_mayor && i.qty >= i.cantidad_por_mayor && i.descuento_por_mayor) {
                        return t + (i.precio * (i.descuento_por_mayor / 100) * i.qty);
                    }
                    return t;
                }, 0);
            },
            get subtotalNeto() { return this.subtotal - this.descuentoTotal; },
            get envio()    { return (this.subtotalNeto >= 50 || this.subtotalNeto === 0) ? 0 : 4.50; },
            get iva()      { return this.subtotalNeto * 0.15; },
            get total()    { return this.subtotalNeto + this.envio + this.iva; },

            saveCart() { localStorage.setItem('ferremax_cart', JSON.stringify(this.items)); },

            addToCart(id, nombre, precio, stock, imagen, cantidad_por_mayor, descuento_por_mayor) {
                const existing = this.items.find(i => i.id === id);
                if (existing) {
                    if (existing.qty < stock) {
                        existing.qty++;
                        this._toast('success', 'Cantidad actualizada');
                    } else {
                        this._toast('warning', 'No hay más stock disponible');
                    }
                } else {
                    this.items.push({ id, nombre, precio, qty: 1, stock, imagen, cantidad_por_mayor, descuento_por_mayor });
                    this._toast('success', `<strong>${nombre}</strong> agregado al carrito`);
                }
                this.saveCart();
                this.open = true;
            },

            changeQty(id, delta) {
                const item = this.items.find(i => i.id === id);
                if (!item) return;
                if (delta > 0 && item.qty >= item.stock) {
                    this._toast('warning', 'Stock máximo alcanzado'); return;
                }
                item.qty += delta;
                if (item.qty <= 0) this.removeItem(id);
                else this.saveCart();
            },

            removeItem(id) { this.items = this.items.filter(i => i.id !== id); this.saveCart(); },

            emptyCart() {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: '¿Vaciar carrito?',
                        text: 'Se eliminarán todos los productos',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ea580c',
                        cancelButtonColor: '#94a3b8',
                        confirmButtonText: 'Sí, vaciar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.items = [];
                            this.saveCart();
                            this._toast('success', 'Carrito vaciado');
                        }
                    });
                } else {
                    if (confirm('¿Vaciar carrito?')) {
                        this.items = [];
                        this.saveCart();
                    }
                }
            },

            _toast(icon, msg) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        toast: true, position: 'bottom-end', icon, html: msg,
                        showConfirmButton: false, timer: 2500, timerProgressBar: true,
                        customClass: { container: 'toast-container', popup: 'rounded-xl shadow-xl' }
                    });
                }
            },
            checkout() {
                if (this.items.length === 0) return;
                window.location.href = '{{ route("cart.checkout.view") }}';
            }
        }
    }
</script>
@endpush
