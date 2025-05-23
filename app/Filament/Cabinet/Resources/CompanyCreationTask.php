<?php
// app/Filament/Cabinet/Resources/CompanyCreationTaskResource.php

namespace App\Filament\Cabinet\Resources;

use App\Filament\Cabinet\Resources\CompanyCreationTaskResource\Pages;
use App\Models\CompanyCreationTask;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\KeyValue;
use Filament\Facades\Filament;

class CompanyCreationTaskResource extends Resource
{
    protected static ?string $model = CompanyCreationTask::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Création de Sociétés';
    protected static ?string $modelLabel = 'Tâche de Création';
    protected static ?string $pluralModelLabel = 'Tâches de Création';
    protected static ?string $navigationGroup = 'Gestion';
    protected static ?int $navigationSort = 3;
    protected static ?string $recordTitleAttribute = 'company_name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Informations Générales')
                        ->icon('heroicon-m-information-circle')
                        ->schema([
                            Grid::make(2)->schema([
                                TextInput::make('company_name')
                                    ->label('Nom de la Société')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Ex: Atlas Trading SARL'),

                                Select::make('company_type')
                                    ->label('Forme Juridique')
                                    ->options([
                                        'SARL' => 'SARL - Société à Responsabilité Limitée',
                                        'SA' => 'SA - Société Anonyme',
                                        'SAS' => 'SAS - Société par Actions Simplifiée',
                                        'SNC' => 'SNC - Société en Nom Collectif',
                                        'SCS' => 'SCS - Société en Commandite Simple',
                                        'SCA' => 'SCA - Société en Commandite par Actions',
                                        'SASU' => 'SASU - Société par Actions Simplifiée Unipersonnelle',
                                        'EI' => 'Entreprise Individuelle',
                                        'AUTO' => 'Auto-Entrepreneur'
                                    ])
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, $set) {
                                        // Définir le capital minimum selon le type
                                        if ($state === 'SA') {
                                            $set('capital_social', 300000);
                                        } elseif ($state === 'SARL') {
                                            $set('capital_social', 10000);
                                        }
                                    }),
                            ]),

                            Grid::make(3)->schema([
                                TextInput::make('client_name')
                                    ->label('Nom du Client')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('client_phone')
                                    ->label('Téléphone')
                                    ->tel()
                                    ->maxLength(20),

                                TextInput::make('client_email')
                                    ->label('Email')
                                    ->email()
                                    ->maxLength(255),
                            ]),

                            Grid::make(2)->schema([
                                Textarea::make('activity_description')
                                    ->label('Activité Principale')
                                    ->rows(3)
                                    ->maxLength(1000)
                                    ->placeholder('Décrivez l\'activité principale de la société'),

                                TextInput::make('capital_social')
                                    ->label('Capital Social')
                                    ->numeric()
                                    ->step(0.01)
                                    ->suffix('MAD')
                                    ->minValue(0)
                                    ->helperText(function ($get) {
                                        $type = $get('company_type');
                                        if ($type === 'SA') {
                                            return 'Minimum: 300,000 MAD (SA faisant appel public à l\'épargne: 3,000,000 MAD)';
                                        } elseif ($type === 'SARL') {
                                            return 'Minimum: 10,000 MAD (ou librement fixé)';
                                        }
                                        return null;
                                    }),
                            ]),
                        ]),

                    Wizard\Step::make('Adresse du Siège')
                        ->icon('heroicon-m-map-pin')
                        ->schema([
                            Select::make('domiciliation_type')
                                ->label('Type de Domiciliation')
                                ->options([
                                    'domiciliation' => 'Centre de Domiciliation',
                                    'bail' => 'Bail Commercial',
                                    'propriete' => 'Propriété'
                                ])
                                ->required()
                                ->reactive(),

                            TextInput::make('siege_address')
                                ->label('Adresse du Siège')
                                ->required()
                                ->columnSpanFull(),

                            TextInput::make('siege_city')
                                ->label('Ville')
                                ->required()
                                ->datalist([
                                    'Casablanca', 'Rabat', 'Marrakech', 'Fès', 'Tanger',
                                    'Agadir', 'Meknès', 'Oujda', 'Kenitra', 'Tétouan'
                                ]),
                        ]),

                    Wizard\Step::make('Planification')
                        ->icon('heroicon-m-calendar')
                        ->schema([
                            Grid::make(2)->schema([
                                Select::make('user_id')
                                    ->label('Responsable')
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->default(auth()->id()),

                                DatePicker::make('target_completion_date')
                                    ->label('Date Cible de Finalisation')
                                    ->native(false)
                                    ->minDate(now()->addDays(7))
                                    ->helperText('Délai moyen: 7-14 jours ouvrables'),
                            ]),

                            Grid::make(2)->schema([
                                Select::make('status')
                                    ->label('Statut')
                                    ->options([
                                        'draft' => 'Brouillon',
                                        'in_progress' => 'En cours',
                                        'waiting_client' => 'En attente client',
                                        'waiting_admin' => 'En attente administration',
                                        'completed' => 'Terminé',
                                        'cancelled' => 'Annulé'
                                    ])
                                    ->default('draft')
                                    ->required(),

                                Select::make('stage')
                                    ->label('Étape Actuelle')
                                    ->options([
                                        'initial_contact' => 'Contact Initial',
                                        'certificat_negatif' => 'Certificat Négatif',
                                        'statuts' => 'Rédaction des Statuts',
                                        'capital_deposit' => 'Dépôt du Capital',
                                        'enregistrement' => 'Enregistrement',
                                        'patente' => 'Taxe Professionnelle',
                                        'rc_immatriculation' => 'Immatriculation RC',
                                        'cnss_affiliation' => 'Affiliation CNSS',
                                        'publication' => 'Publication JAL/BO',
                                        'finalization' => 'Finalisation'
                                    ])
                                    ->default('initial_contact')
                                    ->required(),
                            ]),

                            MarkdownEditor::make('notes')
                                ->label('Notes')
                                ->columnSpanFull()
                                ->toolbarButtons([
                                    'bold',
                                    'italic',
                                    'bulletList',
                                    'orderedList',
                                    'link',
                                ]),
                        ]),
                ])
                    ->columnSpanFull()
                    ->skippable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company_name')
                    ->label('Société')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->limit(30),

                BadgeColumn::make('company_type')
                    ->label('Type'),

                TextColumn::make('client_name')
                    ->label('Client')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('user.name')
                    ->label('Responsable')
                    ->searchable()
                    ->toggleable(),

                BadgeColumn::make('stage')
                    ->label('Étape')
                    ->formatStateUsing(fn ($state): string => match($state) {
                        'initial_contact' => 'Contact',
                        'certificat_negatif' => 'Certificat Négatif',
                        'statuts' => 'Statuts',
                        'capital_deposit' => 'Capital',
                        'enregistrement' => 'Enregistrement',
                        'patente' => 'Patente',
                        'rc_immatriculation' => 'RC',
                        'cnss_affiliation' => 'CNSS',
                        'publication' => 'Publication',
                        'finalization' => 'Finalisation',
                        default => $state
                    })
                    ->colors([
                        'info' => 'initial_contact',
                        'blue' => 'certificat_negatif',
                        'indigo' => 'statuts',
                        'purple' => 'capital_deposit',
                        'pink' => 'enregistrement',
                        'red' => 'patente',
                        'orange' => 'rc_immatriculation',
                        'yellow' => 'cnss_affiliation',
                        'green' => 'publication',
                        'success' => 'finalization',
                    ]),

                BadgeColumn::make('status')
                    ->label('Statut')
                    ->formatStateUsing(fn ($state): string => match($state) {
                        'draft' => 'Brouillon',
                        'in_progress' => 'En cours',
                        'waiting_client' => 'Attente client',
                        'waiting_admin' => 'Attente admin',
                        'completed' => 'Terminé',
                        'cancelled' => 'Annulé',
                        default => $state
                    })
                    ->colors([
                        'gray' => 'draft',
                        'info' => 'in_progress',
                        'warning' => 'waiting_client',
                        'orange' => 'waiting_admin',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ]),

                TextColumn::make('progress_percentage')
                    ->label('Progression')
                    ->formatStateUsing(fn ($state): string => $state . '%')
                    ->badge()
                    ->color(fn ($state): string => match(true) {
                        $state < 25 => 'danger',
                        $state < 50 => 'warning',
                        $state < 75 => 'info',
                        $state < 100 => 'primary',
                        default => 'success'
                    }),

                TextColumn::make('target_completion_date')
                    ->label('Date Cible')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn ($record) => $record->isOverdue() ? 'danger' : null)
                    ->icon(fn ($record) => $record->isOverdue() ? 'heroicon-m-exclamation-triangle' : null),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'draft' => 'Brouillon',
                        'in_progress' => 'En cours',
                        'waiting_client' => 'En attente client',
                        'waiting_admin' => 'En attente administration',
                        'completed' => 'Terminé',
                        'cancelled' => 'Annulé'
                    ])
                    ->multiple(),

                SelectFilter::make('stage')
                    ->label('Étape')
                    ->options([
                        'initial_contact' => 'Contact Initial',
                        'certificat_negatif' => 'Certificat Négatif',
                        'statuts' => 'Rédaction des Statuts',
                        'capital_deposit' => 'Dépôt du Capital',
                        'enregistrement' => 'Enregistrement',
                        'patente' => 'Taxe Professionnelle',
                        'rc_immatriculation' => 'Immatriculation RC',
                        'cnss_affiliation' => 'Affiliation CNSS',
                        'publication' => 'Publication JAL/BO',
                        'finalization' => 'Finalisation'
                    ])
                    ->multiple(),

                SelectFilter::make('company_type')
                    ->label('Type de Société')
                    ->options([
                        'SARL' => 'SARL',
                        'SA' => 'SA',
                        'SAS' => 'SAS',
                        'SNC' => 'SNC',
                        'AUTO' => 'Auto-Entrepreneur'
                    ])
                    ->multiple(),

                Tables\Filters\Filter::make('overdue')
                    ->label('En retard')
                    ->query(fn (Builder $query): Builder => $query->overdue())
                    ->toggle(),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->color('info'),
                    Tables\Actions\EditAction::make()
                        ->color('warning'),
                    Tables\Actions\Action::make('advance_stage')
                        ->label('Étape Suivante')
                        ->icon('heroicon-m-arrow-right')
                        ->color('success')
                        ->action(function (CompanyCreationTask $record) {
                            if ($record->moveToNextStage()) {
                                Notification::make()
                                    ->title('Étape avancée')
                                    ->body('La tâche est passée à l\'étape: ' . $record->stage_name)
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Dernière étape atteinte')
                                    ->body('Cette tâche est déjà à la dernière étape.')
                                    ->warning()
                                    ->send();
                            }
                        })
                        ->visible(fn ($record) => $record->status === 'in_progress'),
                    Tables\Actions\DeleteAction::make()
                        ->color('danger'),
                ])
                    ->label('Actions')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size('sm')
                    ->color('gray')
                    ->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanyCreationTasks::route('/'),
            'create' => Pages\CreateCompanyCreationTask::route('/create'),
            'view' => Pages\ViewCompanyCreationTask::route('/{record}'),
            'edit' => Pages\EditCompanyCreationTask::route('/{record}/edit'),
            'kanban' => Pages\CompanyCreationTasksKanban::route('/kanban'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $cabinet = Filament::getTenant();
        return $cabinet ? static::getEloquentQuery()
            ->where('cabinet_id', $cabinet->id)
            ->where('status', 'in_progress')
            ->count() : '0';
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $cabinet = Filament::getTenant();

        if ($cabinet) {
            $query->where('cabinet_id', $cabinet->id);
        }

        return $query;
    }
}
