<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Total Leads -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-900 font-bold text-xl mb-2">Total Leads</div>
                    <div class="text-4xl text-blue-600">{{ $totalLeads }}</div>
                </div>
                <!-- Total Messages -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-900 font-bold text-xl mb-2">Total Messages</div>
                    <div class="text-4xl text-green-600">{{ $totalMessages }}</div>
                </div>
            </div>

            <!-- Recent Leads -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Recent Leads</h3>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="text-left py-2">Phone</th>
                                <th class="text-left py-2">Last Interaction</th>
                                <th class="text-left py-2">Interactions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentLeads as $lead)
                                <tr>
                                    <td class="py-2">{{ $lead->phone }}</td>
                                    <td class="py-2">{{ $lead->last_interaction ? $lead->last_interaction->diffForHumans() : 'N/A' }}</td>
                                    <td class="py-2">{{ $lead->interactions_count }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-gray-500 py-2">No leads yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Logs -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Recent Logs</h3>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="text-left py-2">From</th>
                                <th class="text-left py-2">Message</th>
                                <th class="text-left py-2">Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentLogs as $log)
                                <tr>
                                    <td class="py-2">{{ $log->from_number }}</td>
                                    <td class="py-2 truncate max-w-xs">{{ $log->message }}</td>
                                    <td class="py-2">{{ $log->created_at->diffForHumans() }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-gray-500 py-2">No logs yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
