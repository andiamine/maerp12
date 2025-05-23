<?php
// app/Filament/Admin/Widgets/CompaniesByCabinetAndCity.php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Cabinet;
use Illuminate\Support\Facades\DB;

class CompaniesByCabinetAndCity extends ChartWidget
{
    protected static ?string $heading = 'Répartition des Sociétés par Cabinet';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $data = Cabinet::select('cabinets.nom', 'cabinets.ville')
            ->selectRaw('COUNT(companies.id) as companies_count')
            ->leftJoin('companies', 'cabinets.id', '=', 'companies.cabinet_id')
            ->groupBy('cabinets.id', 'cabinets.nom', 'cabinets.ville')
            ->having('companies_count', '>', 0)
            ->orderBy('companies_count', 'desc')
            ->limit(10)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Nombre de sociétés',
                    'data' => $data->pluck('companies_count')->toArray(),
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(236, 72, 153, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(251, 146, 60, 0.8)',
                        'rgba(99, 102, 241, 0.8)',
                        'rgba(168, 85, 247, 0.8)',
                    ],
                    'borderColor' => [
                        'rgba(59, 130, 246, 1)',
                        'rgba(16, 185, 129, 1)',
                        'rgba(245, 158, 11, 1)',
                        'rgba(239, 68, 68, 1)',
                        'rgba(139, 92, 246, 1)',
                        'rgba(236, 72, 153, 1)',
                        'rgba(34, 197, 94, 1)',
                        'rgba(251, 146, 60, 1)',
                        'rgba(99, 102, 241, 1)',
                        'rgba(168, 85, 247, 1)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $data->map(fn ($item) => $item->nom . ' (' . $item->ville . ')')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
        ];
    }
}
