<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Démarrage du seeding de la base de données...');

        $this->call([
            CabinetSeeder::class,
            CompanySeeder::class,
            UserSeeder::class,
            CompanyCreationTaskSeeder::class,
        ]);

        $this->command->info('🎉 Seeding terminé avec succès!');
        $this->command->line('');
        $this->command->info('📊 Données créées :');
        $this->command->line('   • 5 Cabinets comptables');
        $this->command->line('   • 6 Sociétés avec différents statuts');
        $this->command->line('   • 8 Utilisateurs avec différents rôles');
        $this->command->line('   • Relations entre utilisateurs et sociétés');
        $this->command->line('');
        $this->command->info('🔐 Accès administrateur :');
        $this->command->line('   Email: admin@comptabilite-maroc.ma');
        $this->command->line('   Mot de passe: password');
        $this->command->line('');
        $this->command->info('🏢 Accès aux panels :');
        $this->command->line('   Admin: /admin');
        $this->command->line('   Cabinet: /cabinet');
        $this->command->line('   Comptabilité: /comptabilite');
    }
}
