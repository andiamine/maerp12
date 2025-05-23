<?php

namespace App\Filament\Cabinet\Resources;

use App\Filament\Cabinet\Resources\UserResource\Pages;
use App\Filament\Cabinet\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\ActionGroup;
use Filament\Notifications\Notification;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Utilisateurs';
    protected static ?string $modelLabel = 'Utilisateur';
    protected static ?string $pluralModelLabel = 'Utilisateurs';
    protected static ?string $navigationGroup = 'Utilisateurs';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informations Personnelles')
                    ->description('Informations de base de l\'utilisateur')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Nom')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('prenom')
                                ->label('Prénom')
                                ->maxLength(255),
                        ]),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('telephone')
                            ->label('Téléphone')
                            ->tel(),
                    ]),

                Section::make('Accès et Rôle')
                    ->description('Paramètres d\'accès')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('password')
                                ->label('Mot de passe')
                                ->password()
                                ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                                ->dehydrated(fn ($state) => filled($state))
                                ->required(fn (string $context): bool => $context === 'create')
                                ->maxLength(255),
                            Select::make('role_global')
                                ->label('Rôle')
                                ->options(function () {
                                    $currentUserRole = auth()->user()->role_global;

                                    // Les admins cabinet peuvent créer tous les rôles sauf super_admin
                                    if ($currentUserRole === 'admin_cabinet') {
                                        return [
                                            'admin_cabinet' => 'Admin Cabinet',
                                            'expert_comptable' => 'Expert-Comptable',
                                            'comptable' => 'Comptable',
                                            'assistant' => 'Assistant',
                                            'client' => 'Client',
                                        ];
                                    }

                                    // Les experts-comptables peuvent créer des rôles inférieurs
                                    if ($currentUserRole === 'expert_comptable') {
                                        return [
                                            'comptable' => 'Comptable',
                                            'assistant' => 'Assistant',
                                            'client' => 'Client',
                                        ];
                                    }

                                    // Les comptables peuvent créer des assistants et clients
                                    return [
                                        'assistant' => 'Assistant',
                                        'client' => 'Client',
                                    ];
                                })
                                ->default('assistant')
                                ->required(),
                        ]),
                        Select::make('statut')
                            ->label('Statut')
                            ->options([
                                'actif' => 'Actif',
                                'suspendu' => 'Suspendu',
                                'inactif' => 'Inactif',
                            ])
                            ->default('actif')
                            ->required(),
                    ]),

                Section::make('Notes')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),

                TextColumn::make('prenom')
                    ->label('Prénom')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Email copié!'),

                TextColumn::make('telephone')
                    ->label('Téléphone')
                    ->searchable()
                    ->toggleable(),

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

                TextColumn::make('statut')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'actif' => 'success',
                        'suspendu' => 'warning',
                        'inactif' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('companies_count')
                    ->label('Sociétés')
                    ->counts('companies')
                    ->alignCenter()
                    ->badge(),

                TextColumn::make('derniere_connexion')
                    ->label('Dernière Connexion')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('role_global')
                    ->label('Rôle')
                    ->options([
                        'admin_cabinet' => 'Admin Cabinet',
                        'expert_comptable' => 'Expert-Comptable',
                        'comptable' => 'Comptable',
                        'assistant' => 'Assistant',
                        'client' => 'Client',
                    ]),
                SelectFilter::make('statut')
                    ->label('Statut')
                    ->options([
                        'actif' => 'Actif',
                        'suspendu' => 'Suspendu',
                        'inactif' => 'Inactif',
                    ]),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->color('info'),
                    Tables\Actions\EditAction::make()
                        ->color('warning')
                        ->before(function (User $record) {
                            // Empêcher la modification d'utilisateurs avec des rôles supérieurs
                            if (!static::canEditUser($record)) {
                                Notification::make()
                                    ->title('Action non autorisée')
                                    ->body('Vous ne pouvez pas modifier un utilisateur avec un rôle supérieur.')
                                    ->danger()
                                    ->send();
                                return false;
                            }
                        }),
                    Tables\Actions\Action::make('toggle_status')
                        ->label(fn ($record) => $record->statut === 'actif' ? 'Suspendre' : 'Activer')
                        ->icon(fn ($record) => $record->statut === 'actif' ? 'heroicon-m-pause' : 'heroicon-m-play')
                        ->color(fn ($record) => $record->statut === 'actif' ? 'warning' : 'success')
                        ->action(function (User $record) {
                            $newStatus = $record->statut === 'actif' ? 'suspendu' : 'actif';
                            $record->update(['statut' => $newStatus]);

                            Notification::make()
                                ->title('Statut mis à jour')
                                ->body('Le statut de l\'utilisateur a été mis à jour.')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation(),
                    Tables\Actions\DeleteAction::make()
                        ->color('danger')
                        ->before(function (User $record) {
                            if (!static::canEditUser($record)) {
                                Notification::make()
                                    ->title('Action non autorisée')
                                    ->body('Vous ne pouvez pas supprimer un utilisateur avec un rôle supérieur.')
                                    ->danger()
                                    ->send();
                                return false;
                            }
                        }),
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
            ->emptyStateHeading('Aucun utilisateur trouvé')
            ->emptyStateDescription('Commencez par créer votre premier utilisateur.')
            ->emptyStateIcon('heroicon-o-users')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Créer un utilisateur')
                    ->icon('heroicon-m-plus'),
            ])
            ->striped()
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CompaniesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getEloquentQuery()->where('statut', 'actif')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function shouldRegisterNavigation(): bool
    {
        // Seuls les admins cabinet et experts-comptables peuvent gérer les utilisateurs
        $user = auth()->user();
        return $user && in_array($user->role_global, ['admin_cabinet', 'expert_comptable']);
    }

    protected static function canEditUser(User $user): bool
    {
        $currentUser = auth()->user();
        $roleHierarchy = [
            'super_admin' => 6,
            'admin_cabinet' => 5,
            'expert_comptable' => 4,
            'comptable' => 3,
            'assistant' => 2,
            'client' => 1,
        ];

        $currentUserLevel = $roleHierarchy[$currentUser->role_global] ?? 0;
        $targetUserLevel = $roleHierarchy[$user->role_global] ?? 0;

        return $currentUserLevel > $targetUserLevel;
    }
}
