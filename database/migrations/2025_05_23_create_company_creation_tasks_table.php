<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_creation_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cabinet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // Responsable

            // Informations de la société à créer
            $table->string('company_name');
            $table->string('company_type'); // SARL, SA, SAS, etc.
            $table->string('client_name'); // Nom du client demandeur
            $table->string('client_phone')->nullable();
            $table->string('client_email')->nullable();
            $table->text('activity_description')->nullable();
            $table->decimal('capital_social', 15, 2)->nullable();

            // État d'avancement
            $table->enum('status', [
                'draft',           // Brouillon
                'in_progress',     // En cours
                'waiting_client',  // En attente client
                'waiting_admin',   // En attente administration
                'completed',       // Terminé
                'cancelled'        // Annulé
            ])->default('draft');

            $table->enum('stage', [
                'initial_contact',      // Contact initial
                'certificat_negatif',   // Certificat négatif
                'statuts',             // Rédaction des statuts
                'capital_deposit',      // Dépôt du capital
                'enregistrement',      // Enregistrement
                'patente',             // Taxe professionnelle
                'rc_immatriculation',  // Immatriculation RC
                'cnss_affiliation',    // Affiliation CNSS
                'publication',         // Publication JAL/BO
                'finalization'         // Finalisation
            ])->default('initial_contact');

            // Documents et validations
            $table->boolean('certificat_negatif_done')->default(false);
            $table->date('certificat_negatif_date')->nullable();
            $table->string('certificat_negatif_number')->nullable();

            $table->boolean('statuts_done')->default(false);
            $table->date('statuts_date')->nullable();

            $table->boolean('capital_deposited')->default(false);
            $table->date('capital_deposit_date')->nullable();
            $table->string('bank_name')->nullable();

            $table->boolean('enregistrement_done')->default(false);
            $table->date('enregistrement_date')->nullable();

            $table->boolean('patente_done')->default(false);
            $table->string('patente_number')->nullable();
            $table->string('identifiant_fiscal')->nullable();

            $table->boolean('rc_done')->default(false);
            $table->string('rc_number')->nullable();
            $table->date('rc_date')->nullable();

            $table->boolean('cnss_done')->default(false);
            $table->string('cnss_number')->nullable();
            $table->date('cnss_date')->nullable();

            $table->boolean('publication_done')->default(false);
            $table->date('publication_jal_date')->nullable();
            $table->date('publication_bo_date')->nullable();

            // Adresse du siège
            $table->string('siege_address')->nullable();
            $table->string('siege_city')->nullable();
            $table->string('domiciliation_type')->nullable(); // domiciliation, bail, propriété

            // Notes et commentaires
            $table->text('notes')->nullable();
            $table->json('documents')->nullable(); // Liste des documents attachés
            $table->json('checklist')->nullable(); // Checklist personnalisée

            // Dates importantes
            $table->date('target_completion_date')->nullable();
            $table->date('actual_completion_date')->nullable();

            // Métadonnées
            $table->integer('position')->default(0); // Pour l'ordre dans le Kanban
            $table->json('metadata')->nullable(); // Données supplémentaires

            $table->timestamps();

            $table->index(['cabinet_id', 'status']);
            $table->index(['cabinet_id', 'stage']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_creation_tasks');
    }
};
