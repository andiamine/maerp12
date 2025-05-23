<?php

namespace App\Filament\Cabinet\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Company;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\Enums\FontWeight;
use Filament\Facades\Filament;
use App\Filament\Cabinet\Resources\CompanyResource;

class RecentCompanies extends BaseWidget
{
    protected static ?string $heading = 'Sociétés Récentes';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $cabinet = Filament::getTenant();

        return $table
            ->query(
                $cabinet ? Company::query()
                    ->where('cabinet_id', $cabinet->id)
                    ->orderBy('created_at', 'desc')
                    ->limit(5) : Company::query()->whereRaw('1 = 0')
            )
            ->columns([
                TextColumn::make('raison_sociale')
                    ->label('Raison Sociale')
                    ->weight(FontWeight::Bold)
                    ->searchable(),

                TextColumn::make('forme_juridique')
                    ->label('Forme')
                    ->badge(),

                TextColumn::make('ville_siege')
                    ->label('Ville')
                    ->searchable(),

                TextColumn::make('statut')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'suspendue' => 'warning',
                        'en_liquidation' => 'danger',
                        'radiee' => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Créée le')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Voir')
                    ->icon('heroicon-m-eye')
                    ->url(fn (Company $record): string =>
                    CompanyResource::getUrl('view', ['record' => $record])
                    )
                    ->openUrlInNewTab(),
            ]);
    }
}
