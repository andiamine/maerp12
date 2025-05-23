<?php
// app/Models/Cabinet.php - Updated for Filament tenancy

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Filament\Models\Contracts\HasName;
use Filament\Models\Contracts\HasAvatar;

class Cabinet extends Model implements HasName, HasAvatar
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

    // Filament HasName implementation
    public function getFilamentName(): string
    {
        return $this->nom;
    }

    // Filament HasAvatar implementation
    public function getFilamentAvatarUrl(): ?string
    {
        // You can return a logo URL if you have one
        return null;
    }

    // Relationships
    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function companyCreationTasks(): HasMany
    {
        return $this->hasMany(CompanyCreationTask::class);
    }

    // Business logic methods
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

    public function getDaysUntilExpirationAttribute(): ?int
    {
        if (!$this->date_expiration) {
            return null;
        }

        return now()->diffInDays($this->date_expiration, false);
    }

    public function isExpired(): bool
    {
        return $this->date_expiration && $this->date_expiration->isPast();
    }

    public function isExpiringSoon(): bool
    {
        if (!$this->date_expiration) {
            return false;
        }

        $daysUntilExpiration = $this->days_until_expiration;
        return $daysUntilExpiration !== null && $daysUntilExpiration <= 30 && $daysUntilExpiration > 0;
    }
}
