<?php
// database/migrations/xxxx_create_cabinets_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cabinets', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('raison_sociale');
            $table->string('forme_juridique')->nullable();
            $table->string('registre_commerce')->nullable();
            $table->string('identifiant_fiscal')->nullable();
            $table->string('ice')->nullable(); // Identifiant Commun de l'Entreprise
            $table->string('patente')->nullable();
            $table->string('cnss')->nullable();

            // Adresse
            $table->string('adresse');
            $table->string('ville');
            $table->string('code_postal')->nullable();
            $table->string('pays')->default('Maroc');

            // Contact
            $table->string('telephone')->nullable();
            $table->string('fax')->nullable();
            $table->string('email')->nullable();
            $table->string('site_web')->nullable();

            // Expert-comptable responsable
            $table->string('expert_comptable_nom')->nullable();
            $table->string('expert_comptable_numero')->nullable();
            $table->string('expert_comptable_email')->nullable();

            // Statut et limites
            $table->enum('statut', ['actif', 'suspendu', 'inactif'])->default('actif');
            $table->integer('limite_societes')->default(10);
            $table->integer('limite_utilisateurs')->default(5);
            $table->date('date_creation');
            $table->date('date_expiration')->nullable();

            // Paramètres
            $table->json('parametres')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['statut', 'date_expiration']);
            $table->unique('ice');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cabinets');
    }
};
