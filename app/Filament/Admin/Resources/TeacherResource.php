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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

// Model
use App\Models\User;
use App\Models\Teacher;

class TeacherResource extends Resource
{
    protected static ?string $model = Teacher::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup = 'Users';

    public static function form(Form $form): Form
    {
        return $form->columns(1)->schema([
            Wizard::make([
                Wizard\Step::make('User Information')
                    ->icon('heroicon-o-user')
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
                                'L' => 'Male',
                                'P' => 'Female',
                            ]),
                        Forms\Components\Textarea::make('user.address')
                            ->label('Address')
                            ->rows(3)
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('user.password')
                            ->label('Password')
                            ->password()
                            ->required(
                                fn(string $context): bool => $context ===
                                    'create',
                            )
                            ->dehydrated(fn($state) => filled($state))
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Wizard\Step::make('Teacher Information')
                    ->icon('heroicon-o-academic-cap')
                    ->schema([
                        Forms\Components\FileUpload::make('user.image')
                            ->label('Image')
                            ->image()
                            ->directory('teacher-images')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('nip')
                            ->label('NIP')
                            ->required()
                            ->maxLength(255)
                            ->unique(Teacher::class, 'nip', ignoreRecord: true)
                            ->columnSpanFull(),
                    ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('user.image')
                    ->label('Image')
                    ->height(50)
                    ->width(50)
                    ->rounded()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                    ->formatStateUsing(
                        fn($state) => $state === 'L' ? 'Male' : 'Female',
                    )
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
                Tables\Filters\SelectFilter::make('gender')
                    ->options([
                        'L' => 'Male',
                        'P' => 'Female',
                    ])
                    ->label('Gender')
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn(
                                Builder $query,
                                $value,
                            ): Builder => $query->whereHas('user', function (
                                Builder $query,
                            ) use ($value) {
                                $query->where('gender', $value);
                            }),
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->using(function (
                    Model $record,
                    array $data,
                ): Model {
                    return app(
                        TeacherResource\Pages\ManageTeachers::class,
                    )->handleRecordUpdate($record, $data);
                }),
                Tables\Actions\DeleteAction::make()->using(function (
                    Model $record,
                ): void {
                    app(
                        TeacherResource\Pages\ManageTeachers::class,
                    )->handleRecordDeletion($record);
                }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTeachers::route('/'),
        ];
    }
}
