<x-guest-layout>
    <div class="max-w-xl w-full bg-white rounded-2xl shadow-xl p-8 border mx-4 sm:mx-auto mt-6 mb-6">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold">Crea tu cuenta</h2>
            <p class="text-gray-500 mt-2">Empieza a automatizar hoy mismo.</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-6">
            @csrf

            <!-- Name -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                    <input id="first_name" class="w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-emerald-500 transition-all outline-none" type="text" name="first_name" :value="old('first_name')" required autofocus autocomplete="given-name" />
                    <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                </div>
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Apellido</label>
                    <input id="last_name" class="w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-emerald-500 transition-all outline-none" type="text" name="last_name" :value="old('last_name')" required autocomplete="family-name" />
                    <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                </div>
            </div>

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input id="email" class="w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-emerald-500 transition-all outline-none" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                    <input id="password" class="w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-emerald-500 transition-all outline-none"
                                    type="password"
                                    name="password"
                                    required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar</label>
                    <input id="password_confirmation" class="w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-emerald-500 transition-all outline-none"
                                    type="password"
                                    name="password_confirmation" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                     <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Nacimiento (Opcional)</label>
                     <input type="date" name="birth_date" class="w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-emerald-500 transition-all outline-none" value="{{ old('birth_date') }}">
                </div>
                <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Género</label>
                      <select name="gender" class="w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-emerald-500 transition-all outline-none bg-white">
                        <option value="male">Masculino</option>
                        <option value="female">Femenino</option>
                        <option value="other">Otro / No deseo decirlo</option>
                      </select>
                </div>
            </div>

            <div class="flex items-start gap-3 text-sm text-gray-500">
                <input required type="checkbox" class="mt-1 rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500" name="terms" />
                <p>Acepto los <a href="#" class="text-emerald-600 hover:underline">Términos y Condiciones</a> y el uso de mis datos para la gestión del servicio.</p>
            </div>

            <button type="submit" class="w-full py-4 bg-emerald-600 text-white font-bold rounded-xl shadow-lg hover:bg-emerald-700 transition-all active:scale-95">
                {{ __('Registrarme y empezar prueba gratuita') }}
            </button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-6">
            ¿Ya tienes cuenta? <a href="{{ route('login') }}" class="text-emerald-600 font-bold hover:underline">Inicia sesión</a>
        </p>
    </div>
</x-guest-layout>
