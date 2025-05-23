<?php
// app/Models/User.php - Updated with proper Filament tenancy

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

class User extends Authenticatable implements FilamentUser, HasTenants
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

    public function companyCreationTasks(): HasMany
    {
        return $this->hasMany(CompanyCreationTask::class);
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

    // Méthodes pour FilamentUser
    public function canAccessPanel(Panel $panel): bool
    {
        // Vérifier l'accès selon le panel
        if ($panel->getId() === 'admin') {
            return $this->role_global === 'super_admin';
        }

        if ($panel->getId() === 'cabinet') {
            return $this->cabinet_id !== null &&
                in_array($this->role_global, ['admin_cabinet', 'expert_comptable', 'comptable', 'assistant', 'client']) &&
                $this->statut === 'actif';
        }

        if ($panel->getId() === 'comptabilite') {
            return $this->cabinet_id !== null &&
                in_array($this->role_global, ['expert_comptable', 'comptable', 'assistant']) &&
                $this->statut === 'actif';
        }

        return false;
    }

    // Méthodes pour HasTenants (multi-tenancy dans le panel cabinet)
    public function getTenants(Panel $panel): Collection
    {
        // Pour le panel cabinet, retourner le cabinet de l'utilisateur
        if ($panel->getId() === 'cabinet' && $this->cabinet) {
            return collect([$this->cabinet]);
        }

        return collect();
    }

    public function canAccessTenant(Model $tenant): bool
    {
        // Vérifier que l'utilisateur peut accéder à ce cabinet
        return $this->cabinet_id === $tenant->id;
    }
}
