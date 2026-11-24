{{-- PROFESSIONAL LIGHT SIDEBAR --}}

<aside
    x-cloak
    :class="{
        'translate-x-0': sidebarOpen,
        '-translate-x-full': !sidebarOpen,
        'lg:translate-x-0': true,
        'lg:w-64': !sidebarCollapsed,
        'lg:w-[76px]': sidebarCollapsed
    }"
    class="fixed inset-y-0 left-0 z-[70] flex w-64 flex-col
           overflow-hidden border-r border-gray-200 bg-white
           text-gray-700 shadow-sm sidebar-transition">

    {{-- BRAND --}}
    <div class="flex h-[72px] flex-shrink-0 items-center justify-between
                border-b border-gray-100 px-5">

        <div x-show="!sidebarCollapsed"
             class="flex min-w-0 items-center gap-3">

            <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center
                        rounded-lg bg-indigo-600 text-sm font-bold text-white">
                HR
            </div>

            <div class="min-w-0">
                <h2 class="truncate text-sm font-bold tracking-tight text-gray-900">
                    Hotel Reservation
                </h2>
                <p class="mt-0.5 text-[10px] font-medium tracking-wide text-gray-400">
                    MANAGEMENT SYSTEM
                </p>
            </div>
        </div>

        <div x-show="sidebarCollapsed"
             class="hidden h-9 w-9 items-center justify-center
                    rounded-lg bg-indigo-600 text-sm font-bold text-white lg:flex">
            HR
        </div>

        <button
            @click="closeSidebar()"
            class="rounded-md p-2 text-gray-400 transition hover:bg-gray-100
                   hover:text-gray-700 lg:hidden"
            aria-label="Close sidebar">

            <svg class="h-5 w-5" fill="none" stroke="currentColor"
                 viewBox="0 0 24 24" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>


    {{-- NAVIGATION --}}
    <div class="sidebar-scroll flex-1 overflow-y-auto px-3 py-6">

        {{-- MAIN MENU --}}
        <div>

            <p x-show="!sidebarCollapsed"
               class="mb-3 px-3 text-[10px] font-semibold uppercase
                      tracking-wider text-gray-400">
                Main
            </p>

            <div x-show="sidebarCollapsed"
                 class="mb-4 border-t border-gray-100 mx-2"></div>

            <nav class="space-y-1">

                {{-- DASHBOARD --}}
                <a href="{{ route('dashboard') }}"
                   title="Dashboard"
                   class="group flex items-center gap-3 rounded-lg px-3 py-2.5
                          text-[13px] font-medium transition-colors
                          {{ request()->routeIs('dashboard')
                              ? 'bg-indigo-50 text-indigo-700'
                              : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">

                    <svg class="h-[18px] w-[18px] flex-shrink-0
                                {{ request()->routeIs('dashboard') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }}"
                         fill="none" stroke="currentColor"
                         viewBox="0 0 24 24" stroke-width="1.8">
                        <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                        <rect x="14" y="3" width="7" height="7" rx="1.5"/>
                        <rect x="14" y="14" width="7" height="7" rx="1.5"/>
                        <rect x="3" y="14" width="7" height="7" rx="1.5"/>
                    </svg>

                    <span x-show="!sidebarCollapsed">Dashboard</span>
                </a>


                {{-- HOTELS --}}
                @can('view hotels')
                    <a href="{{ route('hotel.index') }}"
                       title="Hotels"
                       class="group flex items-center gap-3 rounded-lg px-3 py-2.5
                              text-[13px] font-medium transition-colors
                              {{ request()->routeIs('hotel.*')
                                  ? 'bg-indigo-50 text-indigo-700'
                                  : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">

                        <svg class="h-[18px] w-[18px] flex-shrink-0
                                    {{ request()->routeIs('hotel.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }}"
                             fill="none" stroke="currentColor"
                             viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M3 21h18M5 21V5a2 2 0 012-2h10a2 2 0 012 2v16M9 7h1m4 0h1M9 11h1m4 0h1M9 15h1m4 0h1M10 21v-3h4v3"/>
                        </svg>

                        <span x-show="!sidebarCollapsed">Hotels</span>
                    </a>
                @endcan


                {{-- TABLES --}}
                @can('view tables')
                    <a href="{{ route('tables.index') }}"
                       title="Tables"
                       class="group flex items-center gap-3 rounded-lg px-3 py-2.5
                              text-[13px] font-medium transition-colors
                              {{ request()->routeIs('tables.*')
                                  ? 'bg-indigo-50 text-indigo-700'
                                  : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">

                        <svg class="h-[18px] w-[18px] flex-shrink-0
                                    {{ request()->routeIs('tables.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }}"
                             fill="none" stroke="currentColor"
                             viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M4 10h16M5 10V7a2 2 0 012-2h10a2 2 0 012 2v3M6 10v9m12-9v9M4 19h16"/>
                        </svg>

                        <span x-show="!sidebarCollapsed">Tables</span>
                    </a>
                @endcan


                {{-- RESERVATIONS --}}
                @can('view reservations')
                    <a href="{{ route('reservations.index') }}"
                       title="Reservations"
                       class="group flex items-center gap-3 rounded-lg px-3 py-2.5
                              text-[13px] font-medium transition-colors
                              {{ request()->routeIs('reservations.*')
                                  ? 'bg-indigo-50 text-indigo-700'
                                  : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">

                        <svg class="h-[18px] w-[18px] flex-shrink-0
                                    {{ request()->routeIs('reservations.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }}"
                             fill="none" stroke="currentColor"
                             viewBox="0 0 24 24" stroke-width="1.8">
                            <rect x="3" y="5" width="18" height="16" rx="2"/>
                            <path stroke-linecap="round" d="M16 3v4M8 3v4M3 10h18M8 14h2m4 0h2m-8 3h2"/>
                        </svg>

                        <span x-show="!sidebarCollapsed">Reservations</span>
                    </a>
                @endcan


                {{-- PRODUCTS --}}
                @can('view products')
                    <a href="{{ route('products.index') }}"
                       title="Products"
                       class="group flex items-center gap-3 rounded-lg px-3 py-2.5
                              text-[13px] font-medium transition-colors
                              {{ request()->routeIs('products.*')
                                  ? 'bg-indigo-50 text-indigo-700'
                                  : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">

                        <svg class="h-[18px] w-[18px] flex-shrink-0
                                    {{ request()->routeIs('products.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }}"
                             fill="none" stroke="currentColor"
                             viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M4 7h16l-1 13H5L4 7zM9 7a3 3 0 016 0"/>
                            <path stroke-linecap="round" d="M9 11v2m6-2v2"/>
                        </svg>

                        <span x-show="!sidebarCollapsed">Products</span>
                    </a>
                @endcan


                {{-- PRE-ORDERS --}}
                @can('view pre-orders')

                    @php
                        $pendingCount = \App\Models\PreOrder::query()
                            ->when(
                                !auth()->user()->hasAnyRole(['superadmin', 'admin', 'kitchen']),
                                function ($q) {
                                    $q->where('user_id', auth()->id());
                                }
                            )
                            ->whereIn('status', ['pending', 'confirmed', 'preparing'])
                            ->count();
                    @endphp

                    <a href="{{ route('pre-orders.index') }}"
                       title="Pre-Orders"
                       class="group relative flex items-center gap-3 rounded-lg px-3 py-2.5
                              text-[13px] font-medium transition-colors
                              {{ request()->routeIs('pre-orders.*')
                                  ? 'bg-indigo-50 text-indigo-700'
                                  : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">

                        <svg class="h-[18px] w-[18px] flex-shrink-0
                                    {{ request()->routeIs('pre-orders.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }}"
                             fill="none" stroke="currentColor"
                             viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M3 3h2l2.2 11.2a2 2 0 002 1.6h8.9a2 2 0 002-1.6L22 7H6"/>
                            <circle cx="10" cy="20" r="1"/>
                            <circle cx="18" cy="20" r="1"/>
                        </svg>

                        <span x-show="!sidebarCollapsed">Pre-Orders</span>

                        @if($pendingCount > 0)
                            <span x-show="!sidebarCollapsed"
                                  class="ml-auto rounded-md bg-amber-50 px-2 py-0.5
                                         text-[10px] font-semibold text-amber-700">
                                {{ $pendingCount > 99 ? '99+' : $pendingCount }}
                            </span>

                            <span x-show="sidebarCollapsed"
                                  class="absolute right-2 top-2 h-2 w-2 rounded-full bg-amber-500"
                                  title="{{ $pendingCount }} pending orders"></span>
                        @endif
                    </a>
                @endcan

            </nav>
        </div>


        {{-- ADMINISTRATION --}}
        @can('view users')
            <div class="mt-8">

                <p x-show="!sidebarCollapsed"
                   class="mb-3 px-3 text-[10px] font-semibold uppercase
                          tracking-wider text-gray-400">
                    Administration
                </p>

                <div x-show="sidebarCollapsed"
                     class="mb-4 border-t border-gray-100 mx-2"></div>

                <nav class="space-y-1">

                    <a href="{{ route('users.index') }}"
                       title="Users"
                       class="group flex items-center gap-3 rounded-lg px-3 py-2.5
                              text-[13px] font-medium transition-colors
                              {{ request()->routeIs('users.*')
                                  ? 'bg-indigo-50 text-indigo-700'
                                  : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">

                        <svg class="h-[18px] w-[18px] flex-shrink-0
                                    {{ request()->routeIs('users.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }}"
                             fill="none" stroke="currentColor"
                             viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M16 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2m6-10a4 4 0 100-8 4 4 0 000 8zm8-3a4 4 0 010 8m4 5v-2a4 4 0 00-3-3.87"/>
                        </svg>

                        <span x-show="!sidebarCollapsed">Users</span>
                    </a>

                </nav>
            </div>
        @endcan

    </div>


    {{-- FOOTER --}}
    <div class="flex-shrink-0 border-t border-gray-100 p-4">

        <div x-show="!sidebarCollapsed"
             class="flex items-center gap-3 rounded-lg bg-gray-50 px-3 py-3">

            <span class="relative flex h-2.5 w-2.5 flex-shrink-0">
                <span class="absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-50"></span>
                <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
            </span>

            <div class="min-w-0">
                <p class="text-[11px] font-semibold text-gray-700">
                    System Operational
                </p>
                <p class="mt-0.5 text-[10px] text-gray-400">
                    © {{ date('Y') }} Hotel Reservation
                </p>
            </div>
        </div>

        <div x-show="sidebarCollapsed"
             class="hidden justify-center lg:flex">
            <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"
                  title="System Operational"></span>
        </div>

    </div>

</aside>

