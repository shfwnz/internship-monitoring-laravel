<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BusinessFieldResource\Pages;
use App\Filament\Admin\Resources\BusinessFieldResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

// Model
use App\Models\BusinessField;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;

class BusinessFieldResource extends Resource
{
    protected static ?string $model = BusinessField::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube-transparent';
    protected static ?string $navigationGroup = 'Industry';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true)
                ->dehydrated(fn(string $value): string => strtolower($value))
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
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
                Tables\Actions\DeleteAction::make()
                    ->successNotificationTitle('Field deleted successfully')
                    ->hidden(
                        fn(BusinessField $record): bool => $record
                            ->industries()
                            ->exists(),
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotificationTitle(
                            'Business Field deleted sucessfully',
                        )
                        ->using(function (Collection $record): void {
                            foreach ($record as $field) {
                                if ($field->industries()->exists()) {
                                    Notification::make()
                                        ->title(
                                            "Cannot delete {$field->name} with active internship records.",
                                        )
                                        ->danger()
                                        ->send();
                                    continue;
                                }
                                $field->delete();
                            }
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageBusinessFields::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
