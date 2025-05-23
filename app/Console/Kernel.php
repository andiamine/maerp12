<?php
// app/Console/Kernel.php - Planification des tâches

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Commands\CheckCabinetExpirations::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // Vérifier les expirations de cabinets tous les jours à 9h
        $schedule->command('cabinets:check-expirations')
            ->dailyAt('09:00')
            ->description('Vérifier les expirations de cabinets');

        // Nettoyer les anciennes notifications tous les dimanche
        $schedule->command('model:prune', ['--model' => 'Illuminate\\Notifications\\DatabaseNotification'])
            ->weekly()
            ->description('Nettoyer les anciennes notifications');

        // Backup de la base de données tous les jours à 2h du matin
        $schedule->command('backup:run')
            ->dailyAt('02:00')
            ->description('Sauvegarde automatique de la base de données');
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
