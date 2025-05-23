<?php
// app/Filament/Admin/Widgets/SystemStatus.php - Version corrigée

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class SystemStatus extends Widget
{
    protected static string $view = 'filament.admin.widgets.system-status';
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        return [
            'phpVersion' => PHP_VERSION,
            'laravelVersion' => app()->version(),
            'diskSpace' => $this->getDiskSpace(),
            'databaseStatus' => $this->getDatabaseStatus(),
            'cacheStatus' => $this->getCacheStatus(),
        ];
    }

    private function getDiskSpace(): array
    {
        $bytes = disk_free_space('/');
        $totalBytes = disk_total_space('/');

        if ($bytes === false || $totalBytes === false) {
            return [
                'free' => 'N/A',
                'total' => 'N/A',
                'used' => 'N/A',
                'usage_percentage' => 0,
            ];
        }

        $usedBytes = $totalBytes - $bytes;
        $usagePercentage = round(($usedBytes / $totalBytes) * 100, 2);

        return [
            'free' => $this->formatBytes($bytes),
            'total' => $this->formatBytes($totalBytes),
            'used' => $this->formatBytes($usedBytes),
            'usage_percentage' => $usagePercentage,
        ];
    }

    private function getDatabaseStatus(): bool
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function getCacheStatus(): bool
    {
        try {
            cache()->put('test_cache_status', 'value', 1);
            return cache()->get('test_cache_status') === 'value';
        } catch (\Exception $e) {
            return false;
        }
    }

    private function formatBytes($bytes, $precision = 2): string
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
