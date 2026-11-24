
@extends('layouts.public')

@section('title', ($hotel->name ?? 'Welcome') . ' — Experience Great Food')

@section('content')

@php
    $foodImages = [
        'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=900&q=85',
        'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=900&q=85',
        'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=900&q=85',
        'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?w=900&q=85',
        'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=900&q=85',
        'https://images.unsplash.com/photo-1600891964599-f61ba0e24092?w=900&q=85',
    ];

    $heroImage = 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=2000&q=90';
@endphp

{{-- NAVIGATION --}}
<header class="sticky top-0 z-50 bg-white/95 backdrop-blur-xl border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-5 lg:px-8">
        <div class="h-20 flex items-center justify-between">

            <a href="{{ route('landing') }}" class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-orange-500 flex items-center justify-center text-white text-xl shadow-lg shadow-orange-200">
                    ✦
                </div>

                <div>
                    <h1 class="text-xl font-extrabold tracking-tight text-gray-900">
                        {{ $hotel->name ?? config('app.name') }}
                    </h1>
                    <p class="text-[10px] uppercase tracking-[3px] text-gray-400 font-semibold">
                        Food • Flavor • Experience
                    </p>
                </div>
            </a>

            <nav class="hidden md:flex items-center gap-9 text-sm font-semibold text-gray-600">
                <a href="#home" class="hover:text-orange-500 transition">Home</a>
                <a href="#menu" class="hover:text-orange-500 transition">Our Menu</a>
                <a href="#about" class="hover:text-orange-500 transition">About Us</a>
                <a href="#contact" class="hover:text-orange-500 transition">Contact</a>
            </nav>

            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}"
                       class="hidden sm:inline-flex px-5 py-3 rounded-full bg-gray-900 text-white text-sm font-bold hover:bg-orange-500 transition">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="hidden sm:inline-flex text-sm font-semibold text-gray-600 hover:text-orange-500 transition">
                        Login
                    </a>
                @endauth

                <a href="{{ auth()->check() ? route('reservations.create') : route('guest.book.create') }}"
                   class="inline-flex items-center gap-2 px-5 sm:px-6 py-3 rounded-full bg-orange-500 text-white text-sm font-bold shadow-lg shadow-orange-200 hover:bg-orange-600 hover:-translate-y-0.5 transition">
                    Reserve Now
                    <span>→</span>
                </a>
            </div>
        </div>
    </div>
</header>


