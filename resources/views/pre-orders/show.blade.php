@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('pre-orders.index') }}"
           class="flex items-center justify-center w-11 h-11 rounded-xl
                  bg-white border border-slate-200 text-slate-600
                  hover:text-indigo-600 hover:border-indigo-300 hover:bg-indigo-50
                  shadow-sm transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-900">
                Pre-Order #{{ $preOrder->id }}
            </h1>
            <p class="text-sm text-slate-500">
                Status:
                <span class="font-semibold text-{{ $preOrder->statusColor() }}-600">
                    {{ ucfirst($preOrder->status) }}
                </span>
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3
                    text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        <div>
            <h2 class="text-sm font-semibold text-slate-700 mb-2">Items</h2>
            <div class="space-y-2">
                @foreach($preOrder->items as $item)
                    <div class="flex items-center justify-between py-2
                                border-b border-slate-100 last:border-0">
                        <div>
                            <p class="text-sm font-medium text-slate-900">{{ $item->product_name }}</p>
                            <p class="text-xs text-slate-500">
                                {{ $item->quantity }} × Rs {{ number_format($item->unit_price, 0) }}
                                @if($item->notes)
                                    · {{ $item->notes }}
                                @endif
                            </p>
                        </div>
                        <p class="text-sm font-semibold text-slate-900">
                            Rs {{ number_format($item->line_total, 0) }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 space-y-1">
            <div class="flex justify-between text-sm">
                <span class="text-slate-600">Subtotal</span>
                <span class="font-medium">Rs {{ number_format($preOrder->subtotal, 0) }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-slate-600">Tax (5%)</span>
                <span class="font-medium">Rs {{ number_format($preOrder->tax, 0) }}</span>
            </div>
            <div class="flex justify-between text-base font-bold pt-2 border-t border-slate-100">
                <span>Total</span>
                <span>Rs {{ number_format($preOrder->total, 0) }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
