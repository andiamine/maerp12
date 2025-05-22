<?php
// app/Models/Cabinet.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cabinet extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'raison_sociale',
        'forme_juridique',
        'registre_commerce',
        'identifiant_fiscal',
        'ice',
        'patente',
        'cnss',
        'adresse',
        'ville',
        'code_postal',
        'pays',
        'telephone',
        'fax',
        'email',
        'site_web',
        'expert_comptable_nom',
        'expert_comptable_numero',
        'expert_comptable_email',
        'statut',
        'limite_societes',
        'limite_utilisateurs',
        'date_creation',
        'date_expiration',
        'parametres',
        'notes'
    ];

    protected $casts = [
        'date_creation' => 'date',
        'date_expiration' => 'date',
        'parametres' => 'array'
    ];

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function isActive(): bool
    {
        return $this->statut === 'actif' &&
            ($this->date_expiration === null || $this->date_expiration->isFuture());
    }

    public function canAddCompany(): bool
    {
        return $this->companies()->count() < $this->limite_societes;
    }

    public function canAddUser(): bool
    {
        return $this->users()->count() < $this->limite_utilisateurs;
    }

    public function getStatutColorAttribute(): string
    {
        return match($this->statut) {
            'actif' => 'success',
            'suspendu' => 'warning',
            'inactif' => 'danger',
            default => 'gray'
        };
    }
}
