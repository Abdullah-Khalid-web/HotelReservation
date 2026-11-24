
@extends('layouts.public')

@section('title', 'Your Reservation — ' . ($reservation->hotel->name ?? config('app.name')))

@section('content')

@php
    $hotel = $reservation->hotel ?? null;

    $isConfirmed = strtolower($reservation->status) === 'confirmed';
    $isPending = strtolower($reservation->status) === 'pending';
    $isCancelled = strtolower($reservation->status) === 'cancelled';

    $statusColor = $isConfirmed
        ? 'emerald'
        : ($isCancelled ? 'red' : 'amber');

    $statusLabel = ucfirst($reservation->status);

    $tableNumbers = $reservation->tables
        ->pluck('table_number')
        ->map(fn ($number) => 'Table ' . $number)
        ->join(', ');
@endphp

{{-- NAVIGATION --}}
<header class="sticky top-0 z-40 bg-white/95 backdrop-blur-xl border-b border-gray-100 print:hidden">
    <div class="max-w-7xl mx-auto px-5 lg:px-8">
        <div class="h-20 flex items-center justify-between">
            <a href="{{ route('landing') }}" class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-orange-500 flex items-center justify-center text-white text-xl shadow-lg">
                    ✦
                </div>
                <div>
                    <h1 class="text-lg font-extrabold text-gray-900">
                        {{ $hotel->name ?? config('app.name') }}
                    </h1>
                    <p class="text-[10px] uppercase tracking-[3px] text-gray-400 font-semibold">
                        Food • Flavor • Experience
                    </p>
                </div>
            </a>

            <a href="{{ route('landing') }}"
               class="text-sm font-bold text-gray-600 hover:text-orange-500 transition">
                ← Back to Restaurant
            </a>
        </div>
    </div>
</header>


{{-- HERO --}}
<section class="relative bg-gray-950 overflow-hidden print:hidden">
    <div class="absolute inset-0">
        <img src="https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=1800&q=85"
             alt="Restaurant dining"
             class="w-full h-full object-cover opacity-40">

        <div class="absolute inset-0 bg-gradient-to-r from-black/90 via-black/70 to-black/30"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-6 lg:px-8 py-12 md:py-16">
        <span class="text-orange-400 text-xs font-extrabold uppercase tracking-[3px]">
            Your dining experience awaits
        </span>

        <h1 class="text-4xl md:text-5xl font-black text-white mt-4">
            Your Reservation
            <span class="text-orange-400">Details.</span>
        </h1>

        <p class="text-gray-300 mt-4 max-w-xl">
            Thank you for choosing {{ $hotel->name ?? 'us' }}.
            Here are the details of your reservation.
        </p>
    </div>
</section>


