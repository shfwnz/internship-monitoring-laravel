<?php

namespace App\Filament\Admin\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

// Model
use App\Models\Internship;

class InternshipLatest extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Internship::query()->latest()->limit(5)
            )
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
            ]);
    }
}
