<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;
use App\Models\Teacher;
use App\Models\User;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teacherRole = Role::firstOrCreate(['name' => 'teacher']);

        for ($i = 1; $i <= 3; $i++) {
            $teacher = Teacher::factory()->create();

            $user = User::factory()->create([
                'name' => 'Teacher ' . $i,
                'email' => 'teacher' . $i . '@example.com',
                'userable_id' => $teacher->id,
                'userable_type' => Teacher::class,
            ]);

            $user->assignRole($teacherRole);
        }
    }
}