{{-- HERO SECTION --}}
<section id="home" class="relative min-h-[650px] lg:min-h-[740px] flex items-center overflow-hidden bg-gray-950">

    <div class="absolute inset-0">
        <img src="{{ $heroImage }}"
             alt="Delicious restaurant food"
             class="w-full h-full object-cover">

        <div class="absolute inset-0 bg-gradient-to-r from-black/85 via-black/65 to-black/20"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-black/20"></div>
    </div>

    <div class="relative max-w-7xl mx-auto w-full px-6 lg:px-8 py-24">
        <div class="max-w-3xl">

            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 border border-white/20 backdrop-blur text-white text-xs font-bold tracking-widest uppercase mb-8">
                <span class="w-2 h-2 rounded-full bg-green-400"></span>
                {{ optional($hotel)->status === 'active' ? 'Welcoming Guests' : 'Welcome to Our Restaurant' }}
            </div>

            <h1 class="text-5xl sm:text-6xl lg:text-7xl font-black text-white leading-[1.08] tracking-tight">
                Good Food.
                <span class="block text-orange-400">Great Moments.</span>
                <span class="block">Made for You.</span>
            </h1>

            <p class="mt-7 text-lg sm:text-xl text-gray-200 leading-relaxed max-w-xl">
                Discover unforgettable flavors, freshly prepared dishes and a dining experience worth coming back for.
                Your next favorite meal is waiting.
            </p>

            @if(optional($hotel)->address)
                <p class="mt-5 text-sm text-gray-300 flex items-center gap-2">
                    <span>📍</span>
                    {{ $hotel->address }}
                </p>
            @endif

            <div class="mt-10 flex flex-wrap gap-4">
                <a href="{{ auth()->check() ? route('reservations.create') : route('guest.book.create') }}"
                   class="inline-flex items-center gap-3 px-8 py-4 rounded-xl bg-orange-500 text-white font-extrabold shadow-xl shadow-orange-900/30 hover:bg-orange-600 hover:-translate-y-1 transition">
                    🍽️ Reserve Your Table
                    <span>→</span>
                </a>

                <a href="#menu"
                   class="inline-flex items-center gap-3 px-8 py-4 rounded-xl border border-white/40 bg-white/10 backdrop-blur text-white font-bold hover:bg-white hover:text-gray-900 transition">
                    Explore Our Menu
                </a>
            </div>

            <div class="mt-14 flex flex-wrap gap-8 text-white">
                <div>
                    <p class="text-2xl font-black text-orange-400">Fresh</p>
                    <p class="text-xs text-gray-300 mt-1">Quality Ingredients</p>
                </div>
                <div class="w-px bg-white/20"></div>
                <div>
                    <p class="text-2xl font-black text-orange-400">Delicious</p>
                    <p class="text-xs text-gray-300 mt-1">Made with Passion</p>
                </div>
                <div class="w-px bg-white/20"></div>
                <div>
                    <p class="text-2xl font-black text-orange-400">Memorable</p>
                    <p class="text-xs text-gray-300 mt-1">Every Visit</p>
                </div>
            </div>
        </div>
    </div>

    <div class="hidden lg:block absolute right-12 bottom-12 bg-white/95 backdrop-blur rounded-2xl p-5 shadow-2xl max-w-xs">
        <p class="text-orange-500 text-xs font-extrabold uppercase tracking-widest">A little happiness</p>
        <p class="text-gray-900 text-lg font-black mt-1">Served fresh, every day.</p>
        <p class="text-gray-500 text-sm mt-1">Come hungry. Leave happy.</p>
    </div>
</section>


{{-- INTRO STRIP --}}
<section class="bg-orange-500 text-white">
    <div class="max-w-7xl mx-auto px-6 lg:px-8 py-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <p class="font-extrabold text-lg">Your table is waiting!</p>
            <p class="text-sm text-orange-100">Make your next meal something special.</p>
        </div>

        <a href="{{ auth()->check() ? route('reservations.create') : route('guest.book.create') }}"
           class="px-6 py-3 rounded-xl bg-white text-orange-600 font-extrabold text-sm hover:bg-gray-100 transition">
            Book a Table Now →
        </a>
    </div>
</section>


