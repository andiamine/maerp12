<?php
// database/migrations/xxxx_add_cabinet_fields_to_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('cabinet_id')->nullable()->constrained()->nullOnDelete();
            $table->string('prenom')->nullable();
            $table->string('telephone')->nullable();
            $table->enum('role_global', [
                'super_admin',      // Administrateur plateforme
                'admin_cabinet',    // Administrateur de cabinet
                'expert_comptable', // Expert-comptable
                'comptable',        // Comptable confirmé
                'assistant',        // Assistant comptable
                'client'           // Client (consultation uniquement)
            ])->default('assistant');
            $table->enum('statut', ['actif', 'suspendu', 'inactif'])->default('actif');
            $table->timestamp('derniere_connexion')->nullable();
            $table->json('permissions')->nullable();
            $table->text('notes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['cabinet_id']);
            $table->dropColumn([
                'cabinet_id', 'prenom', 'telephone', 'role_global',
                'statut', 'derniere_connexion', 'permissions', 'notes'
            ]);
        });
    }
};
