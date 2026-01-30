<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>WhatsMenu Bot</title>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-gray-900 bg-white">
        <!-- Navigation -->
        <nav class="absolute top-0 w-full z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    <!-- Logo -->
                    <div class="flex-shrink-0 flex items-center">
                        <span class="font-black text-2xl tracking-tighter">
                            <span class="text-[#25D366]">Whats</span>menu.bot
                        </span>
                    </div>
                    <!-- Links -->
                    <div class="flex space-x-8 items-center">
                        <a href="#features" class="text-gray-500 hover:text-gray-900 font-medium">Beneficios</a>
                        <a href="#pricing" class="text-gray-500 hover:text-gray-900 font-medium">Precios</a>
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-gray-900 font-bold hover:text-[#25D366]">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-900 font-medium hover:text-[#25D366]">Ingresar</a>
                            <a href="{{ route('register') }}" class="px-5 py-2.5 bg-black text-white rounded-xl font-bold hover:bg-gray-800 transition-all shadow-lg shadow-gray-200">
                                Registrarse
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <div class="overflow-hidden">
            <!-- Hero Section -->
            <section class="relative pt-32 pb-32 bg-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="lg:grid lg:grid-cols-12 lg:gap-8">
                        <div class="sm:text-center md:max-w-2xl md:mx-auto lg:col-span-6 lg:text-left pt-10">
                            <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl lg:text-5xl xl:text-6xl">
                                <span class="block">Automatiza tu Negocio</span>
                                <span class="block mt-1">
                                    <span class="text-[#25D366]">Whats</span>menu.bot
                                </span>
                            </h1>
                            <p class="mt-6 text-base text-gray-500 sm:mt-5 sm:text-xl lg:text-lg xl:text-xl leading-relaxed">
                                Crea menús interactivos profesionales para WhatsApp Business.
                                Sin spam, 100% oficial y diseñado para la eficiencia de tu PyME.
                            </p>
                            <div class="mt-10 sm:flex sm:justify-center lg:justify-start gap-4">
                                <a href="{{ route('register') }}"
                                   class="flex items-center justify-center px-8 py-4 border border-transparent text-base font-bold rounded-2xl text-white bg-gray-900 hover:bg-emerald-600 md:text-lg shadow-xl shadow-gray-200 transition-all hover:scale-105">
                                    Empezar ahora <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                </a>
                                <a href="#features"
                                   class="mt-3 sm:mt-0 flex items-center justify-center px-8 py-4 border-2 border-gray-100 text-base font-bold rounded-2xl text-gray-600 bg-gray-50 hover:bg-white transition-all md:text-lg">
                                    Ver beneficios
                                </a>
                            </div>
                        </div>
                        <div class="mt-12 relative sm:max-w-lg sm:mx-auto lg:mt-0 lg:max-w-none lg:mx-0 lg:col-span-6 lg:flex lg:items-center">
                            <div class="relative mx-auto w-full rounded-[2.5rem] shadow-2xl overflow-hidden bg-gray-900 p-4 border-[10px] border-gray-800 transform rotate-1 hover:rotate-0 transition-all duration-500">
                                <div class="bg-[#e5ddd5] rounded-2xl h-[450px] p-6 space-y-4 overflow-y-auto font-sans">
                                    <div class="flex gap-2">
                                        <div class="bg-white text-gray-800 p-3 rounded-2xl rounded-bl-none text-xs max-w-[85%] shadow-sm leading-relaxed">
                                            ¡Hola! Gracias por escribir a <strong>Pizzería Don Juan 🍕</strong>. Elige una opción:<br/><br/>
                                            1. Ver Menú 📜<br/>
                                            2. Promos del día 🏷️<br/>
                                            3. Delivery / Retiro 🛵
                                        </div>
                                    </div>
                                    <div class="flex justify-end">
                                        <div class="bg-[#dcf8c6] text-gray-800 p-3 rounded-2xl rounded-br-none text-xs shadow-sm font-medium">
                                            1
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <div class="bg-white text-gray-800 p-3 rounded-2xl rounded-bl-none text-xs max-w-[85%] shadow-sm leading-relaxed">
                                            Nuestras pizzas:<br/><br/>
                                            1. Muzzarella Clásica 🧀<br/>
                                            2. Fugazzeta Especial 🧅<br/>
                                            0. Volver atrás
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Benefits Section -->
            <section id="features" class="py-24 bg-gray-50 border-t border-gray-100">
                <div class="max-w-7xl mx-auto px-4 text-center mb-16">
                    <h2 class="text-3xl font-black text-gray-900">¿Por qué elegir <span class="text-[#25D366]">Whats</span>menu.bot?</h2>
                    <p class="mt-4 text-gray-500 max-w-2xl mx-auto italic">"Una atención rápida convierte más que una buena publicidad."</p>
                </div>
                <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm transition-all hover:shadow-lg hover:-translate-y-1">
                        <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold mb-3">API Oficial</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">Operamos bajo las reglas de la API de Cloud WhatsApp, garantizando seguridad y cero recargos externos.</p>
                    </div>
                    <!-- Feature 2 -->
                    <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm transition-all hover:shadow-lg hover:-translate-y-1">
                        <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-500 mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                        </div>
                        <h3 class="text-xl font-bold mb-3">Atención 24h</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">El bot atiende dentro de la ventana oficial de 24 horas, automatizando consultas frecuentes de forma eficiente.</p>
                    </div>
                    <!-- Feature 3 -->
                    <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm transition-all hover:shadow-lg hover:-translate-y-1">
                        <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-500 mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold mb-3">Privacidad Garantizada</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">No guardamos números de terceros ni los utilizamos con fines comerciales externos. Privacidad como estandarte.</p>
                    </div>
                </div>
            </section>
        </div>
    </body>
</html>
