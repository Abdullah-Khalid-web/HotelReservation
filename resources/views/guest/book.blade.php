
@extends('layouts.public')

@section('title', 'Reserve Your Table — ' . $hotel->name)

@section('content')

{{-- NAVIGATION --}}
<header class="sticky top-0 z-40 bg-white/95 backdrop-blur-xl border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-5 lg:px-8">
        <div class="h-20 flex items-center justify-between">
            <a href="{{ route('landing') }}" class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-orange-500 flex items-center justify-center text-white text-xl shadow-lg">
                    ✦
                </div>
                <div>
                    <h1 class="text-lg font-extrabold text-gray-900">{{ $hotel->name }}</h1>
                    <p class="text-[10px] uppercase tracking-[3px] text-gray-400 font-semibold">
                        Food • Flavor • Experience
                    </p>
                </div>
            </a>

            <a href="{{ route('landing') }}"
               class="text-sm font-semibold text-gray-600 hover:text-orange-500 transition">
                ← Back to Restaurant
            </a>
        </div>
    </div>
</header>


{{-- HERO --}}
<section class="relative bg-gray-950 overflow-hidden">
    <div class="absolute inset-0">
        <img src="https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=1800&q=85"
             alt="Restaurant dining"
             class="w-full h-full object-cover opacity-50">

        <div class="absolute inset-0 bg-gradient-to-r from-black/90 via-black/70 to-black/30"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-6 lg:px-8 py-16 md:py-20">
        <div class="max-w-2xl">
            <span class="inline-flex items-center gap-2 text-orange-400 text-xs font-extrabold uppercase tracking-[3px]">
                <span class="w-2 h-2 rounded-full bg-orange-400"></span>
                Your experience starts here
            </span>

            <h1 class="text-4xl md:text-6xl font-black text-white mt-5 leading-tight">
                Your Table.
                <span class="text-orange-400">Your Moment.</span>
            </h1>

            <p class="text-gray-300 text-base md:text-lg mt-5 leading-relaxed max-w-xl">
                Good food tastes even better when everything is ready for you.
                Choose your date, time and table. We'll take care of the rest.
            </p>

            <div class="flex flex-wrap gap-5 mt-8 text-sm text-gray-200">
                <span>✓ No account required</span>
                <span>✓ Easy reservation</span>
                <span>✓ Personal confirmation</span>
            </div>
        </div>
    </div>
</section>


