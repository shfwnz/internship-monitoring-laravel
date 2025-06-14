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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

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
        return $form->columns(1)->schema([
            Wizard::make([
                Wizard\Step::make('Internship Information')
                    ->icon('heroicon-o-briefcase')
                    ->schema([
                        Forms\Components\Select::make('student_id')
                            ->label('Student')
                            ->required()
                            ->options(
                                Student::with('user')
                                    ->where('status', false)
                                    ->get()
                                    ->pluck('user.name', 'id'),
                            )
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('teacher_id')
                            ->label('Teacher')
                            ->options(
                                Teacher::with('user')
                                    ->get()
                                    ->pluck('user.name', 'id'),
                            )
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('industry_id')
                            ->label('Industry')
                            ->required()
                            ->options(Industry::all()->pluck('name', 'id'))
                            ->searchable()
                            ->preload(),
                    ]),
                Wizard\Step::make('Dates and Image')
                    ->icon('heroicon-o-calendar')
                    ->schema([
                        Forms\Components\FileUpload::make('file')
                            ->label('File')
                            ->required()
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxFiles(1)
                            ->downloadable()
                            ->openable()
                            ->maxSize(2048)
                            ->directory('internship-files')
                            ->visibility('public')
                            ->columnSpanFull(),
                        Forms\Components\DatePicker::make(
                            'start_date',
                        )->required(),
                        Forms\Components\DatePicker::make('end_date')
                            ->required()
                            ->afterOrEqual('start_date')
                            ->minDate(
                                fn(Forms\Get $get): ?string => $get(
                                    'start_date',
                                )
                                    ? date(
                                        'Y-m-d',
                                        strtotime(
                                            $get('start_date') . ' + 90 days',
                                        ),
                                    )
                                    : null,
                            ),
                    ])
                    ->columns(2),
            ]),
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
                Tables\Columns\IconColumn::make('file')
                    ->label('File')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: false),
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
                Tables\Actions\EditAction::make()->successNotificationTitle(
                    'Internship updated sucessfully',
                ),
                Tables\Actions\DeleteAction::make()->successNotificationTitle(
                    'Internship deleted sucessfully',
                ),
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
            'index' => Pages\ManageInternships::route('/'),
        ];
    }
}
