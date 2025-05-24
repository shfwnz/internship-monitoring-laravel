<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BusinessField;

class BusinessFieldSeeder extends Seeder
{
    public function run(): void
    {
        BusinessField::factory()->count(10)->create();
    }
}
