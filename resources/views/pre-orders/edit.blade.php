@extends('layouts.app')

@section('content')

<script>
    if (typeof window.preOrderEditForm === 'undefined') {
        window.preOrderEditForm = function () {
            return {
                // ═══════════ State ═══════════
                cart: @json($initialCart),          // pre-filled from server
                specialInstructions: @json($preOrder->special_instructions ?? ''),
                submitting: false,

                // Data from blade
                products:  @json($products),
                reservation: @json($reservationSummary),

                init() {
                    console.log('[preOrderEditForm] Mounted with', Object.keys(this.cart).length, 'items');
                },

                // ═══════════ Computed ═══════════
                get cartItems() {
                    return Object.values(this.cart);
                },

                get itemCount() {
                    return this.cartItems.reduce((s, i) => s + i.quantity, 0);
                },

                get subtotal() {
                    return this.cartItems.reduce((s, i) => s + (i.price * i.quantity), 0);
                },

                get tax() {
                    return Math.round(this.subtotal * 0.05 * 100) / 100;
                },

                get total() {
                    return this.subtotal + this.tax;
                },

                // ═══════════ Actions ═══════════
                addToCart(product) {
                    if (this.cart[product.id]) {
                        this.cart[product.id].quantity++;
                    } else {
                        this.cart[product.id] = {
                            id:       product.id,
                            name:     product.name,
                            price:    parseFloat(product.price),
                            quantity: 1,
                            notes:    '',
                        };
                    }
                },

                removeFromCart(productId) {
                    delete this.cart[productId];
                },

                increment(productId) {
                    if (this.cart[productId]) this.cart[productId].quantity++;
                },

                decrement(productId) {
                    if (this.cart[productId]) {
                        this.cart[productId].quantity--;
                        if (this.cart[productId].quantity <= 0) {
                            delete this.cart[productId];
                        }
                    }
                },

                submitForm(e) {
                    if (this.itemCount === 0) {
                        e.preventDefault();
                        alert('Please keep at least one item.');
                        return false;
                    }
                    this.submitting = true;
                    return true;
                },

                formatPrice(v) {
                    return Number(v).toLocaleString('en-PK', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0,
                    });
                },
            };
        };
    }
</script>

<style>
    [x-cloak] { display: none !important; }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>