{{-- BOOKING FORM --}}
<section class="bg-[#fffaf5] py-12 md:py-16">
    <div class="max-w-6xl mx-auto px-5 lg:px-8">

        {{-- SUCCESS MESSAGE --}}
        @if(session('success'))
            <div class="mb-7 p-5 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-800 flex items-start gap-3">
                <span class="text-2xl">✓</span>

                <div>
                    <p class="font-extrabold">Reservation submitted!</p>
                    <p class="text-sm mt-1">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        {{-- VALIDATION ERRORS --}}
        @if($errors->any())
            <div class="mb-7 p-5 bg-red-50 border border-red-200 rounded-2xl text-red-700">
                <p class="font-bold mb-2">Please check the following details:</p>

                <ul class="list-disc pl-5 text-sm space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid lg:grid-cols-3 gap-8 items-start">

            {{-- MAIN FORM --}}
            <div class="lg:col-span-2">

                <div class="mb-7">
                    <span class="text-orange-500 text-xs font-extrabold uppercase tracking-[3px]">
                        Let's get you seated
                    </span>

                    <h2 class="text-3xl md:text-4xl font-black text-gray-900 mt-2">
                        Make a Reservation
                    </h2>

                    <p class="text-gray-500 mt-2">
                        Fill in your details below. It only takes a minute.
                    </p>
                </div>

                <form method="POST"
                      action="{{ route('guest.book.store') }}"
                      id="guestBookingForm"
                      class="bg-white rounded-3xl border border-orange-100 shadow-xl shadow-orange-100/40 overflow-hidden">

                    @csrf

                    {{-- SECTION 1: GUEST INFORMATION --}}
                    <div class="p-6 sm:p-8">

                        <div class="flex items-center gap-3 mb-7">
                            <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-black">
                                01
                            </div>

                            <div>
                                <h3 class="text-lg font-extrabold text-gray-900">
                                    Your Information
                                </h3>

                                <p class="text-xs text-gray-400">
                                    How can we reach you?
                                </p>
                            </div>
                        </div>

                        <div class="grid sm:grid-cols-2 gap-5">

                            {{-- NAME --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    Full Name <span class="text-orange-500">*</span>
                                </label>

                                <input type="text"
                                       name="guest_name"
                                       value="{{ old('guest_name') }}"
                                       placeholder="Enter your name"
                                       required
                                       class="w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3.5 text-sm focus:bg-white focus:border-orange-500 focus:ring-orange-500 transition">

                                @error('guest_name')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- PHONE --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    Phone Number <span class="text-orange-500">*</span>
                                </label>

                                <input type="tel"
                                       name="guest_phone"
                                       value="{{ old('guest_phone') }}"
                                       placeholder="+92 300 1234567"
                                       required
                                       class="w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3.5 text-sm focus:bg-white focus:border-orange-500 focus:ring-orange-500 transition">

                                @error('guest_phone')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- EMAIL --}}
                        <div class="mt-5">
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Email Address
                                <span class="font-normal text-gray-400">(Optional)</span>
                            </label>

                            <input type="email"
                                   name="guest_email"
                                   value="{{ old('guest_email') }}"
                                   placeholder="you@example.com"
                                   class="w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3.5 text-sm focus:bg-white focus:border-orange-500 focus:ring-orange-500 transition">

                            @error('guest_email')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="border-t border-gray-100"></div>


                    {{-- SECTION 2: DATE AND TIME --}}
                    <div class="p-6 sm:p-8">

                        <div class="flex items-center gap-3 mb-7">
                            <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-black">
                                02
                            </div>

                            <div>
                                <h3 class="text-lg font-extrabold text-gray-900">
                                    Date & Time
                                </h3>

                                <p class="text-xs text-gray-400">
                                    When would you like to visit?
                                </p>
                            </div>
                        </div>

                        <div class="grid sm:grid-cols-3 gap-5">

                            {{-- DATE --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    Date <span class="text-orange-500">*</span>
                                </label>

                                <input type="date"
                                       name="reservation_date"
                                       id="reservation_date"
                                       value="{{ old('reservation_date', date('Y-m-d')) }}"
                                       min="{{ date('Y-m-d') }}"
                                       required
                                       class="w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-3.5 text-sm focus:bg-white focus:border-orange-500 focus:ring-orange-500 transition">
                            </div>

                            {{-- TIME --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    Time <span class="text-orange-500">*</span>
                                </label>

                                <input type="time"
                                       name="reservation_time"
                                       id="reservation_time"
                                       value="{{ old('reservation_time', '19:00') }}"
                                       required
                                       class="w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-3.5 text-sm focus:bg-white focus:border-orange-500 focus:ring-orange-500 transition">
                            </div>

                            {{-- GUESTS --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    Guests <span class="text-orange-500">*</span>
                                </label>

                                <input type="number"
                                       name="number_of_guests"
                                       id="number_of_guests"
                                       value="{{ old('number_of_guests', 2) }}"
                                       min="1"
                                       max="50"
                                       required
                                       class="w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-3.5 text-sm focus:bg-white focus:border-orange-500 focus:ring-orange-500 transition">
                            </div>

                        </div>
                    </div>

                    <div class="border-t border-gray-100"></div>


                    {{-- SECTION 3: AVAILABLE TABLES --}}
                    <div class="p-6 sm:p-8">

                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-black">
                                03
                            </div>

                            <div>
                                <h3 class="text-lg font-extrabold text-gray-900">
                                    Choose Your Table
                                </h3>

                                <p class="text-xs text-gray-400">
                                    Check availability and select your preferred table.
                                </p>
                            </div>
                        </div>

                        {{-- AVAILABILITY LEGEND --}}
                        <div class="flex flex-wrap gap-4 text-xs text-gray-500 mb-6 ml-1">
                            <span class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-sm border-2 border-gray-300"></span>
                                Available selection
                            </span>

                            <span class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-sm bg-orange-500"></span>
                                Selected
                            </span>
                        </div>

                        {{-- AVAILABILITY BUTTON --}}
                        <div class="mb-5">
                            <button type="button"
                                    id="checkAvailabilityBtn"
                                    class="w-full sm:w-auto px-6 py-3 rounded-xl bg-gray-900 text-white text-sm font-extrabold hover:bg-gray-800 transition disabled:opacity-60 disabled:cursor-not-allowed">

                                <span id="availabilityButtonText">
                                    Check Availability
                                </span>
                            </button>
                        </div>

                        {{-- AVAILABILITY STATUS --}}
                        <div id="availabilityMessage"
                             class="hidden mb-5 rounded-xl p-4 text-sm"
                             role="status"
                             aria-live="polite">
                        </div>

                        {{-- TABLE RESULTS --}}
                        <div id="availableTables"
                             class="grid grid-cols-2 sm:grid-cols-3 gap-3">

                            <div class="col-span-full rounded-2xl bg-orange-50 border border-orange-100 p-6 text-center">
                                <div class="text-3xl mb-2">🪑</div>

                                <p class="font-bold text-gray-800">
                                    Find your perfect table
                                </p>

                                <p class="text-sm text-gray-500 mt-1">
                                    Select your date, time and number of guests, then check availability.
                                </p>
                            </div>

                        </div>

                        {{-- TABLE ERRORS --}}
                        @error('table_ids')
                            <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                        @enderror

                        @error('table_ids.*')
                            <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                        @enderror

                    </div>

                    <div class="border-t border-gray-100"></div>


                    {{-- SECTION 4: SPECIAL REQUESTS --}}
                    <div class="p-6 sm:p-8">

                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-black">
                                04
                            </div>

                            <div>
                                <h3 class="text-lg font-extrabold text-gray-900">
                                    Special Requests
                                </h3>

                                <p class="text-xs text-gray-400">
                                    Make your visit extra special.
                                </p>
                            </div>
                        </div>

                        <textarea name="notes"
                                  rows="4"
                                  placeholder="Birthday celebration, food allergies, window seat or anything else..."
                                  class="w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3.5 text-sm focus:bg-white focus:border-orange-500 focus:ring-orange-500 transition">{{ old('notes') }}</textarea>
                    </div>


                    {{-- SUBMIT --}}
                    <div class="p-6 sm:p-8 bg-[#fffaf5] border-t border-orange-100">

                        <button type="submit"
                                id="submitReservationBtn"
                                class="w-full py-4 rounded-xl bg-orange-500 text-white font-black text-base shadow-xl shadow-orange-200 hover:bg-orange-600 hover:-translate-y-0.5 active:scale-[0.99] transition-all">
                            Confirm My Reservation →
                        </button>

                        <p class="text-xs text-gray-400 text-center mt-4 leading-relaxed">
                            By submitting, you request a table reservation.
                            The restaurant will confirm your booking.
                        </p>
                    </div>

                </form>
            </div>


            {{-- SIDEBAR --}}
            <aside class="space-y-6 lg:sticky lg:top-28">

                {{-- RESTAURANT CARD --}}
                <div class="bg-gray-950 rounded-3xl overflow-hidden shadow-xl">

                    <div class="h-48 relative">
                        <img src="https://images.unsplash.com/photo-1559339352-11d035aa65de?w=900&q=85"
                             alt="Restaurant atmosphere"
                             class="w-full h-full object-cover">

                        <div class="absolute inset-0 bg-gradient-to-t from-gray-950/90 to-transparent"></div>

                        <div class="absolute bottom-4 left-5 right-5">
                            <p class="text-orange-400 text-xs font-extrabold uppercase tracking-widest">
                                Your dining destination
                            </p>

                            <h3 class="text-white text-xl font-black mt-1">
                                {{ $hotel->name }}
                            </h3>
                        </div>
                    </div>

                    <div class="p-6">
                        <h4 class="text-white font-extrabold text-lg">
                            Why book with us?
                        </h4>

                        <div class="space-y-4 mt-5">

                            <div class="flex gap-3 items-center">
                                <span class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center">
                                    🍽️
                                </span>

                                <div>
                                    <p class="text-white text-sm font-bold">
                                        A great dining experience
                                    </p>

                                    <p class="text-gray-400 text-xs">
                                        Make every meal memorable.
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-3 items-center">
                                <span class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center">
                                    ⚡
                                </span>

                                <div>
                                    <p class="text-white text-sm font-bold">
                                        Simple booking
                                    </p>

                                    <p class="text-gray-400 text-xs">
                                        No account required.
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-3 items-center">
                                <span class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center">
                                    🤝
                                </span>

                                <div>
                                    <p class="text-white text-sm font-bold">
                                        Personal confirmation
                                    </p>

                                    <p class="text-gray-400 text-xs">
                                        We'll get in touch with you.
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>


                {{-- CONTACT CARD --}}
                <div class="bg-white rounded-2xl border border-orange-100 p-6">

                    <h4 class="font-extrabold text-gray-900">
                        Need assistance?
                    </h4>

                    <p class="text-sm text-gray-500 mt-2">
                        Have a question about your reservation? Get in touch with our team.
                    </p>

                    @if($hotel->phone)
                        <a href="tel:{{ $hotel->phone }}"
                           class="mt-5 flex items-center gap-3 text-orange-600 font-extrabold text-sm hover:text-orange-700">

                            <span class="w-9 h-9 bg-orange-50 rounded-xl flex items-center justify-center">
                                📞
                            </span>

                            {{ $hotel->phone }}
                        </a>
                    @endif

                    @if($hotel->email)
                        <a href="mailto:{{ $hotel->email }}"
                           class="mt-3 flex items-center gap-3 text-orange-600 font-extrabold text-sm hover:text-orange-700 break-all">

                            <span class="w-9 h-9 bg-orange-50 rounded-xl flex items-center justify-center shrink-0">
                                ✉️
                            </span>

                            {{ $hotel->email }}
                        </a>
                    @endif

                </div>


                {{-- REMINDER CARD --}}
                <div class="rounded-2xl bg-orange-100 p-6">
                    <p class="text-orange-700 text-xs font-extrabold uppercase tracking-widest">
                        A little reminder
                    </p>

                    <p class="text-gray-900 text-lg font-black mt-2">
                        Come hungry. Leave happy. ❤️
                    </p>
                </div>

            </aside>

        </div>
    </div>
</section>


{{-- FOOTER --}}
<footer class="bg-black text-gray-400 py-7">
    <div class="max-w-7xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-3">

        <p class="text-white font-extrabold">
            {{ $hotel->name }}
        </p>

        <p class="text-xs">
            © {{ date('Y') }} {{ $hotel->name }}. All rights reserved.
        </p>

        <a href="{{ route('landing') }}"
           class="text-sm font-bold text-orange-400 hover:text-orange-300">
            Back to Restaurant ↑
        </a>
    </div>
</footer>


{{-- TABLE AVAILABILITY SCRIPT --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const dateInput = document.getElementById('reservation_date');
    const timeInput = document.getElementById('reservation_time');
    const guestsInput = document.getElementById('number_of_guests');

    const checkButton = document.getElementById('checkAvailabilityBtn');
    const buttonText = document.getElementById('availabilityButtonText');

    const tablesContainer = document.getElementById('availableTables');
    const messageContainer = document.getElementById('availabilityMessage');

    const bookingForm = document.getElementById('guestBookingForm');

    let currentController = null;
    let availabilityChecked = false;

    function showMessage(message, type = 'info') {

        messageContainer.classList.remove('hidden');

        messageContainer.className = 'mb-5 rounded-xl p-4 text-sm';

        if (type === 'success') {
            messageContainer.classList.add(
                'bg-emerald-50',
                'border',
                'border-emerald-200',
                'text-emerald-700'
            );
        } else if (type === 'error') {
            messageContainer.classList.add(
                'bg-red-50',
                'border',
                'border-red-200',
                'text-red-700'
            );
        } else {
            messageContainer.classList.add(
                'bg-orange-50',
                'border',
                'border-orange-200',
                'text-orange-700'
            );
        }

        messageContainer.textContent = message;
    }

    function hideMessage() {
        messageContainer.classList.add('hidden');
        messageContainer.textContent = '';
    }

    function resetTables(message = null) {

        availabilityChecked = false;

        tablesContainer.innerHTML = '';

        const placeholder = document.createElement('div');

        placeholder.className =
            'col-span-full rounded-2xl bg-orange-50 border border-orange-100 p-6 text-center';

        placeholder.innerHTML = `
            <div class="text-3xl mb-2">🪑</div>
            <p class="font-bold text-gray-800">Find your perfect table</p>
            <p class="text-sm text-gray-500 mt-1">
                Select your date, time and number of guests, then check availability.
            </p>
        `;

        tablesContainer.appendChild(placeholder);

        if (message) {
            showMessage(message, 'info');
        } else {
            hideMessage();
        }
    }

    function setLoading(loading) {

        checkButton.disabled = loading;

        buttonText.textContent = loading
            ? 'Checking availability...'
            : 'Check Availability';
    }

    function createTableCard(table) {

        const label = document.createElement('label');

        label.className = 'cursor-pointer group block';

        const input = document.createElement('input');

        input.type = 'checkbox';
        input.name = 'table_ids[]';
        input.value = table.id;
        input.className = 'peer sr-only table-selection-checkbox';

        const card = document.createElement('div');

        card.className = [
            'relative rounded-2xl border-2 border-gray-200 bg-gray-50 p-5 text-center',
            'hover:border-orange-300 hover:bg-orange-50',
            'peer-checked:border-orange-500 peer-checked:bg-orange-50',
            'transition'
        ].join(' ');

        const checkMark = document.createElement('span');

        checkMark.className = [
            'absolute top-2 right-2 w-5 h-5 rounded-full',
            'bg-orange-500 text-white text-xs',
            'items-center justify-center hidden'
        ].join(' ');

        checkMark.textContent = '✓';

        const icon = document.createElement('div');

        icon.className = 'text-3xl mb-3';
        icon.textContent = '🪑';

        const tableName = document.createElement('p');

        tableName.className = 'font-extrabold text-gray-900 text-sm';
        tableName.textContent = 'Table ' + table.table_number;

        const capacity = document.createElement('p');

        capacity.className = 'text-xs text-gray-500 mt-1';
        capacity.textContent = table.capacity + ' Seats';

        card.appendChild(checkMark);
        card.appendChild(icon);
        card.appendChild(tableName);
        card.appendChild(capacity);

        label.appendChild(input);
        label.appendChild(card);

        // Selected table indicator
        input.addEventListener('change', function () {

            if (input.checked) {
                card.classList.add(
                    'border-orange-500',
                    'bg-orange-50'
                );

                card.classList.remove('border-gray-200', 'bg-gray-50');
                checkMark.classList.remove('hidden');
                checkMark.classList.add('flex');

            } else {
                card.classList.remove(
                    'border-orange-500',
                    'bg-orange-50'
                );

                card.classList.add('border-gray-200', 'bg-gray-50');
                checkMark.classList.remove('flex');
                checkMark.classList.add('hidden');
            }
        });

        return label;
    }

    async function checkAvailability() {

        const date = dateInput.value;
        const time = timeInput.value;
        const guests = guestsInput.value;

        if (!date || !time || !guests) {
            showMessage(
                'Please select reservation date, time and number of guests first.',
                'error'
            );
            return;
        }

        if (parseInt(guests, 10) < 1) {
            showMessage(
                'Number of guests must be at least 1.',
                'error'
            );
            return;
        }

        // Cancel any previous availability request.
        if (currentController) {
            currentController.abort();
        }

        const controller = new AbortController();

        currentController = controller;

        availabilityChecked = false;

        tablesContainer.innerHTML = '';

        hideMessage();

        setLoading(true);

        const loadingElement = document.createElement('div');

        loadingElement.className =
            'col-span-full rounded-2xl border border-gray-200 bg-gray-50 p-8 text-center';

        loadingElement.innerHTML = `
            <div class="animate-spin rounded-full h-8 w-8 border-4 border-orange-200 border-t-orange-500 mx-auto mb-3"></div>
            <p class="font-bold text-gray-800">Checking available tables</p>
            <p class="text-sm text-gray-500 mt-1">
                Please wait while we find tables for your reservation.
            </p>
        `;

        tablesContainer.appendChild(loadingElement);

        try {

            const url = new URL(
                @json(route('guest.book.search')),
                window.location.origin
            );

            url.searchParams.set('reservation_date', date);
            url.searchParams.set('reservation_time', time);
            url.searchParams.set('number_of_guests', guests);

            const response = await fetch(url.toString(), {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                signal: controller.signal
            });

            const result = await response.json();

            // Ignore response if this request is no longer current.
            if (currentController !== controller) {
                return;
            }

            if (!response.ok) {
                throw new Error(
                    result.message || 'Unable to check table availability.'
                );
            }

            tablesContainer.innerHTML = '';

            if (!result.success || !Array.isArray(result.tables)) {
                throw new Error(
                    result.message || 'Invalid availability response.'
                );
            }

            if (result.tables.length === 0) {

                availabilityChecked = true;

                const emptyState = document.createElement('div');

                emptyState.className =
                    'col-span-full rounded-2xl bg-orange-50 border border-orange-100 p-6 text-center';

                emptyState.innerHTML = `
                    <div class="text-3xl mb-2">😔</div>
                    <p class="font-bold text-gray-800">
                        No tables available for this time
                    </p>
                    <p class="text-sm text-gray-500 mt-1">
                        Try another time or choose a different date.
                    </p>
                `;

                tablesContainer.appendChild(emptyState);

                showMessage(
                    'No tables are available for your selected date, time and number of guests.',
                    'error'
                );

                return;
            }

            // Render only tables returned by backend availability check.
            result.tables.forEach(function (table) {
                tablesContainer.appendChild(createTableCard(table));
            });

            availabilityChecked = true;

            showMessage(
                result.tables.length + ' table(s) available. Select your preferred table below.',
                'success'
            );

        } catch (error) {

            if (error.name === 'AbortError') {
                return;
            }

            if (currentController !== controller) {
                return;
            }

            tablesContainer.innerHTML = '';

            const errorElement = document.createElement('div');

            errorElement.className =
                'col-span-full rounded-2xl bg-red-50 border border-red-100 p-6 text-center';

            errorElement.innerHTML = `
                <div class="text-3xl mb-2">⚠️</div>
                <p class="font-bold text-red-800">
                    Unable to check availability
                </p>
                <p class="text-sm text-red-600 mt-1">
                    Please try again.
                </p>
            `;

            tablesContainer.appendChild(errorElement);

            showMessage(
                error.message || 'Something went wrong. Please try again.',
                'error'
            );

        } finally {

            // Only the latest request may update the button.
            if (currentController === controller) {
                setLoading(false);
            }
        }
    }

    // Check availability button
    checkButton.addEventListener('click', checkAvailability);

    // Changing date/time/guests invalidates the previously returned tables.
    [dateInput, timeInput, guestsInput].forEach(function (input) {

        input.addEventListener('change', function () {

            if (currentController) {
                currentController.abort();
                currentController = null;
            }

            setLoading(false);

            resetTables(
                'Your reservation details changed. Check availability again to see the updated tables.'
            );
        });
    });

    // Prevent submission without a valid availability check and selected table.
    bookingForm.addEventListener('submit', function (event) {

        const selectedTables = tablesContainer.querySelectorAll(
            'input[name="table_ids[]"]:checked'
        );

        if (!availabilityChecked) {

            event.preventDefault();

            showMessage(
                'Please check table availability for your selected date and time first.',
                'error'
            );

            checkButton.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });

            return;
        }

        if (selectedTables.length === 0) {

            event.preventDefault();

            showMessage(
                'Please select at least one available table before submitting your reservation.',
                'error'
            );

            tablesContainer.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }
    });

});
</script>

@endsection
