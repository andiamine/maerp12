<?php
// app/Filament/Admin/Widgets/CabinetChart.php - Version corrigée

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Cabinet;
use Illuminate\Support\Facades\DB;

class CabinetChart extends ChartWidget
{
    protected static ?string $heading = 'Évolution des Cabinets';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = Cabinet::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('count(*) as count')
        )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Nouveaux cabinets',
                    'data' => $data->pluck('count')->toArray(),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                    'fill' => true,
                ],
            ],
            'labels' => $data->pluck('date')->map(function($date) {
                return \Carbon\Carbon::parse($date)->format('d/m');
            })->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