{{-- MAIN CONTENT --}}
<section class="bg-[#fffaf5] min-h-screen py-12 md:py-16 print:bg-white print:py-4">
    <div class="max-w-3xl mx-auto px-5">

        {{-- STATUS CARD --}}
        <div class="bg-white rounded-3xl border border-gray-100 shadow-xl shadow-orange-100/40 overflow-hidden">

            <div class="p-8 sm:p-10 text-center">

                @if($isConfirmed)
                    <div class="mx-auto w-20 h-20 rounded-full bg-emerald-100 flex items-center justify-center text-4xl">
                        ✓
                    </div>

                    <span class="inline-flex mt-5 px-4 py-2 rounded-full bg-emerald-50 text-emerald-700 text-xs font-extrabold uppercase tracking-widest">
                        Reservation Confirmed
                    </span>

                    <h2 class="text-3xl sm:text-4xl font-black text-gray-900 mt-4">
                        You're All Set!
                    </h2>

                    <p class="text-gray-500 mt-3 max-w-md mx-auto leading-relaxed">
                        Great news! Your reservation has been confirmed.
                        We look forward to welcoming you.
                    </p>

                @elseif($isCancelled)
                    <div class="mx-auto w-20 h-20 rounded-full bg-red-100 flex items-center justify-center text-4xl">
                        ✕
                    </div>

                    <span class="inline-flex mt-5 px-4 py-2 rounded-full bg-red-50 text-red-700 text-xs font-extrabold uppercase tracking-widest">
                        Reservation Cancelled
                    </span>

                    <h2 class="text-3xl sm:text-4xl font-black text-gray-900 mt-4">
                        Booking Cancelled
                    </h2>

                    <p class="text-gray-500 mt-3 max-w-md mx-auto">
                        This reservation is no longer active.
                        Please contact the restaurant if you need assistance.
                    </p>

                @else
                    <div class="mx-auto w-20 h-20 rounded-full bg-amber-100 flex items-center justify-center text-4xl">
                        ⏳
                    </div>

                    <span class="inline-flex mt-5 px-4 py-2 rounded-full bg-amber-50 text-amber-700 text-xs font-extrabold uppercase tracking-widest">
                        Awaiting Confirmation
                    </span>

                    <h2 class="text-3xl sm:text-4xl font-black text-gray-900 mt-4">
                        Request Received!
                    </h2>

                    <p class="text-gray-500 mt-3 max-w-md mx-auto leading-relaxed">
                        Your reservation request has been submitted successfully.
                        Our team will contact you to confirm your booking.
                    </p>
                @endif

                <div class="mt-7 inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-gray-50 border border-gray-100">
                    <span class="text-gray-400 text-xs font-bold uppercase tracking-widest">
                        Booking ID
                    </span>
                    <span class="text-gray-900 font-black">
                        #{{ $reservation->id }}
                    </span>
                </div>
            </div>


            {{-- DIVIDER --}}
            <div class="relative border-t-2 border-dashed border-gray-200">
                <div class="absolute -left-3 -top-3 w-6 h-6 rounded-full bg-[#fffaf5]"></div>
                <div class="absolute -right-3 -top-3 w-6 h-6 rounded-full bg-[#fffaf5]"></div>
            </div>


            {{-- RESERVATION DETAILS --}}
            <div class="p-6 sm:p-9">
                <div class="flex items-center justify-between mb-7">
                    <div>
                        <p class="text-orange-500 text-xs font-extrabold uppercase tracking-widest">
                            Reservation Summary
                        </p>
                        <h3 class="text-2xl font-black text-gray-900 mt-1">
                            Booking Details
                        </h3>
                    </div>

                    <span class="text-3xl">🍽️</span>
                </div>

                {{-- DATE & TIME HIGHLIGHT --}}
                <div class="rounded-2xl bg-gray-950 text-white p-6 mb-7">
                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">
                                Reservation Date
                            </p>
                            <p class="text-lg sm:text-xl font-black mt-2">
                                {{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d M Y') }}
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ \Carbon\Carbon::parse($reservation->reservation_date)->format('l') }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">
                                Reservation Time
                            </p>
                            <p class="text-lg sm:text-xl font-black mt-2">
                                {{ \Carbon\Carbon::parse($reservation->reservation_time)->format('h:i A') }}
                            </p>
                            <p class="text-xs text-orange-400 mt-1">
                                Your dining time
                            </p>
                        </div>
                    </div>
                </div>


                {{-- CUSTOMER DETAILS --}}
                <div class="space-y-5">

                    <div class="flex items-center gap-4">
                        <div class="w-11 h-11 rounded-xl bg-orange-50 flex items-center justify-center text-xl shrink-0">
                            👤
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-400 font-semibold">Guest Name</p>
                            <p class="text-sm font-extrabold text-gray-900 mt-1">
                                {{ $reservation->guest_name }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="w-11 h-11 rounded-xl bg-orange-50 flex items-center justify-center text-xl shrink-0">
                            📞
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-400 font-semibold">Phone Number</p>
                            <p class="text-sm font-extrabold text-gray-900 mt-1">
                                {{ $reservation->guest_phone }}
                            </p>
                        </div>
                    </div>

                    @if($reservation->guest_email)
                        <div class="flex items-center gap-4">
                            <div class="w-11 h-11 rounded-xl bg-orange-50 flex items-center justify-center text-xl shrink-0">
                                ✉️
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs text-gray-400 font-semibold">Email Address</p>
                                <p class="text-sm font-extrabold text-gray-900 mt-1 break-all">
                                    {{ $reservation->guest_email }}
                                </p>
                            </div>
                        </div>
                    @endif

                    <div class="flex items-center gap-4">
                        <div class="w-11 h-11 rounded-xl bg-orange-50 flex items-center justify-center text-xl shrink-0">
                            👥
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-400 font-semibold">Number of Guests</p>
                            <p class="text-sm font-extrabold text-gray-900 mt-1">
                                {{ $reservation->number_of_guests }}
                                {{ $reservation->number_of_guests == 1 ? 'Person' : 'People' }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="w-11 h-11 rounded-xl bg-orange-50 flex items-center justify-center text-xl shrink-0">
                            🪑
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-400 font-semibold">Reserved Tables</p>
                            <p class="text-sm font-extrabold text-gray-900 mt-1">
                                {{ $tableNumbers ?: 'Not assigned yet' }}
                            </p>
                        </div>
                    </div>

                    @if($reservation->notes)
                        <div class="flex items-start gap-4">
                            <div class="w-11 h-11 rounded-xl bg-orange-50 flex items-center justify-center text-xl shrink-0">
                                📝
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs text-gray-400 font-semibold">Special Requests</p>
                                <p class="text-sm font-semibold text-gray-800 mt-1">
                                    {{ $reservation->notes }}
                                </p>
                            </div>
                        </div>
                    @endif
                </div>


                {{-- STATUS MESSAGE --}}
                @if($isPending)
                    <div class="mt-8 rounded-2xl bg-amber-50 border border-amber-200 p-5">
                        <div class="flex items-start gap-3">
                            <span class="text-2xl">⏳</span>
                            <div>
                                <h4 class="font-extrabold text-amber-900">
                                    Waiting for restaurant confirmation
                                </h4>
                                <p class="text-sm text-amber-800 mt-1 leading-relaxed">
                                    We'll contact you at
                                    <strong>{{ $reservation->guest_phone }}</strong>
                                    once your reservation has been reviewed.
                                </p>
                            </div>
                        </div>
                    </div>

                @elseif($isConfirmed)
                    <div class="mt-8 rounded-2xl bg-emerald-50 border border-emerald-200 p-5">
                        <div class="flex items-start gap-3">
                            <span class="text-2xl">🎉</span>
                            <div>
                                <h4 class="font-extrabold text-emerald-900">
                                    Your table is ready to welcome you!
                                </h4>
                                <p class="text-sm text-emerald-800 mt-1">
                                    Please arrive on time and show your booking reference if requested.
                                </p>
                            </div>
                        </div>
                    </div>

                @elseif($isCancelled)
                    <div class="mt-8 rounded-2xl bg-red-50 border border-red-200 p-5">
                        <p class="font-extrabold text-red-900">This reservation has been cancelled.</p>
                        <p class="text-sm text-red-700 mt-1">
                            Please contact the restaurant if you would like to make a new reservation.
                        </p>
                    </div>
                @endif
            </div>


            {{-- ACTIONS --}}
            <div class="bg-[#fffaf5] border-t border-orange-100 p-6 sm:p-8 print:hidden">
                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="button"
                            onclick="window.print()"
                            class="flex-1 py-4 rounded-xl bg-gray-900 text-white font-extrabold text-sm hover:bg-gray-800 transition">
                        🖨️ Print Reservation
                    </button>

                    <a href="{{ route('landing') }}"
                       class="flex-1 py-4 rounded-xl bg-orange-500 text-white font-extrabold text-sm text-center hover:bg-orange-600 transition">
                        Back to Restaurant →
                    </a>
                </div>

                <p class="text-center text-xs text-gray-400 mt-5">
                    Please keep your booking reference for future communication.
                </p>
            </div>
        </div>


        {{-- BOTTOM MESSAGE --}}
        <div class="text-center mt-10 print:hidden">
            <p class="text-gray-400 text-sm">
                Thank you for choosing
                <span class="font-extrabold text-gray-700">{{ $hotel->name ?? config('app.name') }}</span>.
            </p>
            <p class="text-orange-500 font-black text-lg mt-2">
                Come hungry. Leave happy. ❤️
            </p>
        </div>

    </div>
</section>


<style>
    @media print {
        header, footer, .print\:hidden {
            display: none !important;
        }

        body {
            background: white !important;
        }

        section {
            background: white !important;
        }

        .shadow-xl {
            box-shadow: none !important;
        }

        .max-w-3xl {
            max-width: 100% !important;
        }
    }
</style>

@endsection
