@extends('layouts.app')

@section('content')

    {{-- ============ PAGE HEADER ============ --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center
                sm:justify-between gap-4">

        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-2">
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-600 transition">
                    Dashboard
                </a>
                <span>/</span>
                <span class="text-slate-600 font-medium">Users</span>
            </div>

            <h1 class="text-3xl font-bold text-slate-900 tracking-tight">
                Users
            </h1>

            <p class="text-slate-500 mt-1.5 text-sm">
                Manage system users, roles, and permissions.
            </p>
        </div>

        <div class="flex items-center gap-2">

            <button class="px-4 py-2 rounded-xl bg-white border border-slate-200
                           text-sm font-medium text-slate-700
                           hover:bg-slate-50 hover:border-slate-300
                           transition shadow-sm inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                     stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                </svg>
                Export
            </button>

            @can('create users')
                <a href="{{ route('users.create') }}"
                   class="px-4 py-2 rounded-xl
                          bg-gradient-to-r from-indigo-500 to-violet-600
                          text-sm font-medium text-white
                          hover:shadow-lg hover:shadow-indigo-500/30
                          active:scale-[0.98]
                          transition shadow-md inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                         stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 4v16m8-8H4"/>
                    </svg>
                    Add User
                </a>
            @endcan

        </div>

    </div>


    {{-- ============ FLASH MESSAGES ============ --}}
    @if(session('success'))
        <div class="mb-6 flex items-start gap-3 rounded-xl
                    border border-emerald-200 bg-emerald-50
                    px-4 py-4 text-emerald-800">
            <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none"
                 stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <p class="text-sm font-medium">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 flex items-start gap-3 rounded-xl
                    border border-rose-200 bg-rose-50
                    px-4 py-4 text-rose-800">
            <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none"
                 stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 8v4m0 4h.01M10.29 3.86l-7.82 14A2 2 0 004.21 21h15.58a2 2 0 001.74-3.14l-7.82-14a2 2 0 00-3.42 0z"/>
            </svg>
            <p class="text-sm font-medium">{{ session('error') }}</p>
        </div>
    @endif


    {{-- ============ STATS ROW ============ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">

        {{-- Total --}}
        <div class="group bg-white rounded-2xl border border-slate-200/70 p-5
                    hover:shadow-lg hover:shadow-slate-200/60
                    hover:-translate-y-0.5 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-500">
                        Total Users
                    </p>
                    <h2 class="text-3xl font-bold text-slate-900 mt-2">
                        {{ $stats['total'] ?? 0 }}
                    </h2>
                </div>
                <div class="w-11 h-11 rounded-xl bg-indigo-50
                            flex items-center justify-center text-lg
                            group-hover:scale-110 transition">
                    👥
                </div>
            </div>
        </div>

        {{-- Super Admins --}}
        <div class="group bg-white rounded-2xl border border-slate-200/70 p-5
                    hover:shadow-lg hover:shadow-slate-200/60
                    hover:-translate-y-0.5 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-500">
                        Super Admins
                    </p>
                    <h2 class="text-3xl font-bold text-rose-600 mt-2">
                        {{ $stats['superadmin'] ?? 0 }}
                    </h2>
                </div>
                <div class="w-11 h-11 rounded-xl bg-rose-50
                            flex items-center justify-center text-lg
                            group-hover:scale-110 transition">
                    👑
                </div>
            </div>
        </div>

        {{-- Admins --}}
        <div class="group bg-white rounded-2xl border border-slate-200/70 p-5
                    hover:shadow-lg hover:shadow-slate-200/60
                    hover:-translate-y-0.5 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-500">
                        Admins
                    </p>
                    <h2 class="text-3xl font-bold text-violet-600 mt-2">
                        {{ $stats['admins'] ?? 0 }}
                    </h2>
                </div>
                <div class="w-11 h-11 rounded-xl bg-violet-50
                            flex items-center justify-center text-lg
                            group-hover:scale-110 transition">
                    🛡️
                </div>
            </div>
        </div>

        {{-- Users --}}
        <div class="group bg-white rounded-2xl border border-slate-200/70 p-5
                    hover:shadow-lg hover:shadow-slate-200/60
                    hover:-translate-y-0.5 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-500">
                        Users
                    </p>
                    <h2 class="text-3xl font-bold text-emerald-600 mt-2">
                        {{ $stats['users'] ?? 0 }}
                    </h2>
                </div>
                <div class="w-11 h-11 rounded-xl bg-emerald-50
                            flex items-center justify-center text-lg
                            group-hover:scale-110 transition">
                    🧑
                </div>
            </div>
        </div>

    </div>


    {{-- ============ USERS TABLE ============ --}}
    <div class="bg-white rounded-2xl border border-slate-200/70
                shadow-sm overflow-hidden">

        {{-- Toolbar --}}
        <div class="p-5 border-b border-slate-100
                    flex flex-col lg:flex-row lg:items-center
                    gap-3 lg:justify-between">

            <div class="relative max-w-sm w-full">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center
                            pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400" fill="none"
                         stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/>
                    </svg>
                </div>
                <input type="text"
                       placeholder="Search users..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl
                              bg-slate-50 border border-slate-200
                              text-sm text-slate-700 placeholder-slate-400
                              focus:bg-white focus:border-indigo-400
                              focus:ring-2 focus:ring-indigo-500/20
                              outline-none transition">
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <select class="px-3.5 py-2.5 rounded-xl
                               bg-slate-50 border border-slate-200
                               text-sm text-slate-700
                               focus:bg-white focus:border-indigo-400
                               focus:ring-2 focus:ring-indigo-500/20
                               outline-none transition cursor-pointer">
                    <option>All Roles</option>
                    @foreach($roles ?? [] as $role)
                        <option>{{ ucfirst($role->name) }}</option>
                    @endforeach
                </select>
            </div>

        </div>


        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50/70 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold
                                   uppercase tracking-wider text-slate-500">
                            User
                        </th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold
                                   uppercase tracking-wider text-slate-500">
                            Email
                        </th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold
                                   uppercase tracking-wider text-slate-500">
                            Role
                        </th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold
                                   uppercase tracking-wider text-slate-500">
                            Joined
                        </th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold
                                   uppercase tracking-wider text-slate-500">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">

                    @forelse($users ?? [] as $user)

                        <tr class="hover:bg-slate-50/60 transition-colors">

                            {{-- User --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full
                                                bg-gradient-to-br from-indigo-500 to-violet-600
                                                flex items-center justify-center
                                                text-white font-semibold text-sm
                                                flex-shrink-0
                                                ring-2 ring-white shadow-sm">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-800">
                                            {{ $user->name }}
                                            @if(auth()->id() === $user->id)
                                                <span class="ml-1 text-xs text-indigo-500 font-normal">(you)</span>
                                            @endif
                                        </p>
                                        <p class="text-xs text-slate-400">
                                            ID: #{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            {{-- Email --}}
                            <td class="px-6 py-4 text-slate-600">
                                {{ $user->email }}
                            </td>

                            {{-- Role --}}
                            <td class="px-6 py-4">
                                @php
                                    $role = $user->getRoleNames()->first() ?? 'user';
                                    $roleConfig = [
                                        'superadmin' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        'admin'      => 'bg-violet-50 text-violet-700 border-violet-200',
                                        'user'       => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    ];
                                    $roleClasses = $roleConfig[$role] ?? 'bg-slate-100 text-slate-600 border-slate-200';
                                @endphp
                                <span class="inline-flex items-center gap-1.5
                                             px-2.5 py-1 rounded-lg
                                             border text-xs font-medium capitalize
                                             {{ $roleClasses }}">
                                    {{ $role }}
                                </span>
                            </td>

                            {{-- Joined --}}
                            <td class="px-6 py-4 text-slate-600">
                                <p class="text-sm">
                                    {{ $user->created_at->format('M d, Y') }}
                                </p>
                                <p class="text-xs text-slate-400 mt-0.5">
                                    {{ $user->created_at->diffForHumans() }}
                                </p>
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-1">

                                    {{-- View --}}
                                    <a href="{{ route('users.show', $user) }}"
                                       class="p-2 rounded-lg
                                              text-slate-400 hover:text-indigo-600
                                              hover:bg-indigo-50 transition"
                                       title="View">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                             stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>

                                    {{-- Edit --}}
                                    @can('edit users')
                                        <a href="{{ route('users.edit', $user) }}"
                                           class="p-2 rounded-lg
                                                  text-slate-400 hover:text-amber-600
                                                  hover:bg-amber-50 transition"
                                           title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                 stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                    @endcan

                                    {{-- Delete --}}
                                    @can('delete users')
                                        @if(auth()->id() !== $user->id)
                                            <form action="{{ route('users.destroy', $user) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Delete this user? This cannot be undone.');"
                                                  class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="p-2 rounded-lg
                                                               text-slate-400 hover:text-rose-600
                                                               hover:bg-rose-50 transition"
                                                        title="Delete">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                         stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    @endcan

                                </div>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="5" class="px-6 py-20 text-center">
                                <div class="inline-flex items-center justify-center
                                            w-16 h-16 rounded-2xl bg-slate-100
                                            text-3xl mb-4">
                                    👥
                                </div>
                                <h3 class="text-lg font-semibold text-slate-800">
                                    No users found
                                </h3>
                                <p class="text-sm text-slate-500 mt-1 max-w-sm mx-auto">
                                    Get started by adding your first user.
                                </p>
                            </td>
                        </tr>

                    @endforelse

                </tbody>
            </table>
        </div>


        {{-- Pagination --}}
        @if(isset($users) && method_exists($users, 'links') && $users->count())
            <div class="px-6 py-4 border-t border-slate-100
                        flex flex-col sm:flex-row
                        items-center justify-between gap-3">
                <p class="text-xs text-slate-500">
                    Showing
                    <span class="font-medium text-slate-700">{{ $users->firstItem() }}</span>
                    to
                    <span class="font-medium text-slate-700">{{ $users->lastItem() }}</span>
                    of
                    <span class="font-medium text-slate-700">{{ $users->total() }}</span>
                    users
                </p>
                <div>{{ $users->links() }}</div>
            </div>
        @endif

    </div>

@endsection
