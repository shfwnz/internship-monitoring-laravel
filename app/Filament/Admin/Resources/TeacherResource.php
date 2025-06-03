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
use Filament\Forms\Components\Fieldset;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Filament\Notifications\Notification;
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
                        Fieldset::make('User Information')
                            ->relationship('user')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->required()
                                    ->unique(User::class, 'email', ignoreRecord: true)
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
                                    ->required(
                                        fn(string $context): bool => $context ===
                                            'create',
                                    )
                                    ->dehydrated(fn($state) => filled($state))
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpanFull(),
                Wizard\Step::make('Teacher Information')
                    ->icon('heroicon-o-academic-cap')
                    ->schema([
                        Fieldset::make('Teacher Information')
                            ->relationship('user')
                            ->schema([
                                Forms\Components\FileUpload::make('image')
                                    ->label('Image')
                                    ->image()
                                    ->directory('teacher-images')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                        Forms\Components\TextInput::make('nip')
                            ->label('NIP')
                            ->required()
                            ->maxLength(255)
                            ->unique(Teacher::class, 'nip', ignoreRecord: true)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
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
                Tables\Actions\EditAction::make()->successNotificationTitle('Teacher updated successfully'),
                Tables\Actions\DeleteAction::make()->successNotificationTitle('Teacher deleted successfully')
                    ->after(function (Model $record): void {
                        $record->user->delete();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotificationTitle('Teacher deleted successfully')
                        ->using(function (Collection $record): void {
                            foreach ($record as $teacher) {
                                if ($teacher->internships()->exists()) {
                                    Notification::make()
                                        ->title('Cannot delete ' . $teacher->user->name . ' with active internship records.')
                                        ->danger()
                                        ->send();
                                    continue;
                                }
                                $teacher->user->delete();
                                $teacher->delete();
                            }
                        }),
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['user']);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
