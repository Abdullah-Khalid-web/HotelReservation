@extends('layouts.app')

@section('content')

<div class="max-w-3xl mx-auto">

    {{-- HEADER --}}
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('users.index') }}"
           class="flex items-center justify-center w-11 h-11 rounded-xl
                  bg-white border border-slate-200
                  text-slate-600 hover:text-indigo-600
                  hover:border-indigo-300 hover:bg-indigo-50
                  shadow-sm transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">User Details</h1>
            <p class="text-sm text-slate-500 mt-1">View user profile and permissions.</p>
        </div>
    </div>

    {{-- Profile Card --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        <div class="relative h-24 bg-gradient-to-r from-indigo-500 via-indigo-600 to-violet-600">
            <div class="absolute -bottom-10 left-6">
                <div class="w-20 h-20 rounded-2xl
                            bg-gradient-to-br from-indigo-500 to-violet-600
                            flex items-center justify-center
                            text-white font-bold text-2xl
                            ring-4 ring-white shadow-lg">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            </div>
        </div>

        <div class="pt-14 px-6 pb-6">

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
                <div>
                    <h2 class="text-xl font-bold text-slate-900">{{ $user->name }}</h2>
                    <p class="text-sm text-slate-500">{{ $user->email }}</p>
                </div>
                <div class="flex gap-2">
                    @can('edit users')
                        <a href="{{ route('users.edit', $user) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl
                                  bg-gradient-to-r from-indigo-500 to-violet-600
                                  text-sm font-medium text-white
                                  hover:shadow-lg hover:shadow-indigo-500/30
                                  transition shadow-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                 stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit
                        </a>
                    @endcan
                </div>
            </div>

            {{-- Info grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div class="rounded-xl bg-slate-50 border border-slate-100 p-4">
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-500 mb-1">
                        Role
                    </p>
                    @php
                        $role = $user->getRoleNames()->first() ?? 'guest';
                        $roleConfig = [
                            'admin' => 'bg-violet-50 text-violet-700 border-violet-200',
                            'staff' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'guest' => 'bg-amber-50 text-amber-700 border-amber-200',
                        ];
                        $roleClasses = $roleConfig[$role] ?? 'bg-slate-100 text-slate-600 border-slate-200';
                    @endphp
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg
                                 border text-xs font-medium capitalize {{ $roleClasses }}">
                        {{ $role }}
                    </span>
                </div>

                <div class="rounded-xl bg-slate-50 border border-slate-100 p-4">
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-500 mb-1">
                        User ID
                    </p>
                    <p class="text-sm font-semibold text-slate-800">
                        #{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}
                    </p>
                </div>

                <div class="rounded-xl bg-slate-50 border border-slate-100 p-4">
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-500 mb-1">
                        Joined
                    </p>
                    <p class="text-sm font-semibold text-slate-800">
                        {{ $user->created_at->format('M d, Y') }}
                    </p>
                    <p class="text-xs text-slate-400 mt-0.5">
                        {{ $user->created_at->diffForHumans() }}
                    </p>
                </div>

                <div class="rounded-xl bg-slate-50 border border-slate-100 p-4">
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-500 mb-1">
                        Last Updated
                    </p>
                    <p class="text-sm font-semibold text-slate-800">
                        {{ $user->updated_at->format('M d, Y') }}
                    </p>
                    <p class="text-xs text-slate-400 mt-0.5">
                        {{ $user->updated_at->diffForHumans() }}
                    </p>
                </div>

            </div>


            {{-- Permissions --}}
            @if($user->permissions->count())
                <div class="mt-6">
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-500 mb-3">
                        Direct Permissions
                    </p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($user->permissions as $permission)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg
                                         bg-indigo-50 text-indigo-700 border border-indigo-100
                                         text-xs font-medium">
                                {{ $permission->name }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

    </div>

</div>

@endsection
