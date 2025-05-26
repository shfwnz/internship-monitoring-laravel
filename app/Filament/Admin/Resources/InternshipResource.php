<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\InternshipResource\Pages;
use App\Filament\Admin\Resources\InternshipResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Wizard;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

// Model
use App\Models\Internship;
use App\Models\Industry;
use App\Models\Student;
use App\Models\Teacher;

class InternshipResource extends Resource
{
    protected static ?string $model = Internship::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Internship Information')
                        ->schema([
                            Forms\Components\Select::make('student_id')
                                ->label('Student')
                                ->required()
                                ->options(
                                    Student::with('user')
                                        ->get()
                                        ->pluck('user.name', 'id')
                                )
                                ->searchable()
                                ->preload(),
                            Forms\Components\Select::make('teacher_id')
                                ->label('Teacher')
                                ->required()
                                ->options(
                                    Teacher::with('user')
                                        ->get()
                                        ->pluck('user.name', 'id')
                                )
                                ->searchable()
                                ->preload(),
                            Forms\Components\Select::make('industry_id')
                                ->label('Industry')
                                ->required()
                                ->options(
                                    Industry::all()
                                        ->pluck('name', 'id')
                                )
                                ->searchable()
                                ->preload(),
                        ]),
                    Wizard\Step::make('Dates and Image')
                        ->schema([
                            Forms\Components\FileUpload::make('image')
                                ->label('Image')
                                ->image()
                                ->required()
                                ->columnSpanFull(),
                            Forms\Components\DatePicker::make('start_date')
                                ->required(),
                            Forms\Components\DatePicker::make('end_date')
                                ->required()
                        ])->columns(2),

                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.user.name')
                    ->label('Student')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('teacher.user.name')
                    ->label('Teacher')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('industry.name')
                    ->label('Industry')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date('M d, Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date('M d, Y')
                    ->sortable(),
                Tables\Columns\ImageColumn::make('image')
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
            'index' => Pages\ManageInternships::route('/'),
        ];
    }
}
