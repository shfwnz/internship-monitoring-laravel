<?php

namespace App\Filament\Admin\Resources\StudentResource\Pages;

use App\Filament\Admin\Resources\StudentResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

// Model
use App\Models\User;
use App\Models\Student;

class ManageStudents extends ManageRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->using(function (array $data): Model {
                DB::beginTransaction();

                try {
                    $student = Student::create([
                        'nis' => $data['nis'],
                        'status' => false,
                    ]);

                    $user = User::create([
                        'name' => $data['user']['name'],
                        'email' => $data['user']['email'],
                        'phone' => $data['user']['phone'],
                        'gender' => $data['user']['gender'],
                        'address' => $data['user']['address'],
                        'password' => $data['user']['password'],
                        'userable_id' => $student->id,
                        'userable_type' => Student::class,
                        'image' => $data['user']['image'],
                    ]);

                    $user->assignRole('student');

                    DB::commit();

                    Notification::make()
                        ->title('Student created')
                        ->body('The student was successfully created.')
                        ->success()
                        ->send();

                    return $student->load('user');
                } catch (\Exception $e) {
                    \Log::error($e);
                    DB::rollBack();

                    Notification::make()
                        ->title('Error creating student')
                        ->body(
                            'There was an error while saving the data. Make sure the email is not already in use.',
                        )
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
            $record->update([
                'nis' => $data['nis'],
                'status' => $data['status'] ?? $record->status,
            ]);

            if ($record->user) {
                $userData = $data['user'];

                $userUpdateData = [
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'phone' => $userData['phone'],
                    'gender' => $userData['gender'],
                    'address' => $userData['address'],
                    'image' => $userData['image'],
                ];

                if (!empty($userData['password'])) {
                    $userUpdateData['password'] = Hash::make(
                        $userData['password'],
                    );
                }

                $record->user->update($userUpdateData);
            }

            DB::commit();

            Notification::make()
                ->title('Student updated')
                ->body('The student was successfully updated.')
                ->success()
                ->send();

            return $record->load('user');
        } catch (\Exception $e) {
            DB::rollBack();

            Notification::make()
                ->title('Error updating student')
                ->body(
                    'There was an error while saving the data. Make sure the email is not already in use.',
                )
                ->danger()
                ->send();

            $this->halt();
        }
    }

    public function handleRecordDeletion(Model $record): void
    {
        DB::beginTransaction();

        try {
            $record->user->delete();
            $record->delete();

            DB::commit();

            Notification::make()
                ->title('Student deleted')
                ->body('The student was successfully deleted.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            DB::rollBack();

            Notification::make()
                ->title('Error deleting student')
                ->body('There was an error while deleting the data.')
                ->danger()
                ->send();

            $this->halt();
        }
    }
}
