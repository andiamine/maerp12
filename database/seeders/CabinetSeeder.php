<?php
// database/seeders/CabinetSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cabinet;
use Carbon\Carbon;

class CabinetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cabinets = [
            [
                'nom' => 'Cabinet Comptable Al-Mouhassaba',
                'raison_sociale' => 'Al-Mouhassaba SARL',
                'forme_juridique' => 'SARL',
                'registre_commerce' => 'RC123456',
                'identifiant_fiscal' => 'IF789012',
                'ice' => '001234567890123',
                'patente' => 'P654321',
                'cnss' => 'C987654',
                'adresse' => 'Avenue Mohammed V, Immeuble Al-Baraka, 3ème étage',
                'ville' => 'Casablanca',
                'code_postal' => '20000',
                'pays' => 'Maroc',
                'telephone' => '+212522123456',
                'fax' => '+212522123457',
                'email' => 'contact@almouhassaba.ma',
                'site_web' => 'https://www.almouhassaba.ma',
                'expert_comptable_nom' => 'Ahmed BENALI',
                'expert_comptable_numero' => 'EC001',
                'expert_comptable_email' => 'ahmed.benali@almouhassaba.ma',
                'statut' => 'actif',
                'limite_societes' => 50,
                'limite_utilisateurs' => 15,
                'date_creation' => Carbon::now()->subMonths(6),
                'date_expiration' => Carbon::now()->addYear(),
                'notes' => 'Cabinet spécialisé dans la comptabilité des PME',
            ],
            [
                'nom' => 'Expertise Comptable Rabat',
                'raison_sociale' => 'Expertise Comptable Rabat SA',
                'forme_juridique' => 'SA',
                'registre_commerce' => 'RC234567',
                'identifiant_fiscal' => 'IF890123',
                'ice' => '001234567890124',
                'patente' => 'P765432',
                'cnss' => 'C876543',
                'adresse' => 'Quartier Agdal, Rue Al-Khawarizmi, Résidence Atlas',
                'ville' => 'Rabat',
                'code_postal' => '10000',
                'pays' => 'Maroc',
                'telephone' => '+212537234567',
                'fax' => '+212537234568',
                'email' => 'info@expertiserabat.ma',
                'site_web' => 'https://www.expertiserabat.ma',
                'expert_comptable_nom' => 'Fatima ALAOUI',
                'expert_comptable_numero' => 'EC002',
                'expert_comptable_email' => 'fatima.alaoui@expertiserabat.ma',
                'statut' => 'actif',
                'limite_societes' => 30,
                'limite_utilisateurs' => 10,
                'date_creation' => Carbon::now()->subMonths(12),
                'date_expiration' => Carbon::now()->addMonths(6),
                'notes' => 'Cabinet avec expertise en fiscalité internationale',
            ],
            [
                'nom' => 'Fiduciaire Marrakech',
                'raison_sociale' => 'Fiduciaire Marrakech SARL',
                'forme_juridique' => 'SARL',
                'registre_commerce' => 'RC345678',
                'identifiant_fiscal' => 'IF901234',
                'ice' => '001234567890125',
                'patente' => 'P876543',
                'cnss' => 'C765432',
                'adresse' => 'Avenue Mohammed VI, Quartier Gueliz',
                'ville' => 'Marrakech',
                'code_postal' => '40000',
                'pays' => 'Maroc',
                'telephone' => '+212524345678',
                'email' => 'contact@fiduciairemarrakech.ma',
                'expert_comptable_nom' => 'Youssef TAZI',
                'expert_comptable_numero' => 'EC003',
                'expert_comptable_email' => 'youssef.tazi@fiduciairemarrakech.ma',
                'statut' => 'actif',
                'limite_societes' => 25,
                'limite_utilisateurs' => 8,
                'date_creation' => Carbon::now()->subMonths(3),
                'date_expiration' => Carbon::now()->addYear(2),
                'notes' => 'Spécialisé dans le secteur touristique',
            ],
            [
                'nom' => 'Cabinet Fès Expertise',
                'raison_sociale' => 'Fès Expertise SARL',
                'forme_juridique' => 'SARL',
                'registre_commerce' => 'RC456789',
                'identifiant_fiscal' => 'IF012345',
                'ice' => '001234567890126',
                'patente' => 'P987654',
                'cnss' => 'C654321',
                'adresse' => 'Nouvelle Ville, Boulevard Moulay Youssef',
                'ville' => 'Fès',
                'code_postal' => '30000',
                'pays' => 'Maroc',
                'telephone' => '+212535456789',
                'email' => 'contact@fesexpertise.ma',
                'expert_comptable_nom' => 'Rachid FASSI',
                'expert_comptable_numero' => 'EC004',
                'expert_comptable_email' => 'rachid.fassi@fesexpertise.ma',
                'statut' => 'suspendu',
                'limite_societes' => 20,
                'limite_utilisateurs' => 6,
                'date_creation' => Carbon::now()->subMonths(8),
                'date_expiration' => Carbon::now()->subDays(15),
                'notes' => 'Cabinet temporairement suspendu pour renouvellement',
            ],
            [
                'nom' => 'Tanger Audit & Conseil',
                'raison_sociale' => 'Tanger Audit & Conseil SAS',
                'forme_juridique' => 'SAS',
                'registre_commerce' => 'RC567890',
                'identifiant_fiscal' => 'IF123456',
                'ice' => '001234567890127',
                'patente' => 'P098765',
                'cnss' => 'C543210',
                'adresse' => 'Zone Franche, Complexe Hassan II',
                'ville' => 'Tanger',
                'code_postal' => '90000',
                'pays' => 'Maroc',
                'telephone' => '+212539567890',
                'email' => 'info@tangeraudit.ma',
                'site_web' => 'https://www.tangeraudit.ma',
                'expert_comptable_nom' => 'Aicha SENHAJI',
                'expert_comptable_numero' => 'EC005',
                'expert_comptable_email' => 'aicha.senhaji@tangeraudit.ma',
                'statut' => 'actif',
                'limite_societes' => 40,
                'limite_utilisateurs' => 12,
                'date_creation' => Carbon::now()->subMonths(18),
                'date_expiration' => Carbon::now()->addMonths(3),
                'notes' => 'Expertise en commerce international et zones franches',
            ],
        ];

        foreach ($cabinets as $cabinetData) {
            Cabinet::create($cabinetData);
        }

        $this->command->info('✅ Cabinets créés avec succès!');
    }
}