{{-- MENU SECTION --}}
<section id="menu" class="bg-[#fffaf5] py-20 lg:py-28">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">

        <div class="text-center max-w-2xl mx-auto mb-14">
            <span class="text-orange-500 font-extrabold text-xs uppercase tracking-[4px]">
                Taste the Difference
            </span>

            <h2 class="text-4xl md:text-5xl font-black text-gray-900 mt-4">
                Made to Make You
                <span class="text-orange-500">Hungry.</span>
            </h2>

            <p class="text-gray-500 mt-5 leading-relaxed">
                From comforting classics to unforgettable specialties,
                explore the dishes prepared to make every bite count.
            </p>
        </div>

        @forelse($menu as $category => $items)
            <div class="mb-16">

                <div class="flex items-center justify-between mb-7">
                    <div>
                        <span class="text-orange-500 text-xs font-bold uppercase tracking-widest">
                            Explore our selection
                        </span>
                        <h3 class="text-2xl md:text-3xl font-black text-gray-900 mt-1">
                            {{ $category ?? 'Our Specials' }}
                        </h3>
                    </div>

                    <div class="hidden sm:block h-px bg-orange-100 flex-1 mx-8"></div>

                    <span class="text-sm text-gray-400 font-medium">
                        {{ count($items) }} {{ count($items) === 1 ? 'Dish' : 'Dishes' }}
                    </span>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-7">
                    @foreach($items as $item)
                        <article class="group bg-white rounded-2xl overflow-hidden border border-orange-100/70 shadow-sm hover:shadow-2xl hover:shadow-orange-100 hover:-translate-y-2 transition duration-300">

                            <div class="relative h-56 overflow-hidden bg-orange-50">
                                <img src="{{ $foodImages[$loop->index % count($foodImages)] }}"
                                     alt="{{ $item->name }}"
                                     loading="lazy"
                                     class="w-full h-full object-cover group-hover:scale-110 transition duration-700">

                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>

                                @if($item->is_vegetarian)
                                    <span class="absolute top-4 left-4 px-3 py-1.5 rounded-full bg-white text-emerald-700 text-xs font-bold shadow">
                                        🌱 Vegetarian
                                    </span>
                                @endif

                                <span class="absolute bottom-4 right-4 bg-white text-orange-600 font-black text-lg px-4 py-2 rounded-xl shadow-lg">
                                    Rs. {{ number_format($item->price, 0) }}
                                </span>
                            </div>

                            <div class="p-6">
                                <div class="flex items-start justify-between gap-3">
                                    <h4 class="text-xl font-extrabold text-gray-900 group-hover:text-orange-600 transition">
                                        {{ $item->name }}
                                    </h4>
                                    <span class="text-orange-400 text-xl">✦</span>
                                </div>

                                <p class="text-sm text-gray-500 leading-relaxed mt-3 min-h-[40px]">
                                    {{ $item->description ?: 'Freshly prepared with care using quality ingredients. A delicious choice for your next visit.' }}
                                </p>

                                <div class="flex items-center justify-between mt-6 pt-5 border-t border-gray-100">
                                    <span class="text-xs font-semibold text-gray-400">
                                        ⏱ {{ $item->preparation_minutes }} min preparation
                                    </span>

                                    <a href="{{ auth()->check() ? route('reservations.create') : route('guest.book.create') }}"
                                       class="text-sm font-extrabold text-orange-600 hover:text-orange-700">
                                        Order / Reserve →
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="text-center py-16 bg-white rounded-3xl border border-orange-100">
                <div class="text-5xl mb-4">🍽️</div>
                <h3 class="text-xl font-bold text-gray-900">Our menu is getting ready</h3>
                <p class="text-gray-500 mt-2">Please check back soon for something delicious.</p>
            </div>
        @endforelse

        <div class="text-center mt-10">
            <a href="{{ auth()->check() ? route('reservations.create') : route('guest.book.create') }}"
               class="inline-flex items-center gap-3 px-9 py-4 bg-gray-900 text-white font-extrabold rounded-xl hover:bg-orange-500 transition shadow-lg">
                Find Your Favorite Dish
                <span>→</span>
            </a>
        </div>
    </div>
</section>


