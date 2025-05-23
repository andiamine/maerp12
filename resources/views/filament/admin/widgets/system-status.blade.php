{{-- resources/views/filament/admin/widgets/system-status.blade.php --}}

<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Statut du Système
        </x-slot>

        <x-slot name="headerEnd">
            <div class="text-xs text-gray-500">
                Dernière mise à jour : {{ now()->format('d/m/Y H:i') }}
            </div>
        </x-slot>

        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            {{-- Version PHP --}}
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <div class="text-sm font-medium text-gray-900">Version PHP</div>
                        <div class="text-lg font-semibold text-blue-600">{{ $phpVersion }}</div>
                    </div>
                </div>
            </div>

            {{-- Version Laravel --}}
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <div class="text-sm font-medium text-gray-900">Version Laravel</div>
                        <div class="text-lg font-semibold text-red-600">{{ $laravelVersion }}</div>
                    </div>
                </div>
            </div>

            {{-- Base de données --}}
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        @if($databaseStatus)
                            <svg class="h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                            </svg>
                        @else
                            <svg class="h-8 w-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.896-.833-2.664 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        @endif
                    </div>
                    <div class="ml-3">
                        <div class="text-sm font-medium text-gray-900">Base de données</div>
                        <div class="text-lg font-semibold {{ $databaseStatus ? 'text-green-600' : 'text-red-600' }}">
                            {{ $databaseStatus ? 'Connectée' : 'Déconnectée' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Cache --}}
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        @if($cacheStatus)
                            <svg class="h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        @else
                            <svg class="h-8 w-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.896-.833-2.664 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        @endif
                    </div>
                    <div class="ml-3">
                        <div class="text-sm font-medium text-gray-900">Cache</div>
                        <div class="text-lg font-semibold {{ $cacheStatus ? 'text-green-600' : 'text-red-600' }}">
                            {{ $cacheStatus ? 'Actif' : 'Inactif' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Espace disque --}}
        <div class="mt-6">
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-900 mb-3">Espace Disque</h4>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-600">{{ $diskSpace['used'] }} utilisés sur {{ $diskSpace['total'] }}</span>
                    <span class="text-sm font-medium text-gray-900">{{ $diskSpace['usage_percentage'] }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="h-2 rounded-full {{ $diskSpace['usage_percentage'] > 80 ? 'bg-red-500' : ($diskSpace['usage_percentage'] > 60 ? 'bg-yellow-500' : 'bg-green-500') }}"
                         style="width: {{ $diskSpace['usage_percentage'] }}%"></div>
                </div>
                <div class="mt-2 text-xs text-gray-500">
                    {{ $diskSpace['free'] }} disponibles
                </div>
            </div>
        </div>

        {{-- Actions rapides --}}
        <div class="mt-6 flex gap-2">
            <button type="button" onclick="window.location.reload()"
                    class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Actualiser
            </button>

            <a href="#"
               class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Voir les logs
            </a>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
