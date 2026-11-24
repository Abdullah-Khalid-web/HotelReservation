
<header class="h-16 bg-white/95 backdrop-blur-xl border-b border-gray-200/80
               flex items-center justify-between px-4 sm:px-6
               sticky top-0 z-50 shadow-sm">

    {{-- Left Section --}}
    <div class="flex items-center gap-3 min-w-0">

        {{-- Sidebar Toggle --}}
        <button
            @click="toggleSidebar()"
            type="button"
            class="p-2 -ml-2 text-gray-500 hover:text-indigo-600
                   rounded-xl hover:bg-indigo-50 transition-all duration-200"
            :title="window.innerWidth < 1024 ? 'Open menu' : 'Toggle sidebar'"
            aria-label="Toggle sidebar">

            {{-- Mobile Hamburger --}}
            <svg x-show="window.innerWidth < 1024"
                 class="w-6 h-6"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>

            {{-- Desktop Collapse --}}
            <svg x-show="window.innerWidth >= 1024"
                 class="w-6 h-6"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path x-show="!sidebarCollapsed"
                      stroke-linecap="round" stroke-linejoin="round"
                      stroke-width="2"
                      d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>

                <path x-show="sidebarCollapsed"
                      stroke-linecap="round" stroke-linejoin="round"
                      stroke-width="2"
                      d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
            </svg>
        </button>

        {{-- Brand --}}
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-10 h-10 flex-shrink-0
                        bg-gradient-to-br from-indigo-600 to-violet-600
                        text-white rounded-xl flex items-center justify-center
                        font-bold text-sm shadow-lg shadow-indigo-200/60">
                HR
            </div>

            <div class="min-w-0">
                <h1 class="font-bold text-base sm:text-lg tracking-tight
                           text-gray-800 truncate">
                    Hotel Reservation
                </h1>
                <p class="hidden sm:block text-[10px] text-gray-400
                          font-semibold uppercase tracking-[0.16em]">
                    Management System
                </p>
            </div>
        </div>
    </div>

    {{-- Right Section --}}
    <div class="flex items-center gap-2 sm:gap-4">

        {{-- Notifications --}}
        <button type="button"
                class="relative p-2.5 text-gray-500
                       hover:text-indigo-600 rounded-xl
                       hover:bg-indigo-50 transition-all duration-200"
                aria-label="Notifications">

            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                 stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M15 17h5l-1.405-1.405A2.032 2.032 0
                      0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2
                      0 10-4 0v.341C7.67 6.165 6 8.388
                      6 11v3.159c0 .538-.214 1.055-.595
                      1.436L4 17h5m6 0v1a3 3 0
                      11-6 0v-1m6 0H9"/>
            </svg>

            <span class="absolute top-2 right-2 w-2 h-2
                         bg-rose-500 rounded-full ring-2 ring-white"></span>
        </button>

        {{-- Authenticated User Dropdown --}}
        @auth
            <div x-data="{ userMenuOpen: false }"
                 @click.outside="userMenuOpen = false"
                 @keydown.escape.window="userMenuOpen = false"
                 class="relative">

                {{-- Trigger --}}
                <button
                    @click="userMenuOpen = !userMenuOpen"
                    type="button"
                    class="flex items-center gap-2 sm:gap-3
                           pl-1 pr-2 py-1 rounded-xl
                           hover:bg-gray-100 transition-all duration-200
                           focus:outline-none focus:ring-2
                           focus:ring-indigo-200">

                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-semibold text-gray-800 leading-tight">
                            {{ auth()->user()->name }}
                        </p>
                        <p class="text-xs text-gray-500 capitalize">
                            {{ auth()->user()->getRoleNames()->first() ?? 'User' }}
                        </p>
                    </div>

                    <div class="w-9 h-9 bg-gradient-to-br
                                from-indigo-100 to-violet-200
                                rounded-full flex items-center justify-center
                                text-indigo-700 font-bold
                                ring-2 ring-white flex-shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>

                    <svg class="w-4 h-4 text-gray-400 hidden sm:block
                                transition-transform duration-200"
                         :class="userMenuOpen && 'rotate-180'"
                         fill="none" stroke="currentColor"
                         stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                {{-- Dropdown --}}
                <div
                    x-show="userMenuOpen"
                    x-cloak
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                    class="absolute right-0 mt-3 w-64 bg-white rounded-2xl
                           border border-gray-200 shadow-2xl shadow-gray-900/10
                           overflow-hidden z-50">

                    {{-- User Information --}}
                    <div class="px-4 py-4 bg-gradient-to-br
                                from-indigo-50 to-violet-50
                                border-b border-gray-100">

                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-to-br
                                        from-indigo-500 to-violet-600
                                        rounded-full flex items-center justify-center
                                        text-white font-bold text-lg shadow-md
                                        flex-shrink-0">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-bold text-gray-900 truncate">
                                    {{ auth()->user()->name }}
                                </p>

                                <p class="text-xs text-gray-500 truncate">
                                    {{ auth()->user()->email }}
                                </p>

                                <span class="inline-flex items-center mt-1
                                             px-2 py-0.5 rounded-md
                                             bg-indigo-100 text-indigo-700
                                             text-[10px] font-bold uppercase
                                             tracking-wider">
                                    {{ auth()->user()->getRoleNames()->first() ?? 'User' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Menu Items --}}
                    <nav class="py-2">

                        {{-- Profile --}}
                        <a href="{{ route('profile.edit') }}"
                           class="flex items-center gap-3 px-4 py-2.5
                                  text-sm text-gray-700 hover:bg-indigo-50
                                  hover:text-indigo-600 transition-colors">
                            <span class="w-8 h-8 rounded-lg bg-gray-100
                                         flex items-center justify-center
                                         flex-shrink-0">
                                <svg class="w-4 h-4" fill="none"
                                     stroke="currentColor" stroke-width="2"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          d="M16 7a4 4 0 11-8 0 4 4
                                          0 018 0zM12 14a7 7 0
                                          00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </span>
                            <span class="font-medium">My Profile</span>
                        </a>

                        {{-- Reservations --}}
                        @can('view reservations')
                            <a href="{{ route('reservations.index') }}"
                               class="flex items-center gap-3 px-4 py-2.5
                                      text-sm text-gray-700 hover:bg-indigo-50
                                      hover:text-indigo-600 transition-colors">
                                <span class="w-8 h-8 rounded-lg bg-gray-100
                                             flex items-center justify-center
                                             flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none"
                                         stroke="currentColor" stroke-width="2"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              d="M8 7V3m8 4V3m-9 8h10M5
                                              21h14a2 2 0 002-2V7a2 2
                                              0 00-2-2H5a2 2 0
                                              00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </span>
                                <span class="font-medium">My Reservations</span>
                            </a>
                        @endcan

                        {{-- Pre-Orders --}}
                        @can('view pre-orders')
                            <a href="{{ route('pre-orders.index') }}"
                               class="flex items-center gap-3 px-4 py-2.5
                                      text-sm text-gray-700 hover:bg-indigo-50
                                      hover:text-indigo-600 transition-colors">
                                <span class="w-8 h-8 rounded-lg bg-gray-100
                                             flex items-center justify-center
                                             flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none"
                                         stroke="currentColor" stroke-width="2"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              d="M3 3h2l.4 2M7 13h10l4-8H5.4
                                              M7 13L5.4 5M7 13l-2.293
                                              2.293c-.63.63-.184 1.707.707
                                              1.707H17m0 0a2 2 0
                                              100 4 2 2 0 000-4zm-8
                                              2a2 2 0 11-4 0 2 2
                                              0 014 0z"/>
                                    </svg>
                                </span>
                                <span class="font-medium">My Pre-Orders</span>
                            </a>
                        @endcan

                        {{-- Manage Users --}}
                        @can('view users')
                            <a href="{{ route('users.index') }}"
                               class="flex items-center gap-3 px-4 py-2.5
                                      text-sm text-gray-700 hover:bg-indigo-50
                                      hover:text-indigo-600 transition-colors">
                                <span class="w-8 h-8 rounded-lg bg-gray-100
                                             flex items-center justify-center
                                             flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none"
                                         stroke="currentColor" stroke-width="2"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              d="M12 4.354a4 4 0 110 5.292M15
                                              21H3v-1a6 6 0 0112 0v1zm0
                                              0h6v-1a6 6 0 00-9-5.197M13
                                              7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                </span>
                                <span class="font-medium">Manage Users</span>
                            </a>
                        @endcan

                    </nav>

                    {{-- Logout --}}
                    <div class="border-t border-gray-100 py-2">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <button type="submit"
                                    class="w-full flex items-center gap-3
                                           px-4 py-2.5 text-sm text-rose-600
                                           hover:bg-rose-50 transition-colors
                                           text-left">
                                <span class="w-8 h-8 rounded-lg bg-rose-100
                                             flex items-center justify-center
                                             flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none"
                                         stroke="currentColor" stroke-width="2"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              d="M17 16l4-4m0 0l-4-4m4
                                              4H7m6 4v1a3 3 0
                                              01-3 3H6a3 3 0
                                              01-3-3V7a3 3 0
                                              013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                </span>
                                <span class="font-medium">Logout</span>
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        @endauth

    </div>
</header>
