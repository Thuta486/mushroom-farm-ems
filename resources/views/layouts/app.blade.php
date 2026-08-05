<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') — {{ config('app.name', 'Mushroom Farm EMS') }}</title>
    <script>
        (() => {
            let storedTheme = null;

            try {
                storedTheme = localStorage.getItem('mushroom-farm-theme');
            } catch {
                // The current browser context does not allow persistent storage.
            }

            const theme = ['light', 'dark', 'system'].includes(storedTheme) ? storedTheme : 'system';
            const resolvedTheme = theme === 'system'
                ? (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')
                : theme;

            document.documentElement.dataset.theme = resolvedTheme;
            document.documentElement.dataset.themeMode = theme;
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-stone-100 font-sans text-stone-900 antialiased">
    <div id="app-layout" class="flex min-h-screen">
        <div id="sidebar-backdrop" class="hidden" aria-hidden="true"></div>

        {{-- Sidebar --}}
        <aside id="sidebar" class="relative flex shrink-0 flex-col border-r border-stone-200 bg-white">
            <div class="border-b border-stone-200 px-5 py-5">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-emerald-800">{{ __('app.login.name') }}</p>
                    </div>
                </a>
            </div>

            @auth
                <div class="border-b border-stone-200 px-4 py-3">
                    <p class="truncate text-sm font-medium text-stone-900">{{ auth()->user()->name }}</p>
                    <x-status-badge :status="auth()->user()->role->value" class="mt-1" />
                </div>
            @endauth

            <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
                @if (auth()->user()?->isSuperAdmin())
                    <x-nav-link href="{{ route('dashboard') }}" label="{{ __('app.nav.dashboard') }}" :active="request()->routeIs('dashboard')">
                        <x-slot:icon>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        </x-slot:icon>
                    </x-nav-link>

                    <x-nav-link href="{{ route('reports.index') }}" label="{{ __('app.nav.reports') }}"
                        :active="request()->routeIs('reports.*')">
                        <x-slot:icon>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </x-slot:icon>
                    </x-nav-link>
                @endif

                @if (auth()->user()?->isSuperAdmin())
                    <div class="pt-4">
                        <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-wide text-stone-400">
                            {{ __('app.nav.workforce') }}</p>
                        <x-nav-link href="{{ route('employees.index') }}" label="{{ __('app.nav.employees') }}"
                            :active="request()->routeIs('employees.*')">
                            <x-slot:icon>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </x-slot:icon>
                        </x-nav-link>

                        <x-nav-link href="{{ route('departments.index') }}" label="{{ __('app.nav.departments') }}"
                            :active="request()->routeIs('departments.*')">
                            <x-slot:icon>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </x-slot:icon>
                        </x-nav-link>

                        <x-nav-link href="{{ route('users.index') }}" label="{{ __('app.nav.users') }}"
                            :active="request()->routeIs('users.*')">
                            <x-slot:icon>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </x-slot:icon>
                        </x-nav-link>

                    </div>
                @endif
                <div class="pt-4">
                    <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-wide text-stone-400">
                        {{ __('app.nav.daily_operations') }}</p>

                    <x-nav-link href="{{ route('attendances.daily') }}" label="{{ __('app.nav.attendance') }}"
                        :active="request()->routeIs('attendances.*')">
                        <x-slot:icon>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </x-slot:icon>
                    </x-nav-link>

                    <x-nav-link href="{{ route('cash-advances.daily') }}" label="{{ __('app.nav.cash_advances') }}"
                        :active="request()->routeIs('cash-advances.*')">
                        <x-slot:icon>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </x-slot:icon>
                    </x-nav-link>
                </div>

                @if (auth()->user()?->isSuperAdmin())
                    <div class="pt-4">
                        <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-wide text-stone-400">
                            {{ __('app.nav.payrolls') }}</p>

                        <x-nav-link href="{{ route('payrolls.index') }}" label="{{ __('app.nav.payrolls') }}"
                            :active="request()->routeIs('payrolls.*')">
                            <x-slot:icon>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </x-slot:icon>
                        </x-nav-link>

                        <x-nav-link href="{{ route('advance-types.index') }}"
                            label="{{ __('app.nav.advance_types') }}" :active="request()->routeIs('advance-types.*')">
                            <x-slot:icon>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M7 7h.01M7 3h5.586a1 1 0 01.707.293l6.414 6.414a1 1 0 010 1.414l-8.586 8.586a1 1 0 01-1.414 0l-6.414-6.414A1 1 0 013 12.586V7a4 4 0 014-4z" />
                                </svg>
                            </x-slot:icon>
                        </x-nav-link>

                        <x-nav-link href="{{ route('adjustment-types.index') }}"
                            label="{{ __('app.nav.adjustment_types') }}" :active="request()->routeIs('adjustment-types.*')">
                            <x-slot:icon>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v8m4-4H8" />
                                </svg>
                            </x-slot:icon>
                        </x-nav-link>
                    </div>
                @endif
            </nav>

            <div class="border-t border-stone-200 p-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-stone-600 hover:bg-stone-50 hover:text-stone-900">
                        <span
                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-stone-100 text-stone-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </span>
                        {{ __('app.nav.logout') }}
                    </button>
                </form>
            </div>

            <div id="sidebar-resizer" class="absolute inset-y-0 right-0" role="separator"
                aria-orientation="vertical" aria-label="Resize sidebar"></div>
        </aside>

        {{-- Main content --}}
        <div class="flex min-w-0 flex-1 flex-col">
            <header
                class="sticky top-0 z-10 flex items-center gap-3 border-b border-stone-200 bg-stone-100/95 px-4 py-3 backdrop-blur lg:px-8">
                <button id="sidebar-toggle" type="button"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-stone-300 bg-white text-stone-600 hover:bg-stone-50"
                    aria-label="Open navigation menu">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-medium text-stone-900">@yield('title', 'Dashboard')</p>
                    <p class="truncate text-xs text-stone-500">{{ config('app.name', 'Mushroom Farm EMS') }}</p>
                </div>

                <x-theme-switcher />

                <div class="flex shrink-0 items-center gap-1 rounded-lg border border-stone-300 bg-white p-1 text-sm">
                    <form method="POST" action="{{ route('locale.update', 'en') }}">
                        @csrf
                        <button type="submit"
                            class="rounded-md px-2.5 py-1 font-medium {{ app()->getLocale() === 'en' ? 'bg-emerald-600 text-white' : 'text-stone-600 hover:bg-stone-50' }}">
                            EN
                        </button>
                    </form>
                    <form method="POST" action="{{ route('locale.update', 'my') }}">
                        @csrf
                        <button type="submit"
                            class="rounded-md px-2.5 py-1 font-medium {{ app()->getLocale() === 'my' ? 'bg-emerald-600 text-white' : 'text-stone-600 hover:bg-stone-50' }}">
                            မြန်မာ
                        </button>
                    </form>
                </div>
            </header>

            <main class="flex-1 px-4 py-6 lg:px-8 lg:py-8">
                @if (session('success'))
                    <x-alert type="success" :message="session('success')" />
                @endif

                @if (session('error'))
                    <x-alert type="error" :message="session('error')" />
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>

</html>
