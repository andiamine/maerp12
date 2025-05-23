<?php
// app/Filament/Admin/Widgets/MonthlyGrowth.php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Cabinet;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MonthlyGrowth extends ChartWidget
{
    protected static ?string $heading = 'Croissance Mensuelle des Sociétés';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $months = collect();
        for ($i = 11; $i >= 0; $i--) {
            $months->push(Carbon::now()->subMonths($i));
        }

        $companyData = $months->map(function ($month) {
            return Company::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
        });

        return [
            'datasets' => [
                [
                    'label' => 'Sociétés',
                    'data' => $companyData->toArray(),
                    'borderColor' => 'rgb(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
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
                    'display' => true,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
        ];
    }
}
