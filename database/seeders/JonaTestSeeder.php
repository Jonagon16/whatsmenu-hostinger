<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\BotConfig;
use App\Models\Menu;
use App\Models\MenuNode;
use App\Models\MenuOption;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class JonaTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Crear Usuario
        $user = User::firstOrCreate(
            ['email' => 'jona@gmail.com'],
            [
                'name' => 'Jona Test',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // 2. Configurar Bot (Tenant)
        // Usamos datos ficticios para pruebas locales.
        // En prod, el usuario editaría esto con sus credenciales reales.
        $bot = BotConfig::updateOrCreate(
            ['user_id' => $user->id],
            [
                'whatsapp_phone_number_id' => '123456789_TEST_ID', // ID Simulado
                'whatsapp_business_account_id' => '987654321_TEST_WABA',
                'whatsapp_access_token' => 'test_access_token',
                'whatsapp_verify_token' => 'test_verify_token',
                'whatsapp_app_secret' => 'test_secret', // Si usas validación de firma, esto debe coincidir
                'bot_name' => 'Bot Pizzería Jona',
                'is_active' => true
            ]
        );

        // 3. Crear Menú
        // Borramos menú anterior para asegurar limpieza en pruebas
        Menu::where('user_id', $user->id)->delete();
        
        $menu = Menu::create([
            'user_id' => $user->id,
            'name' => 'Menú Principal Jona',
            'is_active' => true,
            'tree' => [] // Legacy field, usamos nodos ahora
        ]);

        // 4. Nodos del Menú
        
        // --- ROOT ---
        $root = MenuNode::create([
            'menu_id' => $menu->id,
            'slug' => 'root',
            'type' => 'buttons',
            'body_text' => "🍕 ¡Hola! Bienvenido a Pizzería Jona. \n¿Qué te gustaría hacer hoy?",
            'title' => 'Bienvenida',
            'footer_text' => 'Selecciona una opción'
        ]);

        MenuOption::create([
            'menu_node_id' => $root->id,
            'label' => 'Ver Menú',
            'next_node_slug' => 'categorias',
            'sort_order' => 1
        ]);

        MenuOption::create([
            'menu_node_id' => $root->id,
            'label' => 'Horarios y Ubicación',
            'next_node_slug' => 'info',
            'sort_order' => 2
        ]);

        // --- CATEGORIAS ---
        $categorias = MenuNode::create([
            'menu_id' => $menu->id,
            'slug' => 'categorias',
            'type' => 'list',
            'body_text' => 'Tenemos estas categorías de productos:',
            'title' => 'Nuestro Menú',
            'footer_text' => 'Toca ver opciones'
        ]);

        MenuOption::create([
            'menu_node_id' => $categorias->id,
            'label' => 'Pizzas Clásicas',
            'description' => 'Muzza, Napo, Fugazzeta',
            'next_node_slug' => 'pizzas_clasicas',
            'sort_order' => 1
        ]);

        MenuOption::create([
            'menu_node_id' => $categorias->id,
            'label' => 'Empanadas',
            'description' => 'Carne, Pollo, JyQ',
            'next_node_slug' => 'empanadas',
            'sort_order' => 2
        ]);
        
        MenuOption::create([
            'menu_node_id' => $categorias->id,
            'label' => 'Volver al inicio',
            'next_node_slug' => 'root',
            'sort_order' => 99
        ]);

        // --- PIZZAS CLASICAS ---
        $pizzas = MenuNode::create([
            'menu_id' => $menu->id,
            'slug' => 'pizzas_clasicas',
            'type' => 'text', // Solo texto por ahora, o podría ser lista de productos
            'body_text' => "*Pizzas Clásicas:*\n\n1. Muzzarella - $8000\n2. Napolitana - $9500\n3. Especial - $10000\n\nEscribí el nombre de la pizza para pedir (Simulado).",
            'footer_text' => 'Escribe "menu" para volver'
        ]);
        
        // --- INFO ---
        MenuNode::create([
            'menu_id' => $menu->id,
            'slug' => 'info',
            'type' => 'text',
            'body_text' => "📍 *Ubicación:* Calle Falsa 123\n🕒 *Horarios:* Mar a Dom de 19 a 23hs.\n\n¡Te esperamos!",
        ]);

    }
}
