<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('app.login.name') }}</title>
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
    <div class="flex min-h-screen items-center justify-center px-4">
        <div class="w-full max-w-md">

            <!-- Language Switcher -->
            <div class="mb-4 flex items-center justify-end gap-2">
                <x-theme-switcher />

                <div class="flex gap-1 rounded-lg border border-stone-300 bg-white p-1 text-sm">
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
            </div>

            <!-- Login Card -->
            <div class="rounded-2xl border border-stone-200 bg-white p-8 shadow-sm">
                <div class="mb-8 text-center">
                    <h1 class="text-2xl font-semibold text-emerald-800">
                        {{ __('app.login.name') }}
                    </h1>
                    <p class="mt-2 text-sm text-stone-500">
                        {{ __('app.login.subtitle') }}
                    </p>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <x-form-input name="email" :label="__('app.login.email')" type="email" :value="old('email')" required />

                    <x-form-input name="password" :label="__('app.login.password')" type="password" required />

                    <label class="flex items-center gap-2 text-sm text-stone-600">
                        <input type="checkbox" name="remember"
                            class="rounded border-stone-300 text-emerald-700 focus:ring-emerald-500">
                        {{ __('app.login.remember') }}
                    </label>

                    @if ($errors->any())
                        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <x-button type="submit" class="w-full">
                        {{ __('app.login.submit') }}
                    </x-button>
                </form>
            </div>

        </div>
    </div>
</body>

</html>
