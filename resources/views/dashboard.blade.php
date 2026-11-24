@extends('layouts.app')

@section('content')

<div class="space-y-8">

    {{-- Welcome Header --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-indigo-600 via-violet-600 to-purple-600 p-8 md:p-10 shadow-xl shadow-indigo-200/50">

        <div class="absolute -right-10 -top-16 h-64 w-64 rounded-full bg-white/10 blur-2xl"></div>
        <div class="absolute -bottom-20 right-1/3 h-48 w-48 rounded-full bg-purple-300/20 blur-2xl"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">

            <div>
                <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1.5 text-xs font-semibold text-white/90 backdrop-blur-sm">
                    <span class="h-2 w-2 rounded-full bg-green-300"></span>
                    Reservation Management System
                </span>

                <h1 class="mt-5 text-3xl md:text-4xl font-extrabold tracking-tight text-white">
                    Welcome back, {{ auth()->user()->name }}!
                </h1>

                <p class="mt-3 max-w-xl text-sm md:text-base leading-relaxed text-indigo-100">
                    Here's what's happening with your reservations today. Manage bookings, monitor activity and keep everything running smoothly.
                </p>
            </div>

            <div class="hidden md:flex h-24 w-24 shrink-0 items-center justify-center rounded-3xl border border-white/20 bg-white/10 text-5xl shadow-inner backdrop-blur-md">
                🏨
            </div>

        </div>
    </div>


    {{-- Statistics --}}
    <div>
        <div class="mb-5 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Overview</h2>
                <p class="mt-1 text-sm text-gray-500">Your reservation statistics at a glance</p>
            </div>

            <span class="rounded-full bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-600">
                Dashboard
            </span>
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">

            {{-- Total Reservations --}}
            <div class="group relative overflow-hidden rounded-2xl border border-gray-100 bg-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-indigo-100/60">

                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Reservations</p>
                        <h3 class="mt-3 text-3xl font-extrabold tracking-tight text-gray-900">
                            {{ $totalReservations }}
                        </h3>
                    </div>

                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-2xl transition-transform duration-300 group-hover:scale-110">
                        📅
                    </div>
                </div>

                <div class="mt-5 flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-indigo-500"></span>
                    <span class="text-xs font-medium text-gray-400">All bookings</span>
                </div>

                <div class="absolute -bottom-8 -right-8 h-24 w-24 rounded-full bg-indigo-50/70 transition-transform duration-500 group-hover:scale-150"></div>
            </div>


            {{-- Pending Reservations --}}
            <div class="group relative overflow-hidden rounded-2xl border border-gray-100 bg-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-amber-100/60">

                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Pending</p>
                        <h3 class="mt-3 text-3xl font-extrabold tracking-tight text-gray-900">
                            {{ $pendingReservations }}
                        </h3>
                    </div>

                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-2xl transition-transform duration-300 group-hover:scale-110">
                        ⏳
                    </div>
                </div>

                <div class="mt-5 flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                    <span class="text-xs font-medium text-gray-400">Awaiting confirmation</span>
                </div>

                <div class="absolute -bottom-8 -right-8 h-24 w-24 rounded-full bg-amber-50/70 transition-transform duration-500 group-hover:scale-150"></div>
            </div>


            {{-- Confirmed Reservations --}}
            <div class="group relative overflow-hidden rounded-2xl border border-gray-100 bg-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-100/60">

                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Confirmed</p>
                        <h3 class="mt-3 text-3xl font-extrabold tracking-tight text-gray-900">
                            {{ $confirmedReservations }}
                        </h3>
                    </div>

                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-2xl transition-transform duration-300 group-hover:scale-110">
                        ✅
                    </div>
                </div>

                <div class="mt-5 flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                    <span class="text-xs font-medium text-gray-400">Successfully confirmed</span>
                </div>

                <div class="absolute -bottom-8 -right-8 h-24 w-24 rounded-full bg-emerald-50/70 transition-transform duration-500 group-hover:scale-150"></div>
            </div>


            {{-- Available Tables --}}
            <div class="group relative overflow-hidden rounded-2xl border border-gray-100 bg-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-sky-100/60">

                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Available Tables</p>
                        <h3 class="mt-3 text-3xl font-extrabold tracking-tight text-gray-900">
                            {{ $availableTables }}
                        </h3>
                    </div>

                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sky-50 text-2xl transition-transform duration-300 group-hover:scale-110">
                        🪑
                    </div>
                </div>

                <div class="mt-5 flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>
                    <span class="text-xs font-medium text-gray-400">Ready for reservations</span>
                </div>

                <div class="absolute -bottom-8 -right-8 h-24 w-24 rounded-full bg-sky-50/70 transition-transform duration-500 group-hover:scale-150"></div>
            </div>

        </div>
    </div>


    {{-- Main Actions --}}
    <div>
        <div class="mb-5">
            <h2 class="text-xl font-bold text-gray-900">Quick Actions</h2>
            <p class="mt-1 text-sm text-gray-500">Access your frequently used features</p>
        </div>

        <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

            {{-- Voice Reservation --}}
            @can('use voice reservation')
                <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 to-violet-700 p-7 shadow-lg shadow-indigo-200/60 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl lg:col-span-2">

                    <div class="absolute -right-12 -top-12 h-48 w-48 rounded-full bg-white/10 transition-transform duration-500 group-hover:scale-125"></div>
                    <div class="absolute -bottom-16 right-24 h-40 w-40 rounded-full bg-purple-300/10"></div>

                    <div class="relative z-10 flex flex-col justify-between gap-6 sm:flex-row sm:items-center">

                        <div class="max-w-lg">
                            <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl border border-white/20 bg-white/15 text-3xl backdrop-blur-sm">
                                🎤
                            </div>

                            <h3 class="text-xl font-bold text-white">
                                Voice Reservation
                            </h3>

                            <p class="mt-2 text-sm leading-relaxed text-indigo-100">
                                Make reservations naturally using your voice. Simply speak your request and let the system handle the details.
                            </p>
                        </div>

                        <div class="shrink-0">
                            <button
                                type="button"
                                class="inline-flex items-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-bold text-indigo-700 shadow-lg transition-all duration-200 hover:bg-indigo-50 hover:shadow-xl active:scale-95"
                            >
                                <span>🎙️</span>
                                Start Reservation
                                <span>→</span>
                            </button>
                        </div>

                    </div>
                </div>
            @endcan


            {{-- Hotels --}}
            <div class="group relative overflow-hidden rounded-2xl border border-gray-100 bg-white p-7 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">

                <div class="flex items-center justify-between">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-violet-50 text-3xl transition-transform duration-300 group-hover:scale-110">
                        🏨
                    </div>

                    <span class="rounded-full bg-violet-50 px-3 py-1 text-xs font-semibold text-violet-600">
                        Hotels
                    </span>
                </div>

                <p class="mt-6 text-sm font-medium text-gray-500">
                    Total Hotels
                </p>

                <h3 class="mt-1 text-4xl font-extrabold tracking-tight text-gray-900">
                    {{ $totalHotels }}
                </h3>

                @can('create hotels')
                    <button
                        type="button"
                        class="mt-6 inline-flex w-full items-center justify-center gap-2 rounded-xl border border-violet-100 bg-violet-50 px-4 py-3 text-sm font-bold text-violet-700 transition-all hover:border-violet-200 hover:bg-violet-100 active:scale-[0.98]"
                    >
                        <span class="text-lg">+</span>
                        Add New Hotel
                    </button>
                @endcan

            </div>

        </div>
    </div>

</div>

@endsection

