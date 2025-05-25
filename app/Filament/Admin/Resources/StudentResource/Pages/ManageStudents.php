<?php

namespace App\Filament\Admin\Resources\StudentResource\Pages;

use App\Filament\Admin\Resources\StudentResource;
use App\Models\User;
use App\Models\Student;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class ManageStudents extends ManageRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->using(function (array $data): Model {
                    // Create Student first
                    $student = Student::create([
                        'nis' => $data['nis'],
                        'status' => false,
                    ]);

                    // Create User with polymorphic relation
                    $user = User::create([
                        'name' => $data['user']['name'],
                        'email' => $data['user']['email'],
                        'phone' => $data['user']['phone'],
                        'gender' => $data['user']['gender'],
                        'address' => $data['user']['address'] ?? null,
                        'password' => $data['user']['password'],
                        'userable_id' => $student->id,
                        'userable_type' => Student::class,
                    ]);

                    // Assign role to user
                    $user->assignRole('student');

                    return $student->load('user');
                }),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Update Student data
        $record->update([
            'nis' => $data['nis'],
            'status' => $data['status'] ?? true,
        ]);

        // Update User data
        $userUpdateData = [
            'name' => $data['user']['name'],
            'email' => $data['user']['email'],
            'phone' => $data['user']['phone'],
            'gender' => $data['user']['gender'],
            'address' => $data['user']['address'] ?? null,
        ];

        // Only update password if provided
        if (!empty($data['user']['password'])) {
            $userUpdateData['password'] = $data['user']['password'];
        }

        $record->user()->update($userUpdateData);

        return $record->load('user');
    }

    protected function handleRecordDeletion(Model $record): void
    {
        // Delete the associated user first
        if ($record->user) {
            $record->user->delete();
        }
        
        // Then delete the student record
        $record->delete();
    }
}