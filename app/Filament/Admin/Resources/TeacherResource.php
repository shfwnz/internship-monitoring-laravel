<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TeacherResource\Pages;
use App\Filament\Admin\Resources\TeacherResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Wizard;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

// Model
use App\Models\User;
use App\Models\Teacher;

class TeacherResource extends Resource
{
    protected static ?string $model = Teacher::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup = 'Data';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Wizard::make([
                    Wizard\Step::make('User Information')
                        ->schema([
                            Forms\Components\TextInput::make('user.name')
                                ->label('Name')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('user.email')
                                ->label('Email')
                                ->email()
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('user.phone')
                                ->label('Phone')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\Select::make('user.gender')
                                ->label('Gender')
                                ->required()
                                ->options([
                                    'L' => 'Laki-Laki',
                                    'P' => 'Perempuan',
                                ]),
                            Forms\Components\Textarea::make('user.address')
                                ->label('Address')
                                ->rows(3)
                                ->maxLength(255)
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('user.password')
                                ->label('Password')
                                ->password()
                                ->required(fn (string $context): bool => $context === 'create')
                                ->dehydrated(fn ($state) => filled($state))
                                ->maxLength(255)
                                ->columnSpanFull(),
                        ])->columns(2),
                    Wizard\Step::make('Teacher Information')
                        ->schema([
                            Forms\Components\TextInput::make('nip')
                                ->label('NIP')
                                ->required()
                                ->maxLength(255)
                                ->unique(Teacher::class, 'nip', ignoreRecord: true)
                                ->columnSpanFull(),
                    ])
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nip')
                    ->label('NIP')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.phone')
                    ->label('Phone')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('user.gender')
                    ->label('Gender')
                    ->formatStateUsing(fn ($state) => $state === 'L' ? 'Laki-Laki' : 'Perempuan')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.roles.name')
                    ->badge()
                    ->label('Role'),
                Tables\Columns\TextColumn::make('user.address')
                    ->label('Address')
                    ->limit(50)
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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTeachers::route('/'),
        ];
    }
}

