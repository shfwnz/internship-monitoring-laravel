<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\StudentResource\Pages;
use App\Filament\Admin\Resources\StudentResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Wizard;
use Illuminate\Database\Eloquent\Model;

// Model
use App\Models\User;
use App\Models\Student;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
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
                                ->required()
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
                        
                    Wizard\Step::make('Student Information')
                        ->schema([
                            Forms\Components\TextInput::make('nis')
                                ->label('NIS')
                                ->required()
                                ->maxLength(255)
                                ->unique(Student::class, 'nis', ignoreRecord: true)
                                ->columnSpanFull(),
                        ])->columns(2), 
                        
                ])->columnSpanFull() 
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
                Tables\Columns\TextColumn::make('nis')
                    ->label('NIS')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.phone')
                    ->label('Phone')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('status')
                    ->label('Status')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.gender')
                    ->label('Gender')
                    ->formatStateUsing(fn ($state) => $state === 'L' ? 'Laki-Laki' : 'Perempuan')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.roles.name')
                    ->badge()
                    ->label('Role'),
                Tables\Columns\TextColumn::make('user.address')
                    ->label('Address')
                    ->searchable()
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
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        1 => 'Active',
                        0 => 'Inactive',
                    ])
                    ->label('Status'),
                Tables\Filters\SelectFilter::make('gender')
                    ->options([
                        'L' => 'Laki-Laki',
                        'P' => 'Perempuan',
                    ])
                    ->label('Gender')
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['value'],
                                fn (Builder $query, $value): Builder => $query->whereHas('user', function (Builder $query) use ($value) {
                                    $query->where('gender', $value);
                                }),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->using(function (Model $record, array $data): Model {
                        return app(StudentResource\Pages\ManageStudents::class)
                            ->handleRecordUpdate($record, $data);
                    }),
                Tables\Actions\DeleteAction::make()
                    ->using(function (Model $record): void {
                        app(StudentResource\Pages\ManageStudents::class)
                            ->handleRecordDeletion($record);
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
            'index' => Pages\ManageStudents::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user']);
    }
}