<div class="max-w-7xl mx-auto" x-data="preOrderEditForm()">

    {{-- ═══════════ HEADER ═══════════ --}}
    <div class="mb-6 flex items-start gap-4">
        <a href="{{ route('pre-orders.show', $preOrder) }}"
           class="flex items-center justify-center w-11 h-11 rounded-xl
                  bg-white border border-slate-200 text-slate-600
                  hover:text-indigo-600 hover:border-indigo-300 hover:bg-indigo-50
                  shadow-sm transition flex-shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>

        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('pre-orders.index') }}" class="hover:text-indigo-600">Pre-Orders</a>
                <span>/</span>
                <a href="{{ route('pre-orders.show', $preOrder) }}" class="hover:text-indigo-600">
                    #{{ $preOrder->id }}
                </a>
                <span>/</span>
                <span class="text-slate-700">Edit</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">
                Edit Pre-Order #{{ $preOrder->id }}
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                Update items and quantities. Reservation cannot be changed.
            </p>
        </div>

        <span class="inline-flex px-3 py-1 rounded-lg text-xs font-semibold
                     bg-{{ $preOrder->statusColor() }}-100
                     text-{{ $preOrder->statusColor() }}-700 flex-shrink-0">
            {{ ucfirst($preOrder->status) }}
        </span>
    </div>


    {{-- ═══════════ VALIDATION ERRORS ═══════════ --}}
    @if($errors->any())
        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4">
            <p class="text-sm font-semibold text-red-800 mb-1">Please fix:</p>
            <ul class="text-sm text-red-700 list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <form action="{{ route('pre-orders.update', $preOrder) }}"
          method="POST"
          @submit="submitForm($event)">
        @csrf
        @method('PUT')

        {{-- Hidden cart items --}}
        <template x-for="(item, idx) in cartItems" :key="'hidden-' + item.id">
            <div>
                <input type="hidden" :name="`items[${idx}][product_id]`" :value="item.id">
                <input type="hidden" :name="`items[${idx}][quantity]`"   :value="item.quantity">
                <input type="hidden" :name="`items[${idx}][notes]`"      :value="item.notes">
            </div>
        </template>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- ═══════════ LEFT: RESERVATION INFO + MENU ═══════════ --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Reservation summary (locked) --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-3 border-b border-slate-100 flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600
                                    flex items-center justify-center">
                            📅
                        </div>
                        <h2 class="text-base font-semibold text-slate-900">Reservation</h2>
                        <span class="ml-auto text-xs text-slate-400 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                 stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Locked
                        </span>
                    </div>

                    <div class="p-5">
                        <div class="p-3 rounded-xl bg-indigo-50 border border-indigo-100
                                    flex items-center gap-3">
                            <div class="text-xl">🏨</div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-indigo-900"
                                   x-text="reservation.hotel_name"></p>
                                <p class="text-xs text-indigo-700 mt-0.5">
                                    <span x-text="reservation.date"></span> ·
                                    <span x-text="reservation.time"></span>
                                    <template x-if="reservation.end_time">
                                        <span>
                                            – <span x-text="reservation.end_time"></span>
                                        </span>
                                    </template>
                                    · <span x-text="reservation.guests"></span> guests
                                </p>
                            </div>
                        </div>
                    </div>
                </div>


                {{-- Menu --}}
                <template x-for="(items, category) in products" :key="'cat-' + category">
                    <div class="bg-white rounded-2xl border border-slate-200
                                shadow-sm overflow-hidden">

                        <div class="px-5 py-3 border-b border-slate-100
                                    flex items-center justify-between">
                            <h2 class="text-base font-semibold text-slate-900"
                                x-text="category || 'Uncategorized'"></h2>
                            <span class="text-xs text-slate-500"
                                  x-text="items.length + ' items'"></span>
                        </div>

                        <div class="divide-y divide-slate-100">
                            <template x-for="product in items" :key="'p-' + product.id">
                                <div class="p-4 flex items-center gap-4
                                            hover:bg-slate-50 transition">

                                    <div class="w-14 h-14 rounded-xl
                                                bg-gradient-to-br
                                                from-indigo-100 to-violet-100
                                                flex items-center justify-center
                                                text-2xl flex-shrink-0"
                                         x-text="product.is_vegetarian ? '🥗' : '🍖'">
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h3 class="text-sm font-semibold text-slate-900 truncate"
                                                x-text="product.name"></h3>
                                            <template x-if="product.is_vegetarian">
                                                <span class="inline-flex px-1.5 py-0.5 rounded
                                                             bg-emerald-100 text-emerald-700
                                                             text-[10px] font-bold">
                                                    VEG
                                                </span>
                                            </template>
                                        </div>

                                        <template x-if="product.description">
                                            <p class="text-xs text-slate-500 mt-0.5 line-clamp-2"
                                               x-text="product.description"></p>
                                        </template>

                                        <div class="flex items-center gap-3 mt-1.5">
                                            <p class="text-sm font-bold text-slate-900">
                                                Rs <span x-text="formatPrice(product.price)"></span>
                                            </p>
                                            <template x-if="product.stock_quantity !== null">
                                                <p class="text-xs text-slate-400">
                                                    <span x-text="product.stock_quantity"></span> left
                                                </p>
                                            </template>
                                            <p class="text-xs text-slate-400">
                                                ⏱️ <span x-text="product.preparation_minutes"></span>m
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex-shrink-0">
                                        <template x-if="!cart[product.id]">
                                            <button type="button"
                                                    @click="addToCart(product)"
                                                    :disabled="product.stock_quantity !== null && product.stock_quantity <= 0"
                                                    class="inline-flex items-center gap-1.5
                                                           px-4 py-2 rounded-xl
                                                           bg-indigo-600 text-white
                                                           text-sm font-semibold
                                                           hover:bg-indigo-700
                                                           active:scale-95
                                                           transition shadow-sm
                                                           disabled:opacity-50 disabled:cursor-not-allowed">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                     stroke-width="2.5" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          d="M12 4v16m8-8H4"/>
                                                </svg>
                                                Add
                                            </button>
                                        </template>

                                        <template x-if="cart[product.id]">
                                            <div class="inline-flex items-center gap-1
                                                        bg-indigo-50 rounded-xl p-1
                                                        border border-indigo-200">
                                                <button type="button"
                                                        @click="decrement(product.id)"
                                                        class="w-8 h-8 rounded-lg bg-white
                                                               text-indigo-600 font-bold
                                                               hover:bg-indigo-100 transition">
                                                    −
                                                </button>
                                                <span class="w-8 text-center text-sm font-bold
                                                             text-indigo-900"
                                                      x-text="cart[product.id]?.quantity || 0"></span>
                                                <button type="button"
                                                        @click="increment(product.id)"
                                                        class="w-8 h-8 rounded-lg bg-white
                                                               text-indigo-600 font-bold
                                                               hover:bg-indigo-100 transition">
                                                    +
                                                </button>
                                            </div>
                                        </template>
                                    </div>

                                </div>
                            </template>
                        </div>

                    </div>
                </template>

            </div>


            {{-- ═══════════ RIGHT: CART ═══════════ --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm
                            lg:sticky lg:top-20 overflow-hidden">

                    <div class="px-5 py-4 border-b border-slate-100
                                flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600
                                        flex items-center justify-center">
                                🛒
                            </div>
                            <h2 class="text-sm font-semibold text-slate-900">Your Order</h2>
                        </div>
                        <span class="inline-flex items-center justify-center
                                     min-w-[1.5rem] h-6 px-2
                                     rounded-full bg-indigo-600 text-white
                                     text-xs font-bold"
                              x-text="itemCount"></span>
                    </div>

                    <template x-if="itemCount === 0">
                        <div class="p-8 text-center">
                            <div class="text-3xl mb-2">🛒</div>
                            <p class="text-sm text-slate-500">
                                Your cart is empty.
                            </p>
                            <p class="text-xs text-slate-400 mt-1">
                                Add at least one item.
                            </p>
                        </div>
                    </template>

                    <template x-if="itemCount > 0">
                        <div>
                            <div class="max-h-80 overflow-y-auto divide-y divide-slate-100">
                                <template x-for="item in cartItems" :key="'cart-' + item.id">
                                    <div class="p-4">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-semibold text-slate-900 truncate"
                                                   x-text="item.name"></p>
                                                <p class="text-xs text-slate-500 mt-0.5">
                                                    Rs <span x-text="formatPrice(item.price)"></span> ×
                                                    <span x-text="item.quantity"></span>
                                                </p>
                                            </div>
                                            <button type="button"
                                                    @click="removeFromCart(item.id)"
                                                    class="text-slate-400 hover:text-rose-500 transition flex-shrink-0">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                     stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="flex items-center justify-between mt-2">
                                            <div class="inline-flex items-center gap-1
                                                        bg-slate-100 rounded-lg p-0.5">
                                                <button type="button"
                                                        @click="decrement(item.id)"
                                                        class="w-6 h-6 rounded-md bg-white
                                                               text-slate-700 text-sm font-bold
                                                               hover:bg-slate-50 transition">
                                                    −
                                                </button>
                                                <span class="w-6 text-center text-xs font-bold"
                                                      x-text="item.quantity"></span>
                                                <button type="button"
                                                        @click="increment(item.id)"
                                                        class="w-6 h-6 rounded-md bg-white
                                                               text-slate-700 text-sm font-bold
                                                               hover:bg-slate-50 transition">
                                                    +
                                                </button>
                                            </div>
                                            <p class="text-sm font-bold text-slate-900">
                                                Rs <span x-text="formatPrice(item.price * item.quantity)"></span>
                                            </p>
                                        </div>
                                        <input type="text"
                                               x-model="item.notes"
                                               placeholder="Notes (optional)"
                                               class="w-full mt-2 text-xs rounded-lg
                                                      border border-slate-200
                                                      px-2 py-1.5
                                                      focus:border-indigo-500 focus:ring-1
                                                      focus:ring-indigo-200 outline-none">
                                    </div>
                                </template>
                            </div>

                            <div class="p-4 border-t border-slate-100">
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Special Instructions
                                </label>
                                <textarea name="special_instructions"
                                          x-model="specialInstructions"
                                          rows="2"
                                          placeholder="Allergies, timing, etc."
                                          class="w-full rounded-lg border border-slate-300
                                                 px-3 py-2 text-xs
                                                 placeholder-slate-400 resize-none
                                                 focus:border-indigo-500 focus:ring-2
                                                 focus:ring-indigo-200 outline-none transition"></textarea>
                            </div>

                            <div class="px-4 py-3 bg-slate-50 border-t border-slate-100 space-y-1">
                                <div class="flex justify-between text-xs text-slate-600">
                                    <span>Subtotal</span>
                                    <span class="font-medium">
                                        Rs <span x-text="formatPrice(subtotal)"></span>
                                    </span>
                                </div>
                                <div class="flex justify-between text-xs text-slate-600">
                                    <span>Tax (5%)</span>
                                    <span class="font-medium">
                                        Rs <span x-text="formatPrice(tax)"></span>
                                    </span>
                                </div>
                                <div class="flex justify-between text-sm font-bold text-slate-900
                                            pt-2 border-t border-slate-200 mt-2">
                                    <span>Total</span>
                                    <span>Rs <span x-text="formatPrice(total)"></span></span>
                                </div>
                            </div>

                            <div class="p-4 border-t border-slate-100 space-y-2">
                                <button type="submit"
                                        :disabled="submitting || itemCount === 0"
                                        class="w-full inline-flex items-center justify-center gap-2
                                               px-5 py-3 rounded-xl
                                               bg-gradient-to-r from-indigo-500 to-violet-600
                                               text-sm font-semibold text-white
                                               hover:shadow-lg hover:shadow-indigo-500/30
                                               active:scale-[0.98] transition shadow-md
                                               disabled:opacity-50 disabled:cursor-not-allowed">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                         stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span x-text="submitting ? 'Updating…' : 'Update Pre-Order'"></span>
                                </button>

                                <a href="{{ route('pre-orders.show', $preOrder) }}"
                                   class="w-full inline-flex items-center justify-center
                                          px-5 py-2.5 rounded-xl
                                          bg-white border border-slate-300
                                          text-slate-700 text-sm font-semibold
                                          hover:bg-slate-50 transition">
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </template>

                </div>
            </div>

        </div>

    </form>

</div>

@endsection
