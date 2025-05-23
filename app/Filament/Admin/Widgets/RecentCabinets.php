<?php
// app/Filament/Admin/Widgets/RecentCabinets.php

namespace App\Filament\Admin\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Cabinet;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\ActionGroup;

class RecentCabinets extends BaseWidget
{
    protected static ?string $heading = 'Cabinets Récents';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Cabinet::query()
                    ->withCount(['companies', 'users'])
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('nom')
                    ->label('Nom du Cabinet')
                    ->weight(FontWeight::Bold)
                    ->searchable(),

                TextColumn::make('raison_sociale')
                    ->label('Raison Sociale')
                    ->toggleable(),

                TextColumn::make('ville')
                    ->label('Ville')
                    ->searchable(),

                TextColumn::make('statut')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'actif' => 'success',
                        'suspendu' => 'warning',
                        'inactif' => 'danger',
                    }),

                TextColumn::make('companies_count')
                    ->label('Sociétés')
                    ->alignCenter()
                    ->badge()
                    ->color('info'),

                TextColumn::make('users_count')
                    ->label('Utilisateurs')
                    ->alignCenter()
                    ->badge()
                    ->color('warning'),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\Action::make('view')
                        ->label('Voir')
                        ->icon('heroicon-m-eye')
                        ->url(fn ($record) => route('filament.admin.resources.cabinets.view', $record))
                        ->openUrlInNewTab(),
                    Tables\Actions\Action::make('edit')
                        ->label('Modifier')
                        ->icon('heroicon-m-pencil')
                        ->url(fn ($record) => route('filament.admin.resources.cabinets.edit', $record))
                        ->openUrlInNewTab(),
                ]),
            ]);
    }
}
