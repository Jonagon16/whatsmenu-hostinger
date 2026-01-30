<x-guest-layout>
    <div class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-black text-gray-900">Planes Simples</h2>
                <p class="mt-4 text-gray-500">Comienza gratis y escala según tus necesidades.</p>
            </div>
            <div class="max-w-4xl mx-auto px-4 grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Free Plan -->
                <div class="border border-gray-200 rounded-3xl p-8 hover:shadow-lg transition-shadow">
                    <h3 class="text-xl font-bold mb-2">Gratis</h3>
                    <div class="text-4xl font-black mb-6">$0<span class="text-sm font-normal text-gray-500">/mes</span></div>
                    <ul class="space-y-3 text-left mb-8 text-gray-600 text-sm">
                        <li class="flex items-center"><span class="text-emerald-500 mr-2">✓</span> 500 interacciones/mes</li>
                        <li class="flex items-center"><span class="text-emerald-500 mr-2">✓</span> Menú básico</li>
                        <li class="flex items-center"><span class="text-emerald-500 mr-2">✓</span> Soporte por email</li>
                    </ul>
                    <a href="{{ route('register') }}" class="block w-full py-3 px-6 bg-gray-100 hover:bg-gray-200 rounded-xl font-bold text-center text-gray-900 transition-colors">Empezar Gratis</a>
                </div>
                <!-- Pro Plan -->
                <div class="border-2 border-emerald-500 rounded-3xl p-8 shadow-xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 bg-emerald-500 text-white text-xs font-bold px-3 py-1 rounded-bl-xl">POPULAR</div>
                    <h3 class="text-xl font-bold mb-2">Pro</h3>
                    <div class="text-4xl font-black mb-6">$29<span class="text-sm font-normal text-gray-500">/mes</span></div>
                    <ul class="space-y-3 text-left mb-8 text-gray-600 text-sm">
                        <li class="flex items-center"><span class="text-emerald-500 mr-2">✓</span> Interacciones ilimitadas</li>
                        <li class="flex items-center"><span class="text-emerald-500 mr-2">✓</span> Menú avanzado + IA</li>
                        <li class="flex items-center"><span class="text-emerald-500 mr-2">✓</span> Reportes detallados</li>
                        <li class="flex items-center"><span class="text-emerald-500 mr-2">✓</span> Soporte prioritario</li>
                    </ul>
                    <a href="{{ route('register') }}" class="block w-full py-3 px-6 bg-emerald-600 hover:bg-emerald-700 rounded-xl font-bold text-center text-white transition-colors">Elegir Pro</a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
