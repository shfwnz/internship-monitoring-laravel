<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Wizard;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

// Model
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationGroup = 'Users';

    public static function form(Form $form): Form
    {
        return $form->columns(1)->schema([
            Wizard::make([
                Wizard\Step::make('User Information')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->unique(User::class, 'email', ignoreRecord: true)
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('Phone')
                            ->required()
                            ->maxLength(15)
                            ->tel()
                            ->prefix('+62')
                            ->regex('/^\+62[8][0-9]{8,11}$/')
                            ->unique(User::class, 'phone', ignoreRecord: true)
                            ->helperText('Format: +628xxxxxxxxxx'),
                        Forms\Components\Select::make('gender')
                            ->label('Gender')
                            ->required()
                            ->options([
                                'L' => 'Male',
                                'P' => 'Female',
                            ]),
                        Forms\Components\Textarea::make('address')
                            ->label('Address')
                            ->rows(3)
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->maxLength(255)
                            ->dehydrateStateUsing(
                                fn($state) => Hash::make($state),
                            )
                            ->dehydrated(fn($state) => filled($state))
                            ->required(
                                fn(string $operation): bool => $operation ===
                                    'create',
                            )
                            ->columnSpanFull(),
                        Forms\Components\Select::make('roles')
                            ->label('Role')
                            ->relationship('roles', 'name')
                            ->options(function () {
                                return Role::where('guard_name', 'web')->pluck(
                                    'name',
                                    'id',
                                );
                            })
                            ->multiple()
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Wizard\Step::make('image')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Image')
                            ->image()
                            ->required()
                            ->directory('user-images')
                            ->columnSpanFull(),
                    ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Image')
                    ->height(50)
                    ->width(50)
                    ->rounded()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('gender')
                    ->label('Gender')
                    ->formatStateUsing(
                        fn($state) => $state === 'L' ? 'Male' : 'Female',
                    )
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')->searchable(),
                Tables\Columns\TextColumn::make('user.roles.name')
                    ->badge()
                    ->label('Role')
                    ->getStateUsing(function ($record) {
                        $roles = $record->roles()->get();

                        if ($roles->isEmpty() && $record->userable) {
                            $roles =
                                $record->userable->user->roles ?? collect();
                        }

                        return $roles->pluck('name')->toArray();
                    }),
                Tables\Columns\TextColumn::make('address')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('userable_id')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('userable_type')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options(Role::all()->pluck('name', 'id'))
                    ->label('Role')
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn(
                                Builder $query,
                                $value,
                            ): Builder => $query->whereHas('roles', function (
                                Builder $query,
                            ) use ($value) {
                                $query->where('roles.id', $value);
                            }),
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->successNotificationTitle(
                        'User deleted successfully',
                    ),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageUsers::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
