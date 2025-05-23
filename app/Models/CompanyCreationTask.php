<?php
// app/Models/CompanyCreationTask.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyCreationTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'cabinet_id',
        'user_id',
        'company_name',
        'company_type',
        'client_name',
        'client_phone',
        'client_email',
        'activity_description',
        'capital_social',
        'status',
        'stage',
        'certificat_negatif_done',
        'certificat_negatif_date',
        'certificat_negatif_number',
        'statuts_done',
        'statuts_date',
        'capital_deposited',
        'capital_deposit_date',
        'bank_name',
        'enregistrement_done',
        'enregistrement_date',
        'patente_done',
        'patente_number',
        'identifiant_fiscal',
        'rc_done',
        'rc_number',
        'rc_date',
        'cnss_done',
        'cnss_number',
        'cnss_date',
        'publication_done',
        'publication_jal_date',
        'publication_bo_date',
        'siege_address',
        'siege_city',
        'domiciliation_type',
        'notes',
        'documents',
        'checklist',
        'target_completion_date',
        'actual_completion_date',
        'position',
        'metadata'
    ];

    protected $casts = [
        'capital_social' => 'decimal:2',
        'certificat_negatif_done' => 'boolean',
        'statuts_done' => 'boolean',
        'capital_deposited' => 'boolean',
        'enregistrement_done' => 'boolean',
        'patente_done' => 'boolean',
        'rc_done' => 'boolean',
        'cnss_done' => 'boolean',
        'publication_done' => 'boolean',
        'certificat_negatif_date' => 'date',
        'statuts_date' => 'date',
        'capital_deposit_date' => 'date',
        'enregistrement_date' => 'date',
        'rc_date' => 'date',
        'cnss_date' => 'date',
        'publication_jal_date' => 'date',
        'publication_bo_date' => 'date',
        'target_completion_date' => 'date',
        'actual_completion_date' => 'date',
        'documents' => 'array',
        'checklist' => 'array',
        'metadata' => 'array'
    ];

    public function cabinet(): BelongsTo
    {
        return $this->belongsTo(Cabinet::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getProgressPercentageAttribute(): int
    {
        $totalSteps = 10;
        $completedSteps = 0;

        $steps = [
            'certificat_negatif_done',
            'statuts_done',
            'capital_deposited',
            'enregistrement_done',
            'patente_done',
            'rc_done',
            'cnss_done',
            'publication_done'
        ];

        foreach ($steps as $step) {
            if ($this->$step) {
                $completedSteps++;
            }
        }

        return round(($completedSteps / count($steps)) * 100);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'gray',
            'in_progress' => 'info',
            'waiting_client' => 'warning',
            'waiting_admin' => 'orange',
            'completed' => 'success',
            'cancelled' => 'danger',
            default => 'gray'
        };
    }

    public function getStageColorAttribute(): string
    {
        return match($this->stage) {
            'initial_contact' => 'gray',
            'certificat_negatif' => 'blue',
            'statuts' => 'indigo',
            'capital_deposit' => 'purple',
            'enregistrement' => 'pink',
            'patente' => 'red',
            'rc_immatriculation' => 'orange',
            'cnss_affiliation' => 'yellow',
            'publication' => 'green',
            'finalization' => 'success',
            default => 'gray'
        };
    }

    public function getStageNameAttribute(): string
    {
        return match($this->stage) {
            'initial_contact' => 'Contact Initial',
            'certificat_negatif' => 'Certificat Négatif',
            'statuts' => 'Rédaction des Statuts',
            'capital_deposit' => 'Dépôt du Capital',
            'enregistrement' => 'Enregistrement',
            'patente' => 'Taxe Professionnelle',
            'rc_immatriculation' => 'Immatriculation RC',
            'cnss_affiliation' => 'Affiliation CNSS',
            'publication' => 'Publication JAL/BO',
            'finalization' => 'Finalisation',
            default => 'Inconnu'
        };
    }

    public function getCompanyTypeNameAttribute(): string
    {
        return match($this->company_type) {
            'SARL' => 'SARL - Société à Responsabilité Limitée',
            'SA' => 'SA - Société Anonyme',
            'SAS' => 'SAS - Société par Actions Simplifiée',
            'SNC' => 'SNC - Société en Nom Collectif',
            'SCS' => 'SCS - Société en Commandite Simple',
            'SCA' => 'SCA - Société en Commandite par Actions',
            'SASU' => 'SASU - Société par Actions Simplifiée Unipersonnelle',
            'EI' => 'Entreprise Individuelle',
            'AUTO' => 'Auto-Entrepreneur',
            default => $this->company_type
        };
    }

    public function scopeByStage($query, $stage)
    {
        return $query->where('stage', $stage);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOverdue($query)
    {
        return $query->whereNotNull('target_completion_date')
            ->where('target_completion_date', '<', now())
            ->whereNotIn('status', ['completed', 'cancelled']);
    }

    public function isOverdue(): bool
    {
        return $this->target_completion_date
            && $this->target_completion_date->isPast()
            && !in_array($this->status, ['completed', 'cancelled']);
    }

    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->target_completion_date || in_array($this->status, ['completed', 'cancelled'])) {
            return null;
        }

        return now()->diffInDays($this->target_completion_date, false);
    }

    public function getNextStageAttribute(): ?string
    {
        $stages = [
            'initial_contact' => 'certificat_negatif',
            'certificat_negatif' => 'statuts',
            'statuts' => 'capital_deposit',
            'capital_deposit' => 'enregistrement',
            'enregistrement' => 'patente',
            'patente' => 'rc_immatriculation',
            'rc_immatriculation' => 'cnss_affiliation',
            'cnss_affiliation' => 'publication',
            'publication' => 'finalization',
            'finalization' => null
        ];

        return $stages[$this->stage] ?? null;
    }

    public function moveToNextStage(): bool
    {
        $nextStage = $this->getNextStageAttribute();

        if ($nextStage) {
            $this->stage = $nextStage;
            $this->save();
            return true;
        }

        return false;
    }

    public function getRequiredDocumentsAttribute(): array
    {
        $documents = [];

        switch ($this->stage) {
            case 'certificat_negatif':
                $documents = [
                    'Formulaire de demande de certificat négatif',
                    'CIN ou passeport du demandeur',
                    'Procuration si représentant'
                ];
                break;
            case 'statuts':
                $documents = [
                    'Projet de statuts',
                    'CIN des associés',
                    'Certificat négatif'
                ];
                break;
            case 'capital_deposit':
                $documents = [
                    'Statuts signés',
                    'CIN des associés',
                    'Attestation de domiciliation'
                ];
                break;
            case 'enregistrement':
                $documents = [
                    'Statuts',
                    'Contrat de bail ou domiciliation',
                    'PV de nomination (si applicable)'
                ];
                break;
            case 'patente':
                $documents = [
                    'Demande d\'inscription',
                    'Contrat de bail ou domiciliation',
                    'CIN du gérant',
                    'Statuts',
                    'Agrément/diplôme (activités réglementées)'
                ];
                break;
            case 'rc_immatriculation':
                $documents = [
                    'Statuts enregistrés',
                    'Attestation de patente',
                    'Certificat négatif',
                    'Attestation de blocage du capital',
                    'Déclaration de conformité'
                ];
                break;
            case 'cnss_affiliation':
                $documents = [
                    'Demande d\'affiliation',
                    'Statuts',
                    'Registre de commerce',
                    'CIN du gérant'
                ];
                break;
            case 'publication':
                $documents = [
                    'Extrait du registre de commerce',
                    'Statuts',
                    'Annonce légale rédigée'
                ];
                break;
        }

        return $documents;
    }
}
