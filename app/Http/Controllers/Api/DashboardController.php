<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Lead;
use App\Models\WhatsappLog;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        $user = $request->user();
        
        $stats = [
            'total_leads' => $user->leads()->count(),
            'messages_sent' => $user->whatsappLogs()->where('was_ai', true)->count(),
            'active_menus' => $user->menus()->where('is_active', true)->count(),
            'plan' => $user->plan,
        ];

        return response()->json($stats);
    }

    public function chartData(Request $request)
    {
        $user = $request->user();
        
        // Example: Last 7 days messages
        $data = $user->whatsappLogs()
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($data);
    }

    public function activity(Request $request)
    {
        $user = $request->user();
        
        $logs = $user->whatsappLogs()
            ->latest()
            ->take(10)
            ->get();

        return response()->json($logs);
    }
}
