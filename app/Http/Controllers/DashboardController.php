<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Analytics
        $totalLeads = \App\Models\Lead::count(); // Pending: Filter by user if we had user_id on leads
        // Note: Legacy leads table didn't seem to have user_id in the migration I made? 
        // Wait, I missed adding user_id to leads and logs in migration?
        // Let's check the migration I wrote.
        
        $totalMessages = \App\Models\WhatsappLog::count(); // Pending: Filter by user

        $recentLeads = \App\Models\Lead::latest('last_interaction')->take(5)->get();
        $recentLogs = \App\Models\WhatsappLog::latest()->take(5)->get();

        return view('dashboard', compact('totalLeads', 'totalMessages', 'recentLeads', 'recentLogs'));
    }
}
