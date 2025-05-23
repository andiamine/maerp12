<?php
// database/seeders/CompanySeeder.php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Cabinet;
use Carbon\Carbon;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cabinets = Cabinet::all();

        if ($cabinets->isEmpty()) {
            $this->command->error('❌ Aucun cabinet trouvé. Exécutez d\'abord CabinetSeeder.');
            return;
        }

        $companies = [
            [
                'cabinet_id' => $cabinets->first()->id,
                'raison_sociale' => 'Atlas Trading SARL',
                'nom_commercial' => 'Atlas Trading',
                'sigle' => 'ATS',
                'forme_juridique' => 'SARL',
                'registre_commerce' => 'RC12345',
                'identifiant_fiscal' => 'IF67890',
                'ice' => '001111111111111',
                'patente' => 'PT11111',
                'cnss' => 'CN11111',
                'capital_social' => 100000.00,
                'devise_capital' => 'MAD',
                'activite_principale' => 'Commerce de gros de produits alimentaires',
                'secteur_activite' => 'Commerce',
                'adresse_siege' => 'Zone Industrielle Sidi Bernoussi, Lot 150',
                'ville_siege' => 'Casablanca',
                'code_postal_siege' => '20600',
                'pays_siege' => 'Maroc',
                'telephone' => '+212522111111',
                'email' => 'contact@atlastrading.ma',
                'representant_nom' => 'BENALI',
                'representant_prenom' => 'Hassan',
                'representant_qualite' => 'Gérant',
                'representant_cin' => 'AB123456',
                'regime_tva' => 'debit',
                'regime_is' => 'normal',
                'assujetti_taxe_professionnelle' => true,
                'debut_exercice' => Carbon::create(2024, 1, 1),
                'fin_exercice' => Carbon::create(2024, 12, 31),
                'duree_exercice' => 12,
                'date_constitution' => Carbon::create(2020, 3, 15),
                'date_debut_activite' => Carbon::create(2020, 4, 1),
                'date_immatriculation_rc' => Carbon::create(2020, 3, 20),
                'statut' => 'active',
                'monnaie_tenue_compte' => 'MAD',
                'observations' => 'Société en croissance dans le secteur alimentaire',
            ],
            [
                'cabinet_id' => $cabinets->first()->id,
                'raison_sociale' => 'Techno Solutions SA',
                'nom_commercial' => 'TechnoSol',
                'sigle' => 'TS',
                'forme_juridique' => 'SA',
                'registre_commerce' => 'RC23456',
                'identifiant_fiscal' => 'IF78901',
                'ice' => '001111111111112',
                'patente' => 'PT22222',
                'cnss' => 'CN22222',
                'capital_social' => 500000.00,
                'devise_capital' => 'MAD',
                'activite_principale' => 'Développement de logiciels informatiques',
                'secteur_activite' => 'Technologie',
                'adresse_siege' => 'Technopark, Bât A, Bureau 205',
                'ville_siege' => 'Casablanca',
                'code_postal_siege' => '20100',
                'pays_siege' => 'Maroc',
                'telephone' => '+212522222222',
                'email' => 'info@technosol.ma',
                'site_web' => 'https://www.technosol.ma',
                'representant_nom' => 'ALAMI',
                'representant_prenom' => 'Khadija',
                'representant_qualite' => 'Directrice Générale',
                'representant_cin' => 'CD234567',
                'regime_tva' => 'debit',
                'regime_is' => 'normal',
                'assujetti_taxe_professionnelle' => true,
                'debut_exercice' => Carbon::create(2024, 1, 1),
                'fin_exercice' => Carbon::create(2024, 12, 31),
                'duree_exercice' => 12,
                'date_constitution' => Carbon::create(2019, 6, 10),
                'date_debut_activite' => Carbon::create(2019, 7, 1),
                'date_immatriculation_rc' => Carbon::create(2019, 6, 15),
                'statut' => 'active',
                'monnaie_tenue_compte' => 'MAD',
                'observations' => 'Spécialisée dans les solutions ERP',
            ],
            [
                'cabinet_id' => $cabinets->skip(1)->first()->id,
                'raison_sociale' => 'Riad Hospitality SARL',
                'nom_commercial' => 'Riad Atlas',
                'sigle' => 'RH',
                'forme_juridique' => 'SARL',
                'registre_commerce' => 'RC34567',
                'identifiant_fiscal' => 'IF89012',
                'ice' => '001111111111113',
                'patente' => 'PT33333',
                'cnss' => 'CN33333',
                'capital_social' => 250000.00,
                'devise_capital' => 'MAD',
                'activite_principale' => 'Hôtellerie et restauration',
                'secteur_activite' => 'Tourisme',
                'adresse_siege' => 'Médina, Derb El Ferrane, Riad 45',
                'ville_siege' => 'Marrakech',
                'code_postal_siege' => '40000',
                'pays_siege' => 'Maroc',
                'telephone' => '+212524333333',
                'email' => 'reservation@riadatlas.ma',
                'site_web' => 'https://www.riadatlas.ma',
                'representant_nom' => 'TAZI',
                'representant_prenom' => 'Mohamed',
                'representant_qualite' => 'Gérant',
                'representant_cin' => 'EF345678',
                'regime_tva' => 'debit',
                'regime_is' => 'normal',
                'assujetti_taxe_professionnelle' => true,
                'debut_exercice' => Carbon::create(2024, 1, 1),
                'fin_exercice' => Carbon::create(2024, 12, 31),
                'duree_exercice' => 12,
                'date_constitution' => Carbon::create(2021, 2, 28),
                'date_debut_activite' => Carbon::create(2021, 5, 1),
                'date_immatriculation_rc' => Carbon::create(2021, 3, 5),
                'statut' => 'active',
                'monnaie_tenue_compte' => 'MAD',
                'observations' => 'Riad traditionnel avec 12 chambres',
            ],
            [
                'cabinet_id' => $cabinets->skip(1)->first()->id,
                'raison_sociale' => 'Green Energy Maroc SAS',
                'nom_commercial' => 'GEM',
                'sigle' => 'GEM',
                'forme_juridique' => 'SAS',
                'registre_commerce' => 'RC45678',
                'identifiant_fiscal' => 'IF90123',
                'ice' => '001111111111114',
                'patente' => 'PT44444',
                'cnss' => 'CN44444',
                'capital_social' => 1000000.00,
                'devise_capital' => 'MAD',
                'activite_principale' => 'Production d\'énergie solaire',
                'secteur_activite' => 'Énergie',
                'adresse_siege' => 'Technopolis Rabat, Bât Innovation',
                'ville_siege' => 'Rabat',
                'code_postal_siege' => '10100',
                'pays_siege' => 'Maroc',
                'telephone' => '+212537444444',
                'email' => 'contact@greenenergymaroc.ma',
                'site_web' => 'https://www.greenenergymaroc.ma',
                'representant_nom' => 'BENJELLOUN',
                'representant_prenom' => 'Amal',
                'representant_qualite' => 'Présidente',
                'representant_cin' => 'GH456789',
                'regime_tva' => 'debit',
                'regime_is' => 'normal',
                'assujetti_taxe_professionnelle' => true,
                'debut_exercice' => Carbon::create(2024, 1, 1),
                'fin_exercice' => Carbon::create(2024, 12, 31),
                'duree_exercice' => 12,
                'date_constitution' => Carbon::create(2022, 9, 15),
                'date_debut_activite' => Carbon::create(2022, 11, 1),
                'date_immatriculation_rc' => Carbon::create(2022, 9, 20),
                'statut' => 'active',
                'monnaie_tenue_compte' => 'MAD',
                'observations' => 'Entreprise spécialisée dans les énergies renouvelables',
            ],
            [
                'cabinet_id' => $cabinets->skip(2)->first()->id,
                'raison_sociale' => 'Artisanat Fès SARL',
                'nom_commercial' => 'Art Fès',
                'sigle' => 'AF',
                'forme_juridique' => 'SARL',
                'registre_commerce' => 'RC56789',
                'identifiant_fiscal' => 'IF01234',
                'ice' => '001111111111115',
                'patente' => 'PT55555',
                'cnss' => 'CN55555',
                'capital_social' => 50000.00,
                'devise_capital' => 'MAD',
                'activite_principale' => 'Fabrication et vente d\'artisanat traditionnel',
                'secteur_activite' => 'Artisanat',
                'adresse_siege' => 'Médina de Fès, Talaa Kbira, Atelier 12',
                'ville_siege' => 'Fès',
                'code_postal_siege' => '30000',
                'pays_siege' => 'Maroc',
                'telephone' => '+212535555555',
                'email' => 'contact@artfes.ma',
                'representant_nom' => 'FASSI',
                'representant_prenom' => 'Khalid',
                'representant_qualite' => 'Gérant',
                'representant_cin' => 'IJ567890',
                'regime_tva' => 'franchise',
                'regime_is' => 'forfaitaire',
                'assujetti_taxe_professionnelle' => false,
                'debut_exercice' => Carbon::create(2024, 1, 1),
                'fin_exercice' => Carbon::create(2024, 12, 31),
                'duree_exercice' => 12,
                'date_constitution' => Carbon::create(2023, 1, 10),
                'date_debut_activite' => Carbon::create(2023, 2, 1),
                'date_immatriculation_rc' => Carbon::create(2023, 1, 15),
                'statut' => 'active',
                'monnaie_tenue_compte' => 'MAD',
                'observations' => 'Spécialisée dans la poterie et maroquinerie traditionnelle',
            ],
            [
                'cabinet_id' => $cabinets->skip(3)->first()->id,
                'raison_sociale' => 'Transport Express Maroc SA',
                'nom_commercial' => 'Express Maroc',
                'sigle' => 'TEM',
                'forme_juridique' => 'SA',
                'registre_commerce' => 'RC67890',
                'identifiant_fiscal' => 'IF12345',
                'ice' => '001111111111116',
                'patente' => 'PT66666',
                'cnss' => 'CN66666',
                'capital_social' => 800000.00,
                'devise_capital' => 'MAD',
                'activite_principale' => 'Transport de marchandises',
                'secteur_activite' => 'Transport',
                'adresse_siege' => 'Zone Logistique Tanger, Entrepôt 25',
                'ville_siege' => 'Tanger',
                'code_postal_siege' => '90000',
                'pays_siege' => 'Maroc',
                'telephone' => '+212539666666',
                'email' => 'contact@expressmaroc.ma',
                'site_web' => 'https://www.expressmaroc.ma',
                'representant_nom' => 'CHERKAOUI',
                'representant_prenom' => 'Omar',
                'representant_qualite' => 'Directeur Général',
                'representant_cin' => 'KL678901',
                'regime_tva' => 'debit',
                'regime_is' => 'normal',
                'assujetti_taxe_professionnelle' => true,
                'debut_exercice' => Carbon::create(2024, 1, 1),
                'fin_exercice' => Carbon::create(2024, 12, 31),
                'duree_exercice' => 12,
                'date_constitution' => Carbon::create(2018, 11, 30),
                'date_debut_activite' => Carbon::create(2019, 1, 1),
                'date_immatriculation_rc' => Carbon::create(2018, 12, 5),
                'statut' => 'suspendue',
                'monnaie_tenue_compte' => 'MAD',
                'observations' => 'Société temporairement suspendue pour restructuration',
            ],
        ];

        foreach ($companies as $companyData) {
            Company::create($companyData);
        }

        $this->command->info('✅ Sociétés créées avec succès!');
    }
}