{{-- EXPERIENCE SECTION --}}
<section id="about" class="bg-white py-20 lg:py-28">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-14 items-center">

            <div class="relative">
                <div class="rounded-3xl overflow-hidden shadow-2xl">
                    <img src="https://images.unsplash.com/photo-1559339352-11d035aa65de?w=1200&q=85"
                         alt="Restaurant dining experience"
                         loading="lazy"
                         class="w-full h-[430px] object-cover">
                </div>

                <div class="absolute -bottom-7 -right-3 sm:right-6 bg-orange-500 text-white rounded-2xl p-6 shadow-xl max-w-[220px]">
                    <p class="text-3xl font-black">❤️</p>
                    <p class="font-extrabold text-lg mt-2">Made with passion</p>
                    <p class="text-xs text-orange-100 mt-1">Every dish. Every detail. Every visit.</p>
                </div>
            </div>

            <div class="lg:pl-8 mt-6 lg:mt-0">
                <span class="text-orange-500 text-xs font-extrabold uppercase tracking-[4px]">
                    More Than Just a Meal
                </span>

                <h2 class="text-4xl md:text-5xl font-black text-gray-900 mt-4 leading-tight">
                    A Place Where
                    <span class="text-orange-500">Food Brings</span>
                    People Together.
                </h2>

                <p class="text-gray-500 leading-relaxed mt-6">
                    Great food is about more than taste. It is about sharing moments,
                    enjoying good company and creating memories around the table.
                    We want every visit to feel special.
                </p>

                <div class="grid grid-cols-2 gap-5 mt-9">
                    <div class="p-5 rounded-2xl bg-orange-50">
                        <span class="text-2xl">🥘</span>
                        <h4 class="font-extrabold text-gray-900 mt-3">Delicious Food</h4>
                        <p class="text-xs text-gray-500 mt-1">Flavor in every bite.</p>
                    </div>

                    <div class="p-5 rounded-2xl bg-orange-50">
                        <span class="text-2xl">✨</span>
                        <h4 class="font-extrabold text-gray-900 mt-3">Warm Welcome</h4>
                        <p class="text-xs text-gray-500 mt-1">A comfortable experience.</p>
                    </div>
                </div>

                <a href="{{ auth()->check() ? route('reservations.create') : route('guest.book.create') }}"
                   class="inline-flex items-center gap-3 mt-9 px-7 py-4 bg-orange-500 text-white rounded-xl font-extrabold hover:bg-orange-600 transition">
                    Come Dine With Us →
                </a>
            </div>
        </div>
    </div>
</section>


{{-- CONTACT / FINAL CTA --}}
<section id="contact" class="relative overflow-hidden bg-gray-950 py-20 lg:py-24">
    <div class="absolute inset-0 opacity-20">
        <img src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=1800&q=85"
             alt=""
             loading="lazy"
             class="w-full h-full object-cover">
    </div>

    <div class="absolute inset-0 bg-gray-950/80"></div>

    <div class="relative max-w-4xl mx-auto px-6 text-center">
        <span class="text-orange-400 text-xs font-extrabold uppercase tracking-[4px]">
            Your Next Great Meal Starts Here
        </span>

        <h2 class="text-4xl md:text-6xl font-black text-white mt-5 leading-tight">
            Craving Something
            <span class="text-orange-400">Delicious?</span>
        </h2>

        <p class="text-gray-300 text-lg mt-6 max-w-2xl mx-auto">
            Don't just think about it. Come experience the flavors,
            enjoy the atmosphere and make your next meal memorable.
        </p>

        <a href="{{ auth()->check() ? route('reservations.create') : route('guest.book.create') }}"
           class="inline-flex items-center gap-3 mt-10 px-10 py-5 bg-orange-500 text-white rounded-xl font-black text-lg shadow-2xl shadow-orange-900/30 hover:bg-orange-600 hover:-translate-y-1 transition">
            🍽️ Reserve Your Table Now
            <span>→</span>
        </a>

        @if(optional($hotel)->phone || optional($hotel)->email)
            <div class="mt-12 flex flex-wrap justify-center gap-8 text-sm text-gray-300">
                @if($hotel->phone)
                    <span>📞 {{ $hotel->phone }}</span>
                @endif

                @if($hotel->email)
                    <span>✉️ {{ $hotel->email }}</span>
                @endif
            </div>
        @endif
    </div>
</section>


{{-- FOOTER --}}
<footer class="bg-black text-gray-400">
    <div class="max-w-7xl mx-auto px-6 lg:px-8 py-8">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <h3 class="text-white font-extrabold text-lg">
                    {{ $hotel->name ?? config('app.name') }}
                </h3>
                <p class="text-xs mt-1">Good food. Great moments.</p>
            </div>

            <p class="text-xs text-center">
                © {{ date('Y') }} {{ $hotel->name ?? config('app.name') }}. All rights reserved.
            </p>

            <a href="#home" class="text-sm font-bold text-orange-400 hover:text-orange-300 transition">
                Back to top ↑
            </a>
        </div>
    </div>
</footer>

@endsection
