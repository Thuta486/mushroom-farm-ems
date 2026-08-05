<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('app.password_reset.title') }}</title>
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

            <!-- Forgot Password Card -->
            <div class="rounded-2xl border border-stone-200 bg-white p-8 shadow-sm">
                <div class="mb-8 text-center">
                    <h1 class="text-2xl font-semibold text-emerald-800">
                        {{ __('app.password_reset.title') }}
                    </h1>
                    <p class="mt-2 text-sm text-stone-500">
                        {{ __('app.password_reset.subtitle') }}
                    </p>
                </div>

                @if (session('status'))
                    <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                    @csrf

                    <x-form-input name="email" :label="__('app.password_reset.email')" type="email" :value="old('email')" required />

                    <x-button type="submit" class="w-full">
                        {{ __('app.password_reset.send_link') }}
                    </x-button>
                </form>

                <div class="mt-6 text-center text-sm">
                    <a href="{{ route('login') }}" class="font-medium text-emerald-700 hover:text-emerald-800">
                        {{ __('app.password_reset.back_to_login') }}
                    </a>
                </div>
            </div>

        </div>
    </div>
</body>

</html>
