<?php

namespace App\Filament\Cabinet\Resources\CompanyResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\ActionGroup;
use App\Models\User;
use Filament\Facades\Filament;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';
    protected static ?string $title = 'Accès Utilisateurs';
    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Utilisateur')
                    ->schema([
                        Select::make('user_id')
                            ->label('Utilisateur')
                            ->options(function () {
                                $cabinet = Filament::getTenant();
                                return $cabinet->users()
                                    ->get()
                                    ->mapWithKeys(function ($user) {
                                        return [$user->id => $user->nom_complet . ' (' . $user->email . ')'];
                                    });
                            })
                            ->searchable()
                            ->required()
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
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
            ->recordTitleAttribute('user.name')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Utilisateur')
                    ->formatStateUsing(fn ($record): string =>
                    $record->user->prenom ? "{$record->user->prenom} {$record->user->name}" : $record->user->name
                    )
                    ->searchable(['user.name', 'user.prenom'])
                    ->sortable()
                    ->weight(FontWeight::Bold),

                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
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
                    ->label('Statut')
                    ->options([
                        true => 'Actif',
                        false => 'Inactif',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label('Donner Accès')
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
            ->emptyStateHeading('Aucun accès utilisateur')
            ->emptyStateDescription('Cette société n\'a encore aucun utilisateur avec des droits d\'accès.')
            ->emptyStateIcon('heroicon-o-user-group');
    }
}
