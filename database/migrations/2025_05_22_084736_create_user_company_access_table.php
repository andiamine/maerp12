<?php
// database/migrations/xxxx_create_user_company_access_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_company_access', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();

            $table->enum('role_company', [
                'admin',           // Administrateur de la société
                'comptable',       // Comptable de la société
                'assistant',       // Assistant comptable
                'consultation'     // Consultation uniquement
            ])->default('consultation');

            $table->json('permissions')->nullable(); // Permissions spécifiques par société
            $table->boolean('actif')->default(true);
            $table->date('date_debut_acces')->nullable();
            $table->date('date_fin_acces')->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'company_id']);
            $table->index(['company_id', 'actif']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_company_access');
    }
};
