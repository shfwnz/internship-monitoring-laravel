<?php

namespace App\Filament\Admin\Resources\InternshipResource\Pages;

use App\Filament\Admin\Resources\InternshipResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

// Model
use App\Models\Internship;

class ManageInternships extends ManageRecords
{
    protected static string $resource = InternshipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->using(function (array $data): Model {
                    DB::beginTransaction();

                    try {
                        $internship = Internship::create($data);

                        DB::commit();

                        Notification::make()
                            ->title('Internship created')
                            ->body('The internship was successfully created.')
                            ->success()
                            ->send();

                        return $internship;
                    } catch (\Exception $e) {
                        DB::rollBack();

                        Notification::make()
                            ->title('Error creating internship')
                            ->body('There was an error while creating the data.')
                            ->danger()
                            ->send();

                        $this->halt();
                    }
                })
        ];
    }

    public function handleRecordUpdate(Model $record, array $data): Model
    {
        DB::beginTransaction();

        try {
            $record->update($data);

            DB::commit();

            Notification::make()
                ->title('Internship updated')
                ->body('The internship was successfully updated.')
                ->success()
                ->send();

            return $record;
        } catch (\Exception $e) {
            DB::rollBack();

            Notification::make()
                ->title('Error updating internship')
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
                ->title('Internship deleted')
                ->body('The internship was successfully deleted.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            DB::rollBack();

            Notification::make()
                ->title('Error deleting internship')
                ->body('There was an error while deleting the data.')
                ->danger()
                ->send();

            $this->halt();
        }
    }
}
