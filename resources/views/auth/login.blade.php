{{-- <x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" id="loginForm">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required
                autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
            <x-input-error :messages="$errors->get('g-recaptcha-response')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ml-3" type='button' onclick="onClickLogin(event)">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
    <script>
        function onClickLogin(e) {
          e.preventDefault();
          grecaptcha.ready(function() {
            grecaptcha.execute('{{config('services.recaptcha.site_key')}}', {action: 'login'}).then(function(token) {
                document.getElementById("g-recaptcha-response").value = token;
                document.getElementById("loginForm").submit();
            });
          });
        }
    </script>
</x-guest-layout> --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - {{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
</head>

<body class="font-sans text-gray-900 antialiased overflow-hidden bg-white">

    <div class="min-h-screen flex">

        <div class="hidden lg:flex lg:w-1/2 bg-cover bg-center relative"
            style="background-image: url('https://images.unsplash.com/photo-1497215728101-856f4ea42174?q=80&w=2070&auto=format&fit=crop');">
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/40 to-transparent"></div>

            <div class="absolute bottom-0 left-0 p-16 text-white w-full">
                <h2 class="text-4xl font-bold mb-4 tracking-tight">Selamat Datang Kembali.</h2>
                <p class="text-lg text-gray-300 max-w-md leading-relaxed">
                    Masuk ke akun Anda untuk mengakses dashboard dan melanjutkan aktivitas hari ini.
                </p>
            </div>
        </div>

        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-12 lg:p-24">
            <div class="w-full max-w-md">

                <div class="mb-10">
                    <h1 class="text-3xl font-extrabold text-gray-900 mb-3 tracking-tight">Sign In</h1>
                    <p class="text-sm text-gray-500">Silakan masukkan kredensial Anda untuk melanjutkan.</p>
                </div>

                @if (session('status'))
                    <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 font-medium text-sm text-green-700">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" id="loginForm" class="space-y-6">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                            autocomplete="username"
                            class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all duration-200 shadow-sm sm:text-sm @error('email') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror">
                        @error('email')
                            <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                            class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all duration-200 shadow-sm sm:text-sm @error('password') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror">
                        @error('password')
                            <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                    @error('g-recaptcha-response')
                        <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                    @enderror

                    <div class="flex items-center justify-between pt-2">
                        <label for="remember_me" class="flex items-center cursor-pointer group">
                            <input id="remember_me" type="checkbox" name="remember"
                                class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 transition duration-150 ease-in-out cursor-pointer group-hover:bg-indigo-50">
                            <span
                                class="ml-2 text-sm text-gray-600 group-hover:text-gray-900 transition-colors">Remember
                                me</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                                class="text-sm font-semibold text-indigo-600 hover:text-indigo-500 transition-colors">
                                Forgot password?
                            </a>
                        @endif
                    </div>

                    <div class="pt-2">
                        <button type="button" onclick="onClickLogin(event)" id="loginBtn"
                            class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform active:scale-[0.98]">
                            Sign In
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function onClickLogin(e) {
            e.preventDefault();

            // UX Enhancement: Loading State
            const btn = document.getElementById('loginBtn');
            const originalText = btn.innerHTML;
            btn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Signing in...
            `;
            btn.classList.add('cursor-not-allowed', 'opacity-90');
            btn.disabled = true;

            // Eksekusi reCAPTCHA
            grecaptcha.ready(function () {
                grecaptcha.execute('{{ config('services.recaptcha.site_key') }}', { action: 'login' })
                    .then(function (token) {
                        document.getElementById("g-recaptcha-response").value = token;
                        document.getElementById("loginForm").submit();
                    })
                    .catch(function (error) {
                        // Kembalikan tombol jika reCAPTCHA gagal (misal: koneksi terputus)
                        btn.innerHTML = originalText;
                        btn.classList.remove('cursor-not-allowed', 'opacity-90');
                        btn.disabled = false;
                    });
            });
        }
    </script>
</body>

</html>
