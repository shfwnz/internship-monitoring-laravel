<?php

namespace App\Filament\Admin\Resources\StudentResource\Pages;

use App\Filament\Admin\Resources\StudentResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Facades\DB;

// Model
use App\Models\User;
use App\Models\Student;

class ManageStudents extends ManageRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->using(function (array $data): Model {
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
                            'address' => $data['user']['address'] ?? null,
                            'password' => $data['user']['password'],
                            'userable_id' => $student->id,
                            'userable_type' => Student::class,
                        ]);

                        
                        $user->assignRole('student');

                        DB::commit();

                        return $student->load('user');
                    } catch (\Exception $e) {
                        DB::rollBack();

                        throw $e;
                    }
                }),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        DB::beginTransaction();

        try {
            $record->update([
                'nis' => $data['nis'],
                'status' => $data['status'] ?? $record->status,
            ]);

            if ($record->user) {
                $userUpdateData = [
                    'name' => $data['user']['name'],
                    'email' => $data['user']['email'],
                    'phone' => $data['user']['phone'],
                    'gender' => $data['user']['gender'],
                    'address' => $data['user']['address'] ?? null,
                ];

                // Only update password if provided and not empty
                if (!empty($data['user']['password'])) {
                    $userUpdateData['password'] = $data['user']['password']; // Already hashed by form
                }

                $record->user->update($userUpdateData);
            }
            
            DB::commit();

            return $record->load('user');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function handleRecordDeletion(Model $record): void
    {
        DB::beginTransaction();

        try {
            $record->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}