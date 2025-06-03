<?php

namespace App\Filament\Admin\Resources\IndustryResource\Pages;

use App\Filament\Admin\Resources\IndustryResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

// Model
use App\Models\Industry;

class ManageIndustries extends ManageRecords
{
    protected static string $resource = IndustryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->successNotificationTitle(
                'Industry created successfully',
            ),
        ];
    }
}
