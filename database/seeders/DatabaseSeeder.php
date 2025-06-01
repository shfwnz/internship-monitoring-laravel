<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        app()[
            \Spatie\Permission\PermissionRegistrar::class
        ]->forgetCachedPermissions();

        $adminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $teacherRole = Role::firstOrCreate(['name' => 'teacher']);
        $studentRole = Role::firstOrCreate(['name' => 'student']);

        $adminRole->givePermissionTo(Permission::all());

        $teacherRole->givePermissionTo(
            Permission::where('name', 'like', '%industry%')->get(),
        );
        $studentRole->givePermissionTo(
            Permission::where('name', 'like', '%industry%')->get(),
        );

        $admin = User::factory()
            ->create([
                'name' => 'Super Admin',
                'email' => 'superadmin@example.com',
                'gender' => 'L',
                'phone' => '08123456789',
                'address' => 'Jl. Merdeka No. 1',
                'password' => '12345678',
                'email_verified_at' => now(),
                'remember_token' => null,
            ])
            ->assignRole($adminRole);

        // User::factory()->count(3)->create();

        $this->call([
            BusinessFieldSeeder::class,
            IndustrySeeder::class,
            TeacherSeeder::class,
            StudentSeeder::class,
        ]);
    }
}
