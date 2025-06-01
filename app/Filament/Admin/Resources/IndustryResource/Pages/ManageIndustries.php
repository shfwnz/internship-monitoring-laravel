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
            Actions\CreateAction::make()->using(function (array $data): Model {
                DB::beginTransaction();

                try {
                    $industry = Industry::create($data);

                    DB::commit();

                    Notification::make()
                        ->title('Industry created')
                        ->body('The industry was successfully created.')
                        ->success()
                        ->send();

                    return $industry;
                } catch (\Exception $e) {
                    DB::rollBack();

                    Notification::make()
                        ->title('Error creating industry')
                        ->body('There was an error while creating the data.')
                        ->danger()
                        ->send();

                    $this->halt();
                }
            }),
        ];
    }

    public function handleRecordUpdate(Model $record, array $data): Model
    {
        DB::beginTransaction();

        try {
            $record->update($data);

            DB::commit();

            Notification::make()
                ->title('Industry updated')
                ->body('The industry was successfully updated.')
                ->success()
                ->send();

            return $record;
        } catch (\Exception $e) {
            DB::rollBack();

            Notification::make()
                ->title('Error updating industry')
                ->body('There was an error while updating the data.')
                ->danger()
                ->send();

            $this->halt();
        }
    }

    public function handleRecordDeletion(Model $record): void
    {
        DB::beginTransaction();

        try {
            $record->delete();

            DB::commit();

            Notification::make()
                ->title('Industry deleted')
                ->body('The industry was successfully deleted.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            DB::rollBack();

            Notification::make()
                ->title('Error deleting industry')
                ->body('There was an error while deleting the data.')
                ->danger()
                ->send();

            $this->halt();
        }
    }
}
