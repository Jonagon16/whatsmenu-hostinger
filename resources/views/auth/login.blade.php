<x-guest-layout>
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8 border mx-4 sm:mx-auto">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold">Bienvenido de nuevo</h2>
            <p class="text-gray-500 mt-2">Accede a tu panel de control.</p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input id="email" 
                       class="w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-emerald-500 transition-all outline-none" 
                       type="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       placeholder="tu@email.com"
                       required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                <input id="password" 
                       class="w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-emerald-500 transition-all outline-none" 
                       type="password" 
                       name="password" 
                       placeholder="••••••••"
                       required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between text-sm">
                <label for="remember_me" class="flex items-center gap-2 text-gray-500">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500" name="remember">
                    <span>Recordarme</span>
                </label>
                @if (Route::has('password.request'))
                    <a class="text-emerald-600 hover:underline" href="{{ route('password.request') }}">
                        {{ __('¿Olvidaste tu contraseña?') }}
                    </a>
                @endif
            </div>

            <button type="submit" class="w-full py-4 bg-emerald-600 text-white font-bold rounded-xl shadow-lg hover:bg-emerald-700 transition-all active:scale-95">
                {{ __('Iniciar Sesión') }}
            </button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-6">
            ¿No tienes cuenta? <a href="{{ route('register') }}" class="text-emerald-600 font-bold hover:underline">Regístrate gratis</a>
        </p>
    </div>
</x-guest-layout>
