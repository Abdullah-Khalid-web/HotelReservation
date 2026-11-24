<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Hotel Reservation System') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }

        .sidebar-transition {
            transition: width 300ms ease, transform 300ms ease;
        }

        .sidebar-scroll::-webkit-scrollbar {
            width: 6px;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 3px;
        }
        .sidebar-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-900 antialiased">

    <div x-data="{
            sidebarOpen: false,
            sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
            toggleSidebar() {
                if (window.innerWidth < 1024) {
                    this.sidebarOpen = !this.sidebarOpen;
                } else {
                    this.sidebarCollapsed = !this.sidebarCollapsed;
                    localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
                }
            },
            closeSidebar() {
                this.sidebarOpen = false;
            }
         }"
         class="min-h-screen">

        {{-- ═══════════ MOBILE OVERLAY ═══════════ --}}
        <div x-show="sidebarOpen"
             x-cloak
             x-transition.opacity
             @click="closeSidebar()"
             class="fixed inset-0 bg-black/50 z-[60] lg:hidden">
        </div>


        {{-- ═══════════ SIDEBAR ═══════════ --}}
        @include('layouts.sidebar')


        {{-- ═══════════ CONTENT WRAPPER (offset by sidebar width) ═══════════ --}}
        <div :class="{
                'lg:pl-64': !sidebarCollapsed,
                'lg:pl-20': sidebarCollapsed
             }"
             class="transition-all duration-300 ease-in-out">

            {{-- ═══════════ HEADER ═══════════ --}}
            @include('layouts.header')


            {{-- ═══════════ MAIN CONTENT ═══════════ --}}
            <main class="p-6">
                @yield('content')
            </main>


            {{-- ═══════════ FOOTER ═══════════ --}}
            @include('layouts.footer')

        </div>

    </div>


    {{-- ═══════════ CHATBOT ═══════════ --}}
    @include('layouts.chatbot')


    @stack('scripts')

</body>
</html>
