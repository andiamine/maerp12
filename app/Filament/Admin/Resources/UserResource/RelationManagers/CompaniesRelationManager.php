<?php
// app/Filament/Admin/Resources/UserResource/RelationManagers/CompaniesRelationManager.php

namespace App\Filament\Admin\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\ActionGroup;
use App\Models\Company;

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
                            ->relationship('company', 'raison_sociale')
                            ->getOptionLabelFromRecordUsing(fn (Company $record): string =>
                            "{$record->raison_sociale} ({$record->cabinet->nom})"
                            )
                            ->searchable(['raison_sociale', 'cabinet.nom'])
                            ->preload()
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
                                ->label('Début d\'Accès'),
                            DatePicker::make('date_fin_acces')
                                ->label('Fin d\'Accès')
                                ->after('date_debut_acces'),
                        ]),
                    ]),

                Section::make('Permissions Spécifiques')
                    ->description('Permissions spécifiques pour cette société')
                    ->schema([
                        Textarea::make('permissions_description')
                            ->label('Description des Permissions')
                            ->placeholder('Décrivez les permissions spécifiques accordées...')
                            ->rows(3)
                            ->columnSpanFull(),
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

                TextColumn::make('company.cabinet.nom')
                    ->label('Cabinet')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

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
                SelectFilter::make('company.cabinet_id')
                    ->label('Cabinet')
                    ->relationship('company.cabinet', 'nom'),
                SelectFilter::make('company.statut')
                    ->label('Statut Société')
                    ->options([
                        'active' => 'Active',
                        'suspendue' => 'Suspendue',
                        'en_liquidation' => 'En Liquidation',
                        'radiee' => 'Radiée'
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Donner Accès à une Société'),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('toggle_access')
                        ->label(fn ($record) => $record->actif ? 'Désactiver' : 'Activer')
                        ->icon(fn ($record) => $record->actif ? 'heroicon-m-eye-slash' : 'heroicon-m-eye')
                        ->color(fn ($record) => $record->actif ? 'warning' : 'success')
                        ->action(fn ($record) => $record->update(['actif' => !$record->actif]))
                        ->requiresConfirmation()
                        ->modalHeading(fn ($record) => $record->actif ? 'Désactiver l\'accès' : 'Activer l\'accès')
                        ->modalDescription(fn ($record) => $record->actif
                            ? 'Êtes-vous sûr de vouloir désactiver l\'accès à cette société ?'
                            : 'Êtes-vous sûr de vouloir activer l\'accès à cette société ?'
                        ),
                    Tables\Actions\Action::make('view_company')
                        ->label('Voir la Société')
                        ->icon('heroicon-m-building-storefront')
                        ->color('info')
                        ->url(fn ($record) => route('filament.admin.resources.companies.view', $record->company))
                        ->openUrlInNewTab(),
                    Tables\Actions\DeleteAction::make()
                        ->label('Supprimer Accès'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activer les accès')
                        ->icon('heroicon-m-eye')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['actif' => true]))
                        ->requiresConfirmation(),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Désactiver les accès')
                        ->icon('heroicon-m-eye-slash')
                        ->color('warning')
                        ->action(fn ($records) => $records->each->update(['actif' => false]))
                        ->requiresConfirmation(),
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Supprimer les accès'),
                ]),
            ])
            ->emptyStateHeading('Aucun accès à des sociétés')
            ->emptyStateDescription('Cet utilisateur n\'a encore accès à aucune société.')
            ->emptyStateIcon('heroicon-o-building-storefront');
    }
}
