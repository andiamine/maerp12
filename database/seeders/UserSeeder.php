<?php
// database/seeders/UserSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Cabinet;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cabinets = Cabinet::all();
        $companies = Company::all();

        if ($cabinets->isEmpty()) {
            $this->command->error('❌ Aucun cabinet trouvé. Exécutez d\'abord CabinetSeeder.');
            return;
        }

        // Super Admin
        $superAdmin = User::create([
            'name' => 'ADMIN',
            'prenom' => 'Super',
            'email' => 'admin@comptabilite-maroc.ma',
            'password' => Hash::make('password'),
            'role_global' => 'super_admin',
            'statut' => 'actif',
            'telephone' => '+212522000000',
            'notes' => 'Administrateur principal de la plateforme',
            'email_verified_at' => now(),
        ]);

        // Utilisateurs pour le premier cabinet (Al-Mouhassaba)
        $cabinet1 = $cabinets->first();

        $adminCabinet1 = User::create([
            'name' => 'BENALI',
            'prenom' => 'Ahmed',
            'email' => 'ahmed.benali@almouhassaba.ma',
            'password' => Hash::make('password'),
            'cabinet_id' => $cabinet1->id,
            'role_global' => 'admin_cabinet',
            'statut' => 'actif',
            'telephone' => '+212522123456',
            'notes' => 'Expert-comptable responsable',
            'email_verified_at' => now(),
            'derniere_connexion' => Carbon::now()->subDays(1),
        ]);

        $comptable1 = User::create([
            'name' => 'ZEROUALI',
            'prenom' => 'Laila',
            'email' => 'laila.zerouali@almouhassaba.ma',
            'password' => Hash::make('password'),
            'cabinet_id' => $cabinet1->id,
            'role_global' => 'comptable',
            'statut' => 'actif',
            'telephone' => '+212522123457',
            'notes' => 'Comptable senior',
            'email_verified_at' => now(),
            'derniere_connexion' => Carbon::now()->subHours(3),
        ]);

        $assistant1 = User::create([
            'name' => 'HAKIMI',
            'prenom' => 'Youssef',
            'email' => 'youssef.hakimi@almouhassaba.ma',
            'password' => Hash::make('password'),
            'cabinet_id' => $cabinet1->id,
            'role_global' => 'assistant',
            'statut' => 'actif',
            'telephone' => '+212522123458',
            'notes' => 'Assistant comptable junior',
            'email_verified_at' => now(),
            'derniere_connexion' => Carbon::now()->subMinutes(30),
        ]);

        // Utilisateurs pour le deuxième cabinet (Expertise Comptable Rabat)
        $cabinet2 = $cabinets->skip(1)->first();

        $adminCabinet2 = User::create([
            'name' => 'ALAOUI',
            'prenom' => 'Fatima',
            'email' => 'fatima.alaoui@expertiserabat.ma',
            'password' => Hash::make('password'),
            'cabinet_id' => $cabinet2->id,
            'role_global' => 'expert_comptable',
            'statut' => 'actif',
            'telephone' => '+212537234567',
            'notes' => 'Expert-comptable spécialisée en fiscalité internationale',
            'email_verified_at' => now(),
            'derniere_connexion' => Carbon::now()->subDays(2),
        ]);

        $comptable2 = User::create([
            'name' => 'BERRADA',
            'prenom' => 'Karim',
            'email' => 'karim.berrada@expertiserabat.ma',
            'password' => Hash::make('password'),
            'cabinet_id' => $cabinet2->id,
            'role_global' => 'comptable',
            'statut' => 'actif',
            'telephone' => '+212537234568',
            'notes' => 'Comptable spécialisé en audit',
            'email_verified_at' => now(),
            'derniere_connexion' => Carbon::now()->subHours(5),
        ]);

        // Utilisateurs pour le troisième cabinet (Fiduciaire Marrakech)
        $cabinet3 = $cabinets->skip(2)->first();

        $adminCabinet3 = User::create([
            'name' => 'TAZI',
            'prenom' => 'Youssef',
            'email' => 'youssef.tazi@fiduciairemarrakech.ma',
            'password' => Hash::make('password'),
            'cabinet_id' => $cabinet3->id,
            'role_global' => 'admin_cabinet',
            'statut' => 'actif',
            'telephone' => '+212524345678',
            'notes' => 'Expert-comptable spécialisé secteur touristique',
            'email_verified_at' => now(),
            'derniere_connexion' => Carbon::now()->subHours(8),
        ]);

        // Quelques clients
        $client1 = User::create([
            'name' => 'BENALI',
            'prenom' => 'Hassan',
            'email' => 'hassan.benali@atlastrading.ma',
            'password' => Hash::make('password'),
            'role_global' => 'client',
            'statut' => 'actif',
            'telephone' => '+212522111111',
            'notes' => 'Gérant Atlas Trading',
            'email_verified_at' => now(),
        ]);

        $client2 = User::create([
            'name' => 'ALAMI',
            'prenom' => 'Khadija',
            'email' => 'khadija.alami@technosol.ma',
            'password' => Hash::make('password'),
            'role_global' => 'client',
            'statut' => 'actif',
            'telephone' => '+212522222222',
            'notes' => 'Directrice Générale Techno Solutions',
            'email_verified_at' => now(),
        ]);

        // Créer les accès aux sociétés
        if ($companies->count() >= 2) {
            // Donner accès au comptable1 aux deux premières sociétés du cabinet1
            $company1 = $companies->where('cabinet_id', $cabinet1->id)->first();
            $company2 = $companies->where('cabinet_id', $cabinet1->id)->skip(1)->first();

            if ($company1) {
                $comptable1->companies()->attach($company1->id, [
                    'role_company' => 'comptable',
                    'actif' => true,
                    'date_debut_acces' => now(),
                ]);
            }

            if ($company2) {
                $comptable1->companies()->attach($company2->id, [
                    'role_company' => 'assistant',
                    'actif' => true,
                    'date_debut_acces' => now(),
                ]);

                $assistant1->companies()->attach($company2->id, [
                    'role_company' => 'assistant',
                    'actif' => true,
                    'date_debut_acces' => now(),
                ]);
            }

            // Donner accès aux clients à leurs sociétés
            if ($company1) {
                $client1->companies()->attach($company1->id, [
                    'role_company' => 'admin',
                    'actif' => true,
                    'date_debut_acces' => now(),
                ]);
            }

            if ($company2) {
                $client2->companies()->attach($company2->id, [
                    'role_company' => 'admin',
                    'actif' => true,
                    'date_debut_acces' => now(),
                ]);
            }
        }

        $this->command->info('✅ Utilisateurs créés avec succès!');
        $this->command->info('🔑 Comptes de test créés :');
        $this->command->line('   Super Admin: admin@comptabilite-maroc.ma / password');
        $this->command->line('   Cabinet 1: ahmed.benali@almouhassaba.ma / password');
        $this->command->line('   Cabinet 2: fatima.alaoui@expertiserabat.ma / password');
        $this->command->line('   Cabinet 3: youssef.tazi@fiduciairemarrakech.ma / password');
    }
}
