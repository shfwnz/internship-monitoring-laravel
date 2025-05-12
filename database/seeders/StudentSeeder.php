<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\User;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $studentRole = Role::firstOrCreate(['name' => 'student']);

        for ($i = 1; $i <= 3; $i++) {
            $student = Student::factory()->create();

            $user = User::factory()->create([
                'name' => 'Student ' . $i,
                'email' => 'student' . $i . '@example.com',
                'userable_id' => $student->id,
                'userable_type' => Student::class,
            ]);

            $user->assignRole($studentRole);
        }
    }
}
