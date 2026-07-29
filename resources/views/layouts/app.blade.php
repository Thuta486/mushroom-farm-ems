<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') — {{ config('app.name', 'Mushroom Farm EMS') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-100 font-sans text-stone-900 antialiased">
    <div class="min-h-screen">
        <header class="border-b border-stone-200 bg-white">
            <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-4 sm:px-6">
                <div>
                    <a href="{{ route('dashboard') }}" class="text-lg font-semibold text-emerald-800">
                        Mushroom Farm EMS
                    </a>
                    <p class="text-sm text-stone-500">Employee Management</p>
                </div>

                <nav class="flex items-center gap-1 sm:gap-2">
                    <a href="{{ route('dashboard') }}"
                       class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-emerald-50 text-emerald-800' : 'text-stone-600 hover:bg-stone-50' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('employees.index') }}"
                       class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('employees.*') ? 'bg-emerald-50 text-emerald-800' : 'text-stone-600 hover:bg-stone-50' }}">
                        Employees
                    </a>
                    <a href="{{ route('departments.index') }}"
                       class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('departments.*') ? 'bg-emerald-50 text-emerald-800' : 'text-stone-600 hover:bg-stone-50' }}">
                        Departments
                    </a>
                </nav>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-lg border border-stone-300 px-3 py-2 text-sm font-medium text-stone-700 hover:bg-stone-50">
                        Log out
                    </button>
                </form>
            </div>
        </header>

        <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
            @if (session('success'))
                <x-alert type="success" :message="session('success')" />
            @endif

            @if (session('error'))
                <x-alert type="error" :message="session('error')" />
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>
