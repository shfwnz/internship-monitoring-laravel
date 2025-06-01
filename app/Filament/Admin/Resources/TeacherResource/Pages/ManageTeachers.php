<?php

namespace App\Filament\Admin\Resources\TeacherResource\Pages;

use App\Filament\Admin\Resources\TeacherResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

// Model
use App\Models\User;
use App\Models\Teacher;

class ManageTeachers extends ManageRecords
{
    protected static string $resource = TeacherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->using(function (array $data): Model {
                DB::beginTransaction();

                try {
                    $teacher = Teacher::create([
                        'nip' => $data['nip'],
                    ]);

                    $user = User::create([
                        'name' => $data['user']['name'],
                        'email' => $data['user']['email'],
                        'phone' => $data['user']['phone'],
                        'gender' => $data['user']['gender'],
                        'address' => $data['user']['address'],
                        'password' => $data['user']['password'],
                        'userable_id' => $teacher->id,
                        'userable_type' => Teacher::class,
                        'image' => $data['user']['image'],
                    ]);

                    $user->assignRole('teacher');

                    DB::commit();

                    Notification::make()
                        ->title('Teacher created')
                        ->body('The teacher was successfully created.')
                        ->success()
                        ->send();

                    return $teacher->load('user');
                } catch (\Exception $e) {
                    DB::rollBack();

                    Notification::make()
                        ->title('Error creating teacher')
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
            $record->update([
                'nip' => $data['nip'],
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
                ->title('Teacher updated')
                ->body('The teacher was successfully updated.')
                ->success()
                ->send();

            return $record->load('user');
        } catch (\Exception $e) {
            DB::rollBack();

            Notification::make()
                ->title('Error updating teacher')
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
                ->title('Teacher deleted')
                ->body('The teacher was successfully deleted.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            DB::rollBack();

            Notification::make()
                ->title('Error deleting teacher')
                ->body('There was an error while deleting the data.')
                ->danger()
                ->send();

            $this->halt();
        }
    }
}
