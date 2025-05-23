<?php
// app/Filament/Admin/Widgets/LatestActivity.php

namespace App\Filament\Admin\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\User;
use App\Models\Cabinet;
use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\Enums\FontWeight;

class LatestActivity extends BaseWidget
{
    protected static ?string $heading = 'Activité Récente';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->where('created_at', '>=', now()->subDays(7))
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('nom_complet')
                    ->label('Utilisateur')
                    ->getStateUsing(fn ($record) => $record->prenom ? "{$record->prenom} {$record->name}" : $record->name)
                    ->weight(FontWeight::Bold),

                TextColumn::make('email')
                    ->label('Email'),

                TextColumn::make('cabinet.nom')
                    ->label('Cabinet')
                    ->default('Aucun'),

                TextColumn::make('role_global')
                    ->label('Rôle')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'super_admin' => 'Super Admin',
                        'admin_cabinet' => 'Admin Cabinet',
                        'expert_comptable' => 'Expert-Comptable',
                        'comptable' => 'Comptable',
                        'assistant' => 'Assistant',
                        'client' => 'Client',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'super_admin' => 'danger',
                        'admin_cabinet' => 'warning',
                        'expert_comptable' => 'success',
                        'comptable' => 'info',
                        'assistant' => 'gray',
                        'client' => 'secondary',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Voir')
                    ->icon('heroicon-m-eye')
                    ->url(fn ($record) => route('filament.admin.resources.users.view', $record))
                    ->openUrlInNewTab(),
            ]);
    }
}
