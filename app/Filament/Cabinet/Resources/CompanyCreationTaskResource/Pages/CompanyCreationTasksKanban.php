<?php
// app/Filament/Cabinet/Resources/CompanyCreationTaskResource/Pages/CompanyCreationTasksKanban.php

namespace App\Filament\Cabinet\Resources\CompanyCreationTaskResource\Pages;

use App\Filament\Cabinet\Resources\CompanyCreationTaskResource;
use App\Models\CompanyCreationTask;
use Filament\Resources\Pages\Page;
use Filament\Actions;
use Illuminate\Database\Eloquent\Builder;
use Filament\Facades\Filament;

class CompanyCreationTasksKanban extends Page
{
    protected static string $resource = CompanyCreationTaskResource::class;
    protected static string $view = 'filament.cabinet.resources.company-creation-task-resource.pages.kanban';
    protected static ?string $title = 'Vue Kanban - Création de Sociétés';
    protected static ?string $navigationIcon = 'heroicon-o-view-columns';

    public $stages = [
        'initial_contact' => [
            'label' => 'Contact Initial',
            'color' => 'gray',
            'icon' => 'heroicon-o-phone',
        ],
        'certificat_negatif' => [
            'label' => 'Certificat Négatif',
            'color' => 'blue',
            'icon' => 'heroicon-o-document-check',
        ],
        'statuts' => [
            'label' => 'Rédaction des Statuts',
            'color' => 'indigo',
            'icon' => 'heroicon-o-document-text',
        ],
        'capital_deposit' => [
            'label' => 'Dépôt du Capital',
            'color' => 'purple',
            'icon' => 'heroicon-o-banknotes',
        ],
        'enregistrement' => [
            'label' => 'Enregistrement',
            'color' => 'pink',
            'icon' => 'heroicon-o-clipboard-document-check',
        ],
        'patente' => [
            'label' => 'Taxe Professionnelle',
            'color' => 'red',
            'icon' => 'heroicon-o-receipt-percent',
        ],
        'rc_immatriculation' => [
            'label' => 'Immatriculation RC',
            'color' => 'orange',
            'icon' => 'heroicon-o-building-office',
        ],
        'cnss_affiliation' => [
            'label' => 'Affiliation CNSS',
            'color' => 'yellow',
            'icon' => 'heroicon-o-shield-check',
        ],
        'publication' => [
            'label' => 'Publication JAL/BO',
            'color' => 'green',
            'icon' => 'heroicon-o-newspaper',
        ],
        'finalization' => [
            'label' => 'Finalisation',
            'color' => 'success',
            'icon' => 'heroicon-o-check-circle',
        ],
    ];

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('list_view')
                ->label('Vue Liste')
                ->icon('heroicon-o-list-bullet')
                ->url(CompanyCreationTaskResource::getUrl('index'))
                ->color('gray'),

            Actions\CreateAction::make()
                ->label('Nouvelle Tâche')
                ->icon('heroicon-o-plus'),

            Actions\Action::make('refresh')
                ->label('Actualiser')
                ->icon('heroicon-o-arrow-path')
                ->action(function () {
                    return redirect(request()->header('Referer'));
                })
                ->color('gray'),
        ];
    }

    public function getTasksByStage(): array
    {
        $tasksByStage = [];
        $cabinet = Filament::getTenant();

        foreach ($this->stages as $stageKey => $stageInfo) {
            $tasksByStage[$stageKey] = CompanyCreationTask::query()
                ->where('cabinet_id', $cabinet->id)
                ->where('stage', $stageKey)
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->orderBy('position')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return $tasksByStage;
    }

    public function updateTaskStage($taskId, $newStage): void
    {
        $task = CompanyCreationTask::find($taskId);

        if ($task && $task->cabinet_id === Filament::getTenant()->id) {
            $task->update([
                'stage' => $newStage,
                'position' => CompanyCreationTask::where('stage', $newStage)->max('position') + 1
            ]);
        }
    }

    public function getStats(): array
    {
        $cabinet = Filament::getTenant();

        return [
            'total' => CompanyCreationTask::where('cabinet_id', $cabinet->id)
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->count(),
            'in_progress' => CompanyCreationTask::where('cabinet_id', $cabinet->id)
                ->where('status', 'in_progress')
                ->count(),
            'waiting' => CompanyCreationTask::where('cabinet_id', $cabinet->id)
                ->whereIn('status', ['waiting_client', 'waiting_admin'])
                ->count(),
            'overdue' => CompanyCreationTask::where('cabinet_id', $cabinet->id)
                ->overdue()
                ->count(),
        ];
    }
}
