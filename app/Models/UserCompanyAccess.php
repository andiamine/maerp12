<?php
// app/Models/UserCompanyAccess.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserCompanyAccess extends Model
{
    use HasFactory;

    protected $table = 'user_company_access';

    protected $fillable = [
        'user_id',
        'company_id',
        'role_company',
        'permissions',
        'actif',
        'date_debut_acces',
        'date_fin_acces'
    ];

    protected $casts = [
        'permissions' => 'array',
        'actif' => 'boolean',
        'date_debut_acces' => 'date',
        'date_fin_acces' => 'date'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function isActive(): bool
    {
        return $this->actif &&
            ($this->date_fin_acces === null || $this->date_fin_acces->isFuture());
    }

    public function getRoleColorAttribute(): string
    {
        return match($this->role_company) {
            'admin' => 'danger',
            'comptable' => 'warning',
            'assistant' => 'info',
            'consultation' => 'gray',
            default => 'gray'
        };
    }
}
