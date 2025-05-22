<?php
// database/migrations/xxxx_create_companies_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cabinet_id')->constrained()->cascadeOnDelete();

            // Informations générales
            $table->string('raison_sociale');
            $table->string('nom_commercial')->nullable();
            $table->string('sigle')->nullable();
            $table->enum('forme_juridique', [
                'SA', 'SARL', 'SAS', 'SASU', 'SNC', 'SCS', 'SCA',
                'Auto-entrepreneur', 'Entreprise individuelle', 'Association'
            ]);

            // Identifiants officiels
            $table->string('registre_commerce')->nullable();
            $table->string('identifiant_fiscal')->unique();
            $table->string('ice')->unique(); // Identifiant Commun de l'Entreprise
            $table->string('patente')->nullable();
            $table->string('cnss')->nullable();

            // Capital et activité
            $table->decimal('capital_social', 15, 2)->nullable();
            $table->string('devise_capital', 3)->default('MAD');
            $table->string('activite_principale');
            $table->string('secteur_activite')->nullable();

            // Adresse du siège
            $table->string('adresse_siege');
            $table->string('ville_siege');
            $table->string('code_postal_siege')->nullable();
            $table->string('pays_siege')->default('Maroc');

            // Adresse d'exploitation (si différente)
            $table->string('adresse_exploitation')->nullable();
            $table->string('ville_exploitation')->nullable();
            $table->string('code_postal_exploitation')->nullable();

            // Contact
            $table->string('telephone')->nullable();
            $table->string('fax')->nullable();
            $table->string('email')->nullable();
            $table->string('site_web')->nullable();

            // Représentant légal
            $table->string('representant_nom');
            $table->string('representant_prenom');
            $table->string('representant_qualite'); // Gérant, PDG, etc.
            $table->string('representant_cin')->nullable();

            // Régimes fiscaux
            $table->enum('regime_tva', ['encaissement', 'debit', 'franchise', 'forfaitaire'])->default('debit');
            $table->enum('regime_is', ['normal', 'simplifie', 'forfaitaire'])->default('normal');
            $table->boolean('assujetti_taxe_professionnelle')->default(true);

            // Exercice comptable
            $table->date('debut_exercice');
            $table->date('fin_exercice');
            $table->integer('duree_exercice')->default(12); // en mois

            // Dates importantes
            $table->date('date_constitution');
            $table->date('date_debut_activite')->nullable();
            $table->date('date_immatriculation_rc')->nullable();

            // Statut
            $table->enum('statut', ['active', 'suspendue', 'en_liquidation', 'radiee'])->default('active');
            $table->date('date_radiation')->nullable();

            // Paramètres comptables
            $table->string('monnaie_tenue_compte', 3)->default('MAD');
            $table->json('parametres_comptables')->nullable();
            $table->text('observations')->nullable();

            $table->timestamps();

            $table->index(['cabinet_id', 'statut']);
            $table->index('identifiant_fiscal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
