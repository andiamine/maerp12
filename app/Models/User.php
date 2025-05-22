<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'cabinet_id',
        'prenom',
        'telephone',
        'role_global',
        'statut',
        'derniere_connexion',
        'permissions',
        'notes'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'derniere_connexion' => 'datetime',
        'permissions' => 'array'
    ];

    public function cabinet(): BelongsTo
    {
        return $this->belongsTo(Cabinet::class);
    }

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'user_company_access')
            ->withPivot(['role_company', 'permissions', 'actif', 'date_debut_acces', 'date_fin_acces'])
            ->withTimestamps();
    }

    public function getNomCompletAttribute(): string
    {
        return $this->prenom ? $this->prenom . ' ' . $this->name : $this->name;
    }

    public function isSuperAdmin(): bool
    {
        return $this->role_global === 'super_admin';
    }

    public function isAdminCabinet(): bool
    {
        return $this->role_global === 'admin_cabinet';
    }

    public function canAccessCabinet(Cabinet $cabinet): bool
    {
        return $this->isSuperAdmin() || $this->cabinet_id === $cabinet->id;
    }

    public function canAccessCompany(Company $company): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if ($this->cabinet_id === $company->cabinet_id) {
            return true;
        }

        return $this->companies()->where('companies.id', $company->id)
            ->wherePivot('actif', true)
            ->exists();
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
