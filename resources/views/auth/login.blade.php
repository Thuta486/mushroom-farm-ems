<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — Mushroom Farm EMS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-100 font-sans text-stone-900 antialiased">
    <div class="flex min-h-screen items-center justify-center px-4">
        <div class="w-full max-w-md rounded-2xl border border-stone-200 bg-white p-8 shadow-sm">
            <div class="mb-8 text-center">
                <h1 class="text-2xl font-semibold text-emerald-800">Mushroom Farm EMS</h1>
                <p class="mt-2 text-sm text-stone-500">Sign in to manage employees</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <x-form-input name="email" label="Email" type="email" :value="old('email')" required />

                <x-form-input name="password" label="Password" type="password" required />

                <label class="flex items-center gap-2 text-sm text-stone-600">
                    <input type="checkbox" name="remember" class="rounded border-stone-300 text-emerald-700 focus:ring-emerald-500">
                    Remember me
                </label>

                @if ($errors->any())
                    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        {{ $errors->first() }}
                    </div>
                @endif

                <x-button type="submit" class="w-full">
                    Sign in
                </x-button>
            </form>
        </div>
    </div>
</body>
</html>
