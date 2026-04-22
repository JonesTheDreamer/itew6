<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AcademicStructureSeeder::class, // college → programs → sections → curricula → courses → lessons → schedules
            UserSeeder::class,              // admin users, faculty (+job history, edu bg, awards), students (+grades, extracurriculars, violations, awards, edu bg)
            OrganizationSeeder::class,      // organizations → user_organization → organization_history
        ]);
    }
}
