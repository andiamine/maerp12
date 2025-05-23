<?php
// app/Models/Company.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Traits\BelongsToCabinet;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'cabinet_id',
        'raison_sociale',
        'nom_commercial',
        'sigle',
        'forme_juridique',
        'registre_commerce',
        'identifiant_fiscal',
        'ice',
        'patente',
        'cnss',
        'capital_social',
        'devise_capital',
        'activite_principale',
        'secteur_activite',
        'adresse_siege',
        'ville_siege',
        'code_postal_siege',
        'pays_siege',
        'adresse_exploitation',
        'ville_exploitation',
        'code_postal_exploitation',
        'telephone',
        'fax',
        'email',
        'site_web',
        'representant_nom',
        'representant_prenom',
        'representant_qualite',
        'representant_cin',
        'regime_tva',
        'regime_is',
        'assujetti_taxe_professionnelle',
        'debut_exercice',
        'fin_exercice',
        'duree_exercice',
        'date_constitution',
        'date_debut_activite',
        'date_immatriculation_rc',
        'statut',
        'date_radiation',
        'monnaie_tenue_compte',
        'parametres_comptables',
        'observations'
    ];

    protected $casts = [
        'capital_social' => 'decimal:2',
        'assujetti_taxe_professionnelle' => 'boolean',
        'debut_exercice' => 'date',
        'fin_exercice' => 'date',
        'date_constitution' => 'date',
        'date_debut_activite' => 'date',
        'date_immatriculation_rc' => 'date',
        'date_radiation' => 'date',
        'parametres_comptables' => 'array'
    ];

    public function cabinet(): BelongsTo
    {
        return $this->belongsTo(Cabinet::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_company_access')
            ->withPivot(['role_company', 'permissions', 'actif', 'date_debut_acces', 'date_fin_acces'])
            ->withTimestamps();
    }

    public function getRepresentantCompletAttribute(): string
    {
        return $this->representant_prenom . ' ' . $this->representant_nom;
    }

    public function getAdresseCompleteAttribute(): string
    {
        return $this->adresse_siege . ', ' . $this->ville_siege .
            ($this->code_postal_siege ? ' ' . $this->code_postal_siege : '') .
            ', ' . $this->pays_siege;
    }

    public function isActive(): bool
    {
        return $this->statut === 'active';
    }

    public function getStatutColorAttribute(): string
    {
        return match($this->statut) {
            'active' => 'success',
            'suspendue' => 'warning',
            'en_liquidation' => 'danger',
            'radiee' => 'gray',
            default => 'gray'
        };
    }

    public function getTauxTvaDefautAttribute(): float
    {
        return match($this->regime_tva) {
            'franchise' => 0,
            'forfaitaire' => 10,
            default => 20
        };
    }
}
