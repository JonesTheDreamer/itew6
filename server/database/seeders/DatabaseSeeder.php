<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
public function run(): void
{
    $this->call([
        AcademicStructureSeeder::class, // college → programs → sections → curricula → courses → lessons (NO schedules yet)
        UserSeeder::class,              // faculty + students (needs programs)
        OrganizationSeeder::class,
        LargeStudentSeeder::class
    ]);
    
    // Seed schedules last — needs both sections AND faculty to exist
    (new AcademicStructureSeeder())->seedSchedulesPublic();
}
}
