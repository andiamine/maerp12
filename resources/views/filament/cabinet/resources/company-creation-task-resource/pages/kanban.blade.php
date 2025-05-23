{{-- resources/views/filament/cabinet/resources/company-creation-task-resource/pages/kanban.blade.php --}}

<x-filament-panels::page>
    <div class="space-y-4">
        {{-- Stats Cards --}}
        <div class="grid gap-4 md:grid-cols-4">
            @php($stats = $this->getStats())

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-heroicon-o-clipboard-document-list class="h-8 w-8 text-gray-400"/>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Actif</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-heroicon-o-arrow-path class="h-8 w-8 text-blue-400"/>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">En Cours</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $stats['in_progress'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-heroicon-o-clock class="h-8 w-8 text-yellow-400"/>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">En Attente</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $stats['waiting'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-heroicon-o-exclamation-triangle class="h-8 w-8 text-red-400"/>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">En Retard</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $stats['overdue'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kanban Board --}}
        <div class="overflow-x-auto pb-4">
            <div class="inline-flex space-x-4 p-1" style="min-width: max-content;">
                @php($tasksByStage = $this->getTasksByStage())

                @foreach($stages as $stageKey => $stageInfo)
                    <div class="w-80 flex-shrink-0">
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg">
                            {{-- Column Header --}}
                            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <x-dynamic-component
                                            :component="$stageInfo['icon']"
                                            class="h-5 w-5 text-{{ $stageInfo['color'] }}-500"
                                        />
                                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                                            {{ $stageInfo['label'] }}
                                        </h3>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $stageInfo['color'] }}-100 text-{{ $stageInfo['color'] }}-800">
                                        {{ count($tasksByStage[$stageKey]) }}
                                    </span>
                                </div>
                            </div>

                            {{-- Tasks Container --}}
                            <div class="p-2 space-y-2 min-h-[400px] kanban-column" data-stage="{{ $stageKey }}">
                                @forelse($tasksByStage[$stageKey] as $task)
                                    <div
                                        class="kanban-task bg-white dark:bg-gray-700 p-4 rounded-lg shadow hover:shadow-md transition-shadow cursor-move"
                                        data-task-id="{{ $task->id }}"
                                        draggable="true"
                                    >
                                        {{-- Task Header --}}
                                        <div class="flex items-start justify-between mb-2">
                                            <h4 class="text-sm font-medium text-gray-900 dark:text-white line-clamp-2">
                                                {{ $task->company_name }}
                                            </h4>
                                            <div class="flex items-center space-x-1">
                                                @if($task->isOverdue())
                                                    <x-heroicon-m-exclamation-triangle class="h-4 w-4 text-red-500" />
                                                @endif
                                                <x-filament::dropdown>
                                                    <x-slot name="trigger">
                                                        <button type="button" class="p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                                            <x-heroicon-m-ellipsis-vertical class="h-4 w-4 text-gray-500" />
                                                        </button>
                                                    </x-slot>

                                                    <x-filament::dropdown.list>
                                                        <x-filament::dropdown.list.item
                                                            :href="\App\Filament\Cabinet\Resources\CompanyCreationTaskResource::getUrl('view', ['record' => $task])"
                                                            icon="heroicon-m-eye"
                                                        >
                                                            Voir
                                                        </x-filament::dropdown.list.item>

                                                        <x-filament::dropdown.list.item
                                                            :href="\App\Filament\Cabinet\Resources\CompanyCreationTaskResource::getUrl('edit', ['record' => $task])"
                                                            icon="heroicon-m-pencil"
                                                        >
                                                            Modifier
                                                        </x-filament::dropdown.list.item>
                                                    </x-filament::dropdown.list>
                                                </x-filament::dropdown>
                                            </div>
                                        </div>

                                        {{-- Task Type Badge --}}
                                        <div class="mb-2">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200">
                                                {{ $task->company_type }}
                                            </span>
                                        </div>

                                        {{-- Client Info --}}
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                            <span class="font-medium">Client:</span> {{ $task->client_name }}
                                        </p>

                                        {{-- Progress Bar --}}
                                        <div class="mb-2">
                                            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                <span>Progression</span>
                                                <span>{{ $task->progress_percentage }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-1.5">
                                                <div
                                                    class="h-1.5 rounded-full transition-all duration-300
                                                        @if($task->progress_percentage < 25) bg-red-500
                                                        @elseif($task->progress_percentage < 50) bg-yellow-500
                                                        @elseif($task->progress_percentage < 75) bg-blue-500
                                                        @else bg-green-500
                                                        @endif"
                                                    style="width: {{ $task->progress_percentage }}%"
                                                ></div>
                                            </div>
                                        </div>

                                        {{-- Task Footer --}}
                                        <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                                            @if($task->user)
                                                <div class="flex items-center space-x-1">
                                                    <x-heroicon-m-user class="h-3 w-3" />
                                                    <span>{{ $task->user->name }}</span>
                                                </div>
                                            @endif

                                            @if($task->target_completion_date)
                                                <div class="flex items-center space-x-1">
                                                    <x-heroicon-m-calendar class="h-3 w-3" />
                                                    <span>{{ $task->target_completion_date->format('d/m') }}</span>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Status Badge --}}
                                        <div class="mt-2">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                                @if($task->status === 'in_progress') bg-blue-100 text-blue-800
                                                @elseif($task->status === 'waiting_client') bg-yellow-100 text-yellow-800
                                                @elseif($task->status === 'waiting_admin') bg-orange-100 text-orange-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ match($task->status) {
                                                    'draft' => 'Brouillon',
                                                    'in_progress' => 'En cours',
                                                    'waiting_client' => 'Attente client',
                                                    'waiting_admin' => 'Attente admin',
                                                    default => $task->status
                                                } }}
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                        <x-heroicon-o-inbox class="h-8 w-8 mx-auto mb-2 opacity-50" />
                                        <p class="text-sm">Aucune tâche</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const columns = document.querySelectorAll('.kanban-column');

                columns.forEach(column => {
                    new Sortable(column, {
                        group: 'shared',
                        animation: 150,
                        ghostClass: 'opacity-50',
                        dragClass: 'shadow-lg',
                        handle: '.kanban-task',
                        onEnd: function(evt) {
                            const taskId = evt.item.dataset.taskId;
                            const newStage = evt.to.dataset.stage;

                            // Send AJAX request to update task stage
                            fetch('{{ route('filament.cabinet.resources.company-creation-tasks.update-stage') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    taskId: taskId,
                                    newStage: newStage
                                })
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        // Show success notification
                                        window.dispatchEvent(
                                            new CustomEvent('notify', {
                                                detail: {
                                                    message: 'Étape mise à jour avec succès',
                                                    type: 'success'
                                                }
                                            })
                                        );
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    // Revert the drag if error
                                    evt.from.insertBefore(evt.item, evt.from.children[evt.oldIndex]);
                                });
                        }
                    });
                });
            });
        </script>
    @endpush
</x-filament-panels::page>
