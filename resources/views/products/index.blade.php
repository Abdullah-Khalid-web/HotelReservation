@extends('layouts.app')

@section('content')

<div class="max-w-7xl mx-auto">

    {{-- Header --}}
    <div class="mb-6 flex items-start justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-600">Dashboard</a>
                <span>/</span>
                <span class="text-slate-700">Products</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">Products</h1>
            <p class="text-sm text-slate-500 mt-1">
                Menu items available for reservations.
            </p>
        </div>

        @can('create products')
            <a href="{{ route('products.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl
                      bg-gradient-to-r from-indigo-500 to-violet-600
                      text-sm font-semibold text-white
                      hover:shadow-lg hover:shadow-indigo-500/30
                      active:scale-[0.98] transition shadow-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                     stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                New Product
            </a>
        @endcan
    </div>


    {{-- Flash --}}
    @if(session('success'))
        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3
                    text-sm text-emerald-800 flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
                 stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif


    {{-- Filters --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-5">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">

            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search products…"
                   class="lg:col-span-2 rounded-xl border border-slate-300 bg-white
                          px-4 py-2.5 text-sm
                          focus:border-indigo-500 focus:ring-2
                          focus:ring-indigo-200 outline-none transition">

            <select name="hotel_id"
                    class="rounded-xl border border-slate-300 bg-white
                           px-4 py-2.5 text-sm
                           focus:border-indigo-500 focus:ring-2
                           focus:ring-indigo-200 outline-none transition">
                <option value="">All hotels</option>
                @foreach($hotels as $hotel)
                    <option value="{{ $hotel->id }}"
                        @selected(request('hotel_id') == $hotel->id)>
                        {{ $hotel->name }}
                    </option>
                @endforeach
            </select>

            <select name="category"
                    class="rounded-xl border border-slate-300 bg-white
                           px-4 py-2.5 text-sm
                           focus:border-indigo-500 focus:ring-2
                           focus:ring-indigo-200 outline-none transition">
                <option value="">All categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}"
                        @selected(request('category') === $cat)>
                        {{ $cat }}
                    </option>
                @endforeach
            </select>

            <div class="flex gap-2">
                <button type="submit"
                        class="flex-1 rounded-xl bg-slate-900 text-white
                               px-4 py-2.5 text-sm font-semibold
                               hover:bg-slate-800 transition">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'hotel_id', 'category', 'availability']))
                    <a href="{{ route('products.index') }}"
                       class="rounded-xl border border-slate-300 text-slate-700
                              px-4 py-2.5 text-sm font-semibold
                              hover:bg-slate-50 transition">
                        Clear
                    </a>
                @endif
            </div>

        </form>
    </div>


    {{-- Products grid --}}
    @if($products->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-12 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl
                        bg-slate-100 text-3xl mb-3">
                🍽️
            </div>
            <p class="text-sm font-medium text-slate-700">No products found</p>
            <p class="text-xs text-slate-500 mt-1">
                @can('create products')
                    Add your first product to get started.
                @else
                    No products available yet.
                @endcan
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

            @foreach($products as $product)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm
                            hover:shadow-md transition overflow-hidden">

                    {{-- Image placeholder / actual --}}
                    <div class="h-32 bg-gradient-to-br from-indigo-100 to-violet-100
                                flex items-center justify-center text-4xl">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}"
                                 alt="{{ $product->name }}"
                                 class="w-full h-full object-cover">
                        @else
                            {{ $product->is_vegetarian ? '🥗' : '🍖' }}
                        @endif
                    </div>

                    <div class="p-4">

                        {{-- Category + availability badges --}}
                        <div class="flex items-center gap-2 mb-2 flex-wrap">
                            @if($product->category)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md
                                             bg-slate-100 text-slate-700 text-xs font-medium">
                                    {{ $product->category }}
                                </span>
                            @endif

                            @if($product->is_vegetarian)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md
                                             bg-emerald-100 text-emerald-700 text-xs font-medium">
                                    🌱 Veg
                                </span>
                            @endif

                            @if(!$product->is_available)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md
                                             bg-rose-100 text-rose-700 text-xs font-medium">
                                    Unavailable
                                </span>
                            @elseif($product->stock_quantity !== null && $product->stock_quantity <= 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md
                                             bg-amber-100 text-amber-700 text-xs font-medium">
                                    Out of stock
                                </span>
                            @endif
                        </div>

                        {{-- Name --}}
                        <h3 class="text-base font-semibold text-slate-900 truncate">
                            {{ $product->name }}
                        </h3>

                        {{-- Hotel --}}
                        <p class="text-xs text-slate-500 mt-0.5">
                            🏨 {{ $product->hotel?->name ?? '—' }}
                        </p>

                        {{-- Description --}}
                        @if($product->description)
                            <p class="text-xs text-slate-600 mt-2 line-clamp-2">
                                {{ $product->description }}
                            </p>
                        @endif

                        {{-- Price + stock --}}
                        <div class="mt-3 flex items-center justify-between">
                            <div>
                                <p class="text-lg font-bold text-slate-900">
                                    Rs {{ number_format($product->price, 0) }}
                                </p>
                                <p class="text-xs text-slate-500">
                                    @if($product->stock_quantity === null)
                                        Unlimited stock
                                    @else
                                        {{ $product->stock_quantity }} in stock
                                    @endif
                                </p>
                            </div>

                            <div class="flex items-center gap-1">
                                <a href="{{ route('products.show', $product) }}"
                                   class="p-2 rounded-lg text-slate-500
                                          hover:bg-slate-100 hover:text-slate-900 transition"
                                   title="View">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                         stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>

                                @can('edit products')
                                    <a href="{{ route('products.edit', $product) }}"
                                       class="p-2 rounded-lg text-indigo-500
                                              hover:bg-indigo-50 hover:text-indigo-700 transition"
                                       title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                             stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                @endcan

                                @can('delete products')
                                    <form action="{{ route('products.destroy', $product) }}"
                                          method="POST"
                                          onsubmit="return confirm('Delete this product?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-2 rounded-lg text-rose-500
                                                       hover:bg-rose-50 hover:text-rose-700 transition"
                                                title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                 stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach

        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $products->links() }}
        </div>
    @endif

</div>

@endsection
