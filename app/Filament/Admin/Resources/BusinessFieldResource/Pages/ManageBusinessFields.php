<?php

namespace App\Filament\Admin\Resources\BusinessFieldResource\Pages;

use App\Filament\Admin\Resources\BusinessFieldResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageBusinessFields extends ManageRecords
{
    protected static string $resource = BusinessFieldResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
