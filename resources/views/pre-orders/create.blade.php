@extends('layouts.app')

@section('content')

<script>
    if (typeof window.preOrderForm === 'undefined') {
        window.preOrderForm = function () {
            return {
                // ═══════════ State ═══════════
                selectedReservationId: '',
                cart: {},                // { productId: { id, name, price, quantity, notes } }
                specialInstructions: '',
                searchQuery: '',
                submitting: false,

                // Data passed from blade
                reservations:   @json($reservationOptions),
                productsByHotel: @json($productsByHotel),

                init() {
                    console.log('[preOrderForm] Mounted');
                    console.log('Reservations available:', this.reservations.length);
                },

                // ═══════════ Computed ═══════════
                get selectedReservation() {
                    if (!this.selectedReservationId) return null;
                    return this.reservations.find(
                        r => r.id == this.selectedReservationId
                    ) || null;
                },

                get currentHotelId() {
                    return this.selectedReservation?.hotel_id || null;
                },

                get currentProducts() {
                    if (!this.currentHotelId) return {};
                    return this.productsByHotel[this.currentHotelId] || {};
                },

                get hasProducts() {
                    return Object.keys(this.currentProducts).length > 0;
                },

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
                onReservationChange() {
                    // Clear cart when reservation changes
                    this.cart = {};
                },

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
                    if (!this.selectedReservationId) {
                        e.preventDefault();
                        alert('Please select a reservation first.');
                        return false;
                    }
                    if (this.itemCount === 0) {
                        e.preventDefault();
                        alert('Please add at least one item.');
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


<div class="max-w-7xl mx-auto" x-data="preOrderForm()">

    {{-- ═══════════════ HEADER ═══════════════ --}}
    <div class="mb-6 flex items-start gap-4">
        <a href="{{ route('pre-orders.index') }}"
           class="flex items-center justify-center w-11 h-11 rounded-xl
                  bg-white border border-slate-200 text-slate-600
                  hover:text-indigo-600 hover:border-indigo-300 hover:bg-indigo-50
                  shadow-sm transition flex-shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>

        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('pre-orders.index') }}" class="hover:text-indigo-600">Pre-Orders</a>
                <span>/</span>
                <span class="text-slate-700">New</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">
                Add Pre-Order
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                Choose a reservation, then pick items from that hotel's menu.
            </p>
        </div>
    </div>


    {{-- ═══════════════ NO RESERVATIONS AT ALL ═══════════════ --}}
    @if(empty($reservationOptions))
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-12 text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl
                        bg-gradient-to-br from-amber-100 to-orange-100 text-4xl mb-4">
                📅
            </div>
            <p class="text-base font-semibold text-slate-800">
                No confirmed reservations available
            </p>
            <p class="text-sm text-slate-500 mt-1 max-w-md mx-auto">
                You need a confirmed reservation (today or later) to add a pre-order.
            </p>

            <div class="flex items-center justify-center gap-3 mt-5 flex-wrap">
                @can('create reservations')
                    <a href="{{ route('reservations.create') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                              bg-gradient-to-r from-indigo-500 to-violet-600
                              text-sm font-semibold text-white
                              hover:shadow-lg hover:shadow-indigo-500/30
                              active:scale-[0.98] transition shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                             stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 4v16m8-8H4"/>
                        </svg>
                        New Reservation
                    </a>
                @endcan

                <a href="{{ route('reservations.index') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                          bg-white border border-slate-300 text-slate-700
                          text-sm font-semibold hover:bg-slate-50 transition">
                    View Reservations
                </a>
            </div>
        </div>


    {{-- ═══════════════ MAIN FORM ═══════════════ --}}
    @else

        <form action="{{ route('pre-orders.store') }}"
              method="POST"
              @submit="submitForm($event)">
            @csrf

            <input type="hidden"
                   name="reservation_id"
                   :value="selectedReservationId">

            {{-- Hidden cart items --}}
            <template x-for="(item, idx) in cartItems" :key="'hidden-' + item.id">
                <div>
                    <input type="hidden" :name="`items[${idx}][product_id]`" :value="item.id">
                    <input type="hidden" :name="`items[${idx}][quantity]`"   :value="item.quantity">
                    <input type="hidden" :name="`items[${idx}][notes]`"      :value="item.notes">
                </div>
            </template>


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


            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

                {{-- ═══════════ LEFT: RESERVATION + MENU ═══════════ --}}
                <div class="lg:col-span-2 space-y-5">

                    {{-- Reservation dropdown --}}
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

                        <div class="px-5 py-3 border-b border-slate-100 flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600
                                        flex items-center justify-center">
                                📅
                            </div>
                            <h2 class="text-base font-semibold text-slate-900">
                                Step 1 — Choose Reservation
                            </h2>
                        </div>

                        <div class="p-5">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                                Reservation <span class="text-rose-500">*</span>
                            </label>
                            <select x-model="selectedReservationId"
                                    @change="onReservationChange()"
                                    class="w-full rounded-xl border border-slate-300 bg-white
                                           px-4 py-2.5 text-sm text-slate-800 shadow-sm
                                           focus:border-indigo-500 focus:ring-2
                                           focus:ring-indigo-200 outline-none transition">
                                <option value="">— Select a confirmed reservation —</option>
                                <template x-for="r in reservations" :key="'res-' + r.id">
                                    <option :value="r.id" x-text="r.label"></option>
                                </template>
                            </select>

                            <p class="text-xs text-slate-500 mt-2">
                                Only confirmed reservations from today onwards are listed.
                            </p>

                            {{-- Selected reservation detail --}}
                            <template x-if="selectedReservation">
                                <div class="mt-3 p-3 rounded-xl bg-indigo-50 border border-indigo-100
                                            flex items-center gap-3">
                                    <div class="text-xl">🏨</div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-semibold text-indigo-900"
                                           x-text="selectedReservation.hotel_name"></p>
                                        <p class="text-xs text-indigo-700 mt-0.5">
                                            <span x-text="selectedReservation.date"></span> ·
                                            <span x-text="selectedReservation.time"></span>
                                            <template x-if="selectedReservation.end_time">
                                                <span>
                                                    – <span x-text="selectedReservation.end_time"></span>
                                                </span>
                                            </template>
                                            · <span x-text="selectedReservation.guests"></span> guests
                                        </p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>


                    {{-- Menu — appears only after reservation selected --}}
                    <template x-if="selectedReservationId && hasProducts">
                        <div class="space-y-5">

                            <template x-for="(items, category) in currentProducts"
                                      :key="'cat-' + category">
                                <div class="bg-white rounded-2xl border border-slate-200
                                            shadow-sm overflow-hidden">

                                    {{-- Category header --}}
                                    <div class="px-5 py-3 border-b border-slate-100
                                                flex items-center justify-between">
                                        <h2 class="text-base font-semibold text-slate-900"
                                            x-text="category || 'Uncategorized'"></h2>
                                        <span class="text-xs text-slate-500"
                                              x-text="items.length + ' items'"></span>
                                    </div>

                                    {{-- Products list --}}
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
                    </template>


                    {{-- No products for this hotel --}}
                    <template x-if="selectedReservationId && !hasProducts">
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-12 text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl
                                        bg-slate-100 text-3xl mb-3">
                                🍽️
                            </div>
                            <p class="text-sm font-medium text-slate-700">
                                No products available at this hotel.
                            </p>
                            <p class="text-xs text-slate-500 mt-1">
                                Try a different reservation or contact the hotel.
                            </p>
                        </div>
                    </template>


                    {{-- Placeholder when nothing selected --}}
                    <template x-if="!selectedReservationId">
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-12 text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl
                                        bg-slate-100 text-3xl mb-3">
                                🍽️
                            </div>
                            <p class="text-sm font-medium text-slate-700">
                                Step 2 — Menu will appear here
                            </p>
                            <p class="text-xs text-slate-500 mt-1">
                                Select a reservation above to browse its menu.
                            </p>
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
                                    Add items from the menu.
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

                                <div class="p-4 border-t border-slate-100">
                                    <button type="submit"
                                            :disabled="submitting || !selectedReservationId"
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
                                        <span x-text="submitting ? 'Placing…' : 'Place Pre-Order'"></span>
                                    </button>
                                </div>
                            </div>
                        </template>

                    </div>
                </div>

            </div>

        </form>
    @endif

</div>

@endsection
