<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\IndustryResource\Pages;
use App\Filament\Admin\Resources\IndustryResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\Wizard;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Filament\Notifications\Notification;

// Model
use App\Models\Industry;

class IndustryResource extends Resource
{
    protected static ?string $model = Industry::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'Industry';

    public static function form(Form $form): Form
    {
        return $form->columns(1)->schema([
            Wizard::make([
                Wizard\Step::make('Business Information')
                    ->icon('heroicon-o-building-office-2')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->unique(
                                Industry::class,
                                'email',
                                ignoreRecord: true,
                            )
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('Phone')
                            ->required()
                            ->maxLength(15)
                            ->tel()
                            ->unique(
                                Industry::class,
                                'phone',
                                ignoreRecord: true,
                            )
                            ->prefix('+62')
                            ->regex('/^\+62[8][0-9]{8,11}$/')
                            ->helperText('Format: +628xxxxxxxxxx'),
                        Forms\Components\TextInput::make('website')
                            ->label('Website')
                            ->url()
                            ->maxLength(255),
                        Forms\Components\Select::make('business_field_id')
                            ->label('Business Field')
                            ->relationship('business_field', 'name') // Show Name
                            ->required()
                            ->preload()
                            ->searchable()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('address')
                            ->rows(3)
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('business_field.name')
                    ->badge()
                    ->label('Business Field')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('website')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('address')
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
                Tables\Filters\SelectFilter::make('business_field_id')
                    ->label('Business Field')
                    ->relationship('business_field', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->successNotificationTitle(
                    'Industry updated sucessfully',
                ),
                Tables\Actions\DeleteAction::make()
                    ->successNotificationTitle('Industry deleted successfully')
                    ->hidden(
                        fn(Industry $record): bool => $record
                            ->internships()
                            ->exists(),
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotificationTitle(
                            'Industry deleted successfully',
                        )
                        ->using(function (Collection $record): void {
                            foreach ($record as $industry) {
                                if ($industry->internships()->exists()) {
                                    Notification::make()
                                        ->title(
                                            "Cannot delete {$industry->name} with active internship records.",
                                        )
                                        ->danger()
                                        ->send();
                                    continue;
                                }
                                $industry->delete();
                            }
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageIndustries::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
