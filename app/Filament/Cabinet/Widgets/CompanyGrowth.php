<?php

namespace App\Filament\Cabinet\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Company;
use Carbon\Carbon;
use Filament\Facades\Filament;

class CompanyGrowth extends ChartWidget
{
    protected static ?string $heading = 'Croissance des Sociétés';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $cabinet = Filament::getTenant();

        if (!$cabinet) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $months->push(Carbon::now()->subMonths($i));
        }

        $data = $months->map(function ($month) use ($cabinet) {
            return $cabinet->companies()
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
        });

        return [
            'datasets' => [
                [
                    'label' => 'Nouvelles sociétés',
                    'data' => $data->toArray(),
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $months->map(fn ($month) => $month->format('M Y'))->toArray(),
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
                    'display' => false,
                ],
            ],
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
