<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $teacherRole = Role::firstOrCreate(['name' => 'teacher']);
        $studentRole = Role::firstOrCreate(['name' => 'student']);

        $admin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'gender' => 'L',
            'phone' => '08123456789',
            'address' => 'Jl. Merdeka No. 1',
            'password' => '12345678',
            'email_verified_at' => now(),
            'remember_token' => null,
        ])->assignRole($adminRole);

        // User::factory()->count(3)->create();

        $this->call([
            IndustrySeeder::class,
            TeacherSeeder::class,
            StudentSeeder::class,
        ]);
    }
}
