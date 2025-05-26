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
            Actions\CreateAction::make()
                ->using(function (array $data): Model {
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
                            'address' => $data['user']['address'] ?? null,
                            'password' => $data['user']['password'],
                            'userable_id' => $teacher->id,
                            'userable_type' => Teacher::class,
                        ]);

                        $user->assignRole('teacher');

                        DB::commit();

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
                    'address' => $userData['address'] ?? null,

                ];

                if (!empty($userData['password'])) {
                    $userUpdateData['password'] = Hash::make($userData['password']); 
                }

                $record->user->update($userUpdateData);
            }
            
            DB::commit();

            return $record->load('user');

        } catch (\Exception $e) {
            DB::rollBack();

            Notification::make()
                ->title('Error updating teacher')
                ->body('There was an error while saving the data. Make sure the email is not already in use.')
                ->danger()
                ->send();

            $this->halt();
        }
    }
}
