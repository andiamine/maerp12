<?php

namespace App\Filament\Cabinet\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\ActionGroup;
use App\Models\Company;
use Filament\Facades\Filament;

class CompaniesRelationManager extends RelationManager
{
    protected static string $relationship = 'companies';
    protected static ?string $title = 'Accès aux Sociétés';
    protected static ?string $recordTitleAttribute = 'raison_sociale';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Société')
                    ->schema([
                        Select::make('company_id')
                            ->label('Société')
                            ->options(function () {
                                $cabinet = Filament::getTenant();
                                return $cabinet->companies()
                                    ->get()
                                    ->mapWithKeys(function ($company) {
                                        return [$company->id => $company->raison_sociale . ' (' . $company->forme_juridique . ')'];
                                    });
                            })
                            ->searchable()
                            ->required(),
                    ]),

                Section::make('Accès à la Société')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('role_company')
                                ->label('Rôle dans la Société')
                                ->options([
                                    'admin' => 'Administrateur',
                                    'comptable' => 'Comptable',
                                    'assistant' => 'Assistant Comptable',
                                    'consultation' => 'Consultation uniquement'
                                ])
                                ->default('consultation')
                                ->required(),
                            Toggle::make('actif')
                                ->label('Accès Actif')
                                ->default(true),
                        ]),
                        Grid::make(2)->schema([
                            DatePicker::make('date_debut_acces')
                                ->label('Début d\'Accès')
                                ->native(false),
                            DatePicker::make('date_fin_acces')
                                ->label('Fin d\'Accès')
                                ->after('date_debut_acces')
                                ->native(false),
                        ]),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('company.raison_sociale')
            ->columns([
                TextColumn::make('company.raison_sociale')
                    ->label('Société')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),

                TextColumn::make('company.forme_juridique')
                    ->label('Forme')
                    ->badge()
                    ->toggleable(),

                TextColumn::make('role_company')
                    ->label('Rôle')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'admin' => 'Administrateur',
                        'comptable' => 'Comptable',
                        'assistant' => 'Assistant',
                        'consultation' => 'Consultation',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'comptable' => 'warning',
                        'assistant' => 'info',
                        'consultation' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('actif')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Actif' : 'Inactif')
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger'),

                TextColumn::make('date_debut_acces')
                    ->label('Début')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('date_fin_acces')
                    ->label('Fin')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable()
                    ->placeholder('Illimité'),

                TextColumn::make('company.statut')
                    ->label('Statut Société')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'suspendue' => 'warning',
                        'en_liquidation' => 'danger',
                        'radiee' => 'gray',
                    })
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('role_company')
                    ->label('Rôle')
                    ->options([
                        'admin' => 'Administrateur',
                        'comptable' => 'Comptable',
                        'assistant' => 'Assistant',
                        'consultation' => 'Consultation',
                    ]),
                SelectFilter::make('actif')
                    ->label('Statut Accès')
                    ->options([
                        true => 'Actif',
                        false => 'Inactif',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label('Donner Accès à une Société')
                    ->preloadRecordSelect()
                    ->recordSelectOptionsQuery(fn (Builder $query) => $query->whereBelongsTo(Filament::getTenant()))
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Select::make('role_company')
                            ->label('Rôle dans la Société')
                            ->options([
                                'admin' => 'Administrateur',
                                'comptable' => 'Comptable',
                                'assistant' => 'Assistant Comptable',
                                'consultation' => 'Consultation uniquement'
                            ])
                            ->default('consultation')
                            ->required(),
                        Toggle::make('actif')
                            ->label('Accès Actif')
                            ->default(true),
                        Grid::make(2)->schema([
                            DatePicker::make('date_debut_acces')
                                ->label('Début d\'Accès')
                                ->native(false),
                            DatePicker::make('date_fin_acces')
                                ->label('Fin d\'Accès')
                                ->native(false),
                        ]),
                    ]),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('toggle_access')
                        ->label(fn ($record) => $record->actif ? 'Désactiver' : 'Activer')
                        ->icon(fn ($record) => $record->actif ? 'heroicon-m-eye-slash' : 'heroicon-m-eye')
                        ->color(fn ($record) => $record->actif ? 'warning' : 'success')
                        ->action(fn ($record) => $record->update(['actif' => !$record->actif]))
                        ->requiresConfirmation(),
                    Tables\Actions\Action::make('view_company')
                        ->label('Voir la Société')
                        ->icon('heroicon-m-building-storefront')
                        ->color('info')
                        ->url(fn ($record) => route('filament.cabinet.resources.companies.view', $record->company))
                        ->openUrlInNewTab(),
                    Tables\Actions\DetachAction::make()
                        ->label('Supprimer Accès'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make()
                        ->label('Supprimer les accès'),
                ]),
            ])
            ->emptyStateHeading('Aucun accès à des sociétés')
            ->emptyStateDescription('Cet utilisateur n\'a encore accès à aucune société.')
            ->emptyStateIcon('heroicon-o-building-storefront');
    }
}
