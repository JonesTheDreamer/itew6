<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("
            CREATE OR REPLACE VIEW student_full_info AS
            SELECT
                s.id,
                s.\"studentId\",
                CONCAT(e.\"firstName\", ' ', e.\"lastName\") AS \"fullName\",
                e.\"firstName\",
                e.\"lastName\",
                e.\"middleName\",
                e.email,
                e.\"mobileNumber\",
                e.\"birthDate\",
                e.age,
                e.\"birthProvince\",
                e.city,
                e.province,
                p.id AS \"programId\",
                p.name AS \"programName\",
                s.\"yearLevel\",
                s.\"unitsTaken\",
                s.\"unitsLeft\",
                s.gpa,
                s.status,
                s.\"dateEnrolled\",
                s.\"dateGraduated\",
                s.\"dateDropped\",
                s.\"userId\",
                s.created_at,
                s.updated_at
            FROM student s
            JOIN users e ON s.\"userId\" = e.id
            JOIN program p ON s.\"programId\" = p.id
        ");

        DB::statement("
            CREATE OR REPLACE VIEW faculty_full_info AS
            SELECT
                f.id,
                CONCAT(e.\"firstName\", ' ', e.\"lastName\") AS \"fullName\",
                e.\"firstName\",
                e.\"lastName\",
                e.email,
                e.\"mobileNumber\",
                e.age,
                e.city,
                e.province,
                f.position,
                f.department,
                f.\"employmentType\",
                f.\"monthlyIncome\",
                f.\"employmentDate\",
                f.\"userId\",
                f.created_at,
                f.updated_at
            FROM faculty f
            JOIN users e ON f.\"userId\" = e.id
        ");
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS student_full_info');
        DB::statement('DROP VIEW IF EXISTS faculty_full_info');
    }
};