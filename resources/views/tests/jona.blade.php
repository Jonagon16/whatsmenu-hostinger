<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Prueba - Jona</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-6 text-blue-700">🧪 Panel de Prueba (Jona)</h1>

        <!-- Alertas -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Estado Actual -->
            <div class="bg-white p-6 rounded shadow">
                <h2 class="text-xl font-bold mb-4">1. Estado del Usuario</h2>
                @if($user)
                    <p>✅ <strong>Usuario:</strong> {{ $user->name }} ({{ $user->email }})</p>
                    @if($bot)
                        <p class="mt-2">✅ <strong>Bot Configurado:</strong></p>
                        <ul class="list-disc ml-5 text-sm text-gray-600">
                            <li>Nombre: {{ $bot->bot_name }}</li>
                            <li>Phone ID: {{ $bot->whatsapp_phone_number_id }}</li>
                            <li>Token Verify: {{ $bot->whatsapp_verify_token }}</li>
                            <li>Secret: {{ $bot->whatsapp_app_secret ? 'Configurado' : 'Faltante' }}</li>
                        </ul>
                    @else
                        <p class="text-red-500 mt-2">❌ Bot no configurado.</p>
                    @endif
                @else
                    <p class="text-red-500">❌ Usuario Jona no existe.</p>
                @endif

                <form action="{{ route('tests.reset') }}" method="POST" class="mt-4">
                    @csrf
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        🔄 Resetear / Crear Datos de Prueba
                    </button>
                    <p class="text-xs text-gray-500 mt-1">Esto borrará el menú actual de Jona y creará uno nuevo.</p>
                </form>
            </div>

            <!-- Simulador -->
            <div class="bg-white p-6 rounded shadow">
                <h2 class="text-xl font-bold mb-4">2. Simular Webhook</h2>
                <p class="text-sm text-gray-600 mb-4">Envía un mensaje como si fueras un cliente en WhatsApp.</p>
                
                <form action="{{ route('tests.simulate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="phone_id" value="{{ $bot->whatsapp_phone_number_id ?? '' }}">
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Mensaje:</label>
                        <input type="text" name="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Ej: hola, menu" value="hola">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Teléfono Cliente:</label>
                        <input type="text" name="user_phone" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="5491100000000">
                    </div>

                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded" {{ !$bot ? 'disabled' : '' }}>
                        📲 Enviar Mensaje
                    </button>
                </form>
            </div>
        </div>

        <!-- Logs -->
        <div class="bg-white p-6 rounded shadow mt-6">
            <h2 class="text-xl font-bold mb-4">3. Logs de Mensajes (Últimos 20)</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto text-sm">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="px-4 py-2">ID</th>
                            <th class="px-4 py-2">Dirección</th>
                            <th class="px-4 py-2">Tipo</th>
                            <th class="px-4 py-2">Cuerpo</th>
                            <th class="px-4 py-2">Hora</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr class="border-b">
                                <td class="px-4 py-2">{{ $log->id }}</td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 rounded {{ $log->direction === 'inbound' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $log->direction }}
                                    </span>
                                </td>
                                <td class="px-4 py-2">{{ $log->type }}</td>
                                <td class="px-4 py-2 font-mono text-xs max-w-md truncate">{{ $log->body }}</td>
                                <td class="px-4 py-2">{{ $log->created_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-gray-500">No hay mensajes registrados aún.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-8 text-center text-gray-500 text-sm">
            <p>Este panel es temporal. Eliminar route 'test-panel' y controlador al finalizar.</p>
        </div>
    </div>
</body>
</html>
