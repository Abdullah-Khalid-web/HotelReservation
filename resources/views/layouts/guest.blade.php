
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Hotel Reservation') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] {
            display: none !important;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        .animate-float {
            animation: float 7s ease-in-out infinite;
        }

        .animate-float-slow {
            animation: float 9s ease-in-out infinite;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeUp {
            animation: fadeUp .6s ease-out both;
        }

        .auth-background {
            background:
                radial-gradient(circle at 10% 10%, rgba(217,119,6,.07), transparent 30%),
                radial-gradient(circle at 90% 90%, rgba(180,83,9,.06), transparent 30%),
                #faf9f6;
        }
    </style>
</head>

<body class="font-sans text-gray-900 antialiased bg-[#faf9f6]">

<div class="min-h-screen flex flex-col">

    {{-- HEADER --}}
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-xl
                   border-b border-gray-200/80 shadow-sm">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8
                    h-[76px] flex items-center justify-between">

            {{-- Brand --}}
            <a href="{{ route('landing') }}"
               class="flex items-center gap-3 group">

                <div class="w-11 h-11 rounded-xl
                            bg-gradient-to-br from-amber-600 to-orange-700
                            flex items-center justify-center text-xl
                            shadow-lg shadow-amber-200/50
                            group-hover:scale-105 transition-transform">
                    🏨
                </div>

                <div>
                    <p class="font-extrabold text-gray-900 text-base sm:text-lg
                              tracking-tight leading-tight">
                        {{ config('app.name', 'Hotel Reservation') }}
                    </p>

                    <p class="text-[10px] sm:text-xs text-amber-700
                              font-semibold tracking-widest uppercase">
                        Dine. Relax. Enjoy.
                    </p>
                </div>
            </a>

            {{-- Right Navigation --}}
            <div class="flex items-center gap-4">

                <a href="{{ route('landing') }}"
                   class="inline-flex items-center gap-2 text-sm
                          font-semibold text-gray-600
                          hover:text-amber-700 transition-colors">

                    <svg class="w-4 h-4" fill="none"
                         stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M3 10.5L12 3l9 7.5M5 9v11h14V9"/>
                    </svg>

                    <span class="hidden sm:inline">Back to Home</span>
                    <span class="sm:hidden">Home</span>
                </a>

                <span class="hidden sm:block w-px h-6 bg-gray-200"></span>

                <div class="hidden sm:flex items-center gap-2
                            text-xs font-semibold text-emerald-700">

                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Online Booking
                </div>

            </div>
        </div>
    </header>


    {{-- MAIN CONTENT --}}
    <main class="flex-1">

        <div class="min-h-[calc(100vh-76px)]
                    flex flex-col lg:flex-row">

            {{-- LEFT BRANDING PANEL --}}
            <section class="hidden lg:flex lg:w-1/2
                            relative overflow-hidden
                            bg-gradient-to-br from-stone-950
                            via-amber-950 to-orange-950">

                {{-- Decorative Background --}}
                <div class="absolute -top-32 -left-24
                            w-96 h-96 bg-amber-500/15
                            rounded-full blur-3xl animate-float"></div>

                <div class="absolute bottom-0 -right-20
                            w-[30rem] h-[30rem] bg-orange-500/20
                            rounded-full blur-3xl animate-float-slow"></div>

                <div class="absolute top-1/3 right-1/4
                            w-64 h-64 bg-yellow-500/10
                            rounded-full blur-3xl"></div>

                {{-- Subtle Grid --}}
                <div class="absolute inset-0 opacity-[0.05]"
                     style="background-image: linear-gradient(rgba(255,255,255,.5) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.5) 1px, transparent 1px); background-size: 40px 40px;">
                </div>

                {{-- Panel Content --}}
                <div class="relative z-10 flex flex-col
                            justify-between p-10 xl:p-16
                            text-white w-full">

                    {{-- Brand --}}
                    <div class="flex items-center gap-3">

                        <div class="w-12 h-12 rounded-2xl
                                    bg-white/10 backdrop-blur-md
                                    border border-white/15
                                    flex items-center justify-center text-2xl">
                            🏨
                        </div>

                        <div>
                            <p class="text-lg font-extrabold tracking-tight">
                                {{ config('app.name', 'Hotel Reservation') }}
                            </p>

                            <p class="text-xs text-amber-200">
                                A better way to enjoy dining
                            </p>
                        </div>
                    </div>


                    {{-- Hero Content --}}
                    <div class="max-w-xl py-12">

                        <span class="inline-flex items-center gap-2
                                     px-4 py-2 rounded-full
                                     bg-amber-400/10 border border-amber-300/20
                                     text-amber-200 text-xs font-bold
                                     tracking-wider mb-7">

                            <span class="w-2 h-2 rounded-full bg-amber-400"></span>

                            YOUR NEXT GREAT MEAL STARTS HERE
                        </span>

                        <h1 class="text-4xl xl:text-5xl
                                   font-extrabold leading-[1.15]
                                   tracking-tight mb-6">

                            Good food.
                            <br>

                            <span class="text-transparent bg-clip-text
                                         bg-gradient-to-r from-amber-200
                                         via-yellow-100 to-orange-300">
                                Great moments.
                            </span>
                        </h1>

                        <p class="text-stone-200/90 text-base xl:text-lg
                                  leading-relaxed max-w-md">
                            Make every dining experience special.
                            Book your table, explore delicious dishes
                            and have everything ready before you arrive.
                        </p>


                        {{-- Features --}}
                        <div class="mt-9 space-y-5">

                            <div class="flex items-center gap-4">
                                <div class="w-11 h-11 rounded-xl
                                            bg-white/10 border border-white/10
                                            flex items-center justify-center">
                                    <svg class="w-5 h-5 text-amber-300"
                                         fill="none" stroke="currentColor"
                                         stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              d="M8 7V3m8 4V3m-9 8h10M5
                                              21h14a2 2 0 002-2V7a2 2
                                              0 00-2-2H5a2 2 0
                                              00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>

                                <div>
                                    <p class="font-bold text-sm">
                                        Effortless Reservations
                                    </p>
                                    <p class="text-xs text-stone-300 mt-1">
                                        Find your table and reserve in seconds.
                                    </p>
                                </div>
                            </div>


                            <div class="flex items-center gap-4">
                                <div class="w-11 h-11 rounded-xl
                                            bg-white/10 border border-white/10
                                            flex items-center justify-center">
                                    <svg class="w-5 h-5 text-amber-300"
                                         fill="none" stroke="currentColor"
                                         stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              d="M3 3h2l.4 2M7 13h10l4-8H5.4
                                              M7 13L5.4 5M7 13l-2.293
                                              2.293c-.63.63-.184 1.707.707
                                              1.707H17"/>
                                    </svg>
                                </div>

                                <div>
                                    <p class="font-bold text-sm">
                                        Pre-Order Your Favorites
                                    </p>
                                    <p class="text-xs text-stone-300 mt-1">
                                        Your favorite dishes, ready when you arrive.
                                    </p>
                                </div>
                            </div>


                            <div class="flex items-center gap-4">
                                <div class="w-11 h-11 rounded-xl
                                            bg-white/10 border border-white/10
                                            flex items-center justify-center">
                                    <svg class="w-5 h-5 text-amber-300"
                                         fill="none" stroke="currentColor"
                                         stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              d="M12 3a9 9 0 100 18 9 9
                                              0 000-18zm-3 9h6m-3-3v6"/>
                                    </svg>
                                </div>

                                <div>
                                    <p class="font-bold text-sm">
                                        Your Personal AI Assistant
                                    </p>
                                    <p class="text-xs text-stone-300 mt-1">
                                        Get instant help with reservations and orders.
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>


                    {{-- Bottom --}}
                    <div class="flex items-center justify-between
                                border-t border-white/15 pt-6">

                        <p class="text-xs text-stone-300">
                            © {{ date('Y') }} {{ config('app.name') }}
                        </p>

                        <p class="text-xs text-amber-200">
                            Crafted for memorable moments
                        </p>
                    </div>

                </div>
            </section>


            {{-- RIGHT FORM PANEL --}}
            <section class="flex-1 auth-background
                            flex flex-col justify-center items-center
                            px-4 py-12 sm:px-8 lg:px-12">

                {{-- Mobile Brand --}}
                <a href="{{ route('landing') }}"
                   class="lg:hidden mb-8 flex items-center gap-3">

                    <div class="w-11 h-11 rounded-xl
                                bg-gradient-to-br from-amber-600 to-orange-700
                                flex items-center justify-center text-xl
                                shadow-lg shadow-amber-200/50">
                        🏨
                    </div>

                    <div>
                        <p class="font-extrabold text-lg text-gray-900">
                            {{ config('app.name', 'Hotel Reservation') }}
                        </p>

                        <p class="text-[10px] text-amber-700
                                  font-semibold uppercase tracking-widest">
                            Dine. Relax. Enjoy.
                        </p>
                    </div>
                </a>


                {{-- Form --}}
                <div class="w-full max-w-md animate-fadeUp">

                    <div class="bg-white rounded-3xl
                                border border-stone-200/80
                                shadow-2xl shadow-stone-900/[0.06]
                                p-6 sm:p-9">

                        {{ $slot }}

                    </div>


                    {{-- Help --}}
                    <div class="mt-6 flex items-center justify-center gap-2
                                text-xs text-gray-500">

                        <svg class="w-4 h-4 text-amber-700"
                             fill="none" stroke="currentColor"
                             stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M8 10h.01M12 10h.01M16
                                  10h.01M9 16H5a2 2 0
                                  01-2-2V6a2 2 0
                                  012-2h14a2 2 0
                                  012 2v8a2 2
                                  0 01-2 2h-4l-3 3-3-3z"/>
                        </svg>

                        Need help? Chat with our AI assistant.
                    </div>


                    {{-- Home Link --}}
                    <div class="mt-5 text-center">

                        <a href="{{ route('landing') }}"
                           class="inline-flex items-center gap-2
                                  text-sm font-semibold text-gray-500
                                  hover:text-amber-700 transition-colors">

                            <svg class="w-4 h-4" fill="none"
                                 stroke="currentColor" stroke-width="2"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>

                            Back to home
                        </a>
                    </div>

                </div>
            </section>

        </div>
    </main>


    {{-- EXISTING REUSABLE FOOTER --}}
    @include('layouts.footer')

</div>


{{-- CHATBOT --}}
@include('layouts.chatbot')

</body>
</html>
