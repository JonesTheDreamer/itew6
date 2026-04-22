<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedOrganizations();
        $this->seedUserOrganizations();
        // $this->seedOrganizationHistory();
    }

    private function seedOrganizations(): void
    {
        $collegeId = DB::table('college')->where('name', 'College of Computing Studies')->value('id');

        DB::table('organization')->insert([
            [
                'collegeId' => $collegeId,
                'organizationName' => 'Google Developer Student Club - CCS',
                'organizationDescription' => 'A student-run organization supported by Google that empowers students to grow their knowledge of developer technologies through hands-on projects, workshops, and events.',
                'dateCreated' => '2019-09-10',
                'isActive' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'collegeId' => $collegeId,
                'organizationName' => 'CCS Student Council',
                'organizationDescription' => 'The official student governance body of the College of Computing Studies, responsible for student welfare, academic affairs, and co-curricular activities.',
                'dateCreated' => '2001-06-01',
                'isActive' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'collegeId' => $collegeId,
                'organizationName' => 'CCS Research Society',
                'organizationDescription' => 'A multidisciplinary research organization dedicated to fostering a culture of innovation and inquiry among students and faculty in the College of Computing Studies.',
                'dateCreated' => '2015-08-20',
                'isActive' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'collegeId' => $collegeId,
                'organizationName' => 'CCS Robotics and Embedded Systems Club',
                'organizationDescription' => 'Focused on hardware hacking, robotics, IoT, and embedded systems. Students build real-world prototypes and compete in regional and national robotics competitions.',
                'dateCreated' => '2017-10-05',
                'isActive' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'collegeId' => $collegeId,
                'organizationName' => 'CCS Cybersecurity Guild',
                'organizationDescription' => 'A specialized organization for students passionate about ethical hacking, information security, and digital forensics. Hosts CTF competitions and security awareness campaigns.',
                'dateCreated' => '2021-03-15',
                'isActive' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'collegeId' => null,
                'organizationName' => 'Philippine Computing Society - Student Chapter',
                'organizationDescription' => 'The premier professional organization for IT and computing professionals in the Philippines. The student chapter engages members in networking events, technical seminars, and licensure preparation.',
                'dateCreated' => '2012-06-01',
                'isActive' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    private function seedUserOrganizations(): void
    {
        $orgs = DB::table('organization')->pluck('id', 'organizationName');

        // Faculty members in organizations
        $faculty = DB::table('users')
            ->whereIn('email', [
                'r.aquino@ccs.edu.ph',
                'c.santos@ccs.edu.ph',
                'f.reyes@ccs.edu.ph',
                'l.villanueva@ccs.edu.ph',
                'r.bautista@ccs.edu.ph',
            ])
            ->pluck('id', 'email');

        // Students in organizations
        $students = DB::table('users')
            ->whereIn('email', [
                'juan.santos@student.ccs.edu.ph',
                'maria.reyes@student.ccs.edu.ph',
                'ana.garcia@student.ccs.edu.ph',
                'kristine.villanueva@student.ccs.edu.ph',
                'mark.torres@student.ccs.edu.ph',
                'renaldo.delacruz@student.ccs.edu.ph',
                'patricia.mendoza@student.ccs.edu.ph',
                'emmanuel.lim@student.ccs.edu.ph',
                'ricardo.bautista@student.ccs.edu.ph',
                'jennifer.aquino@student.ccs.edu.ph',
                'angelica.navarro@student.ccs.edu.ph',
                'dennis.soriano@student.ccs.edu.ph',
            ])
            ->pluck('id', 'email');

        $memberships = [
            // Faculty advisers
            [$faculty['r.aquino@ccs.edu.ph'], $orgs['Google Developer Student Club - CCS'], 'Faculty Adviser', '2019-09-10', null],
            [$faculty['c.santos@ccs.edu.ph'], $orgs['CCS Research Society'], 'Faculty Adviser', '2015-08-20', null],
            [$faculty['f.reyes@ccs.edu.ph'], $orgs['CCS Cybersecurity Guild'], 'Faculty Adviser', '2021-03-15', null],
            [$faculty['l.villanueva@ccs.edu.ph'], $orgs['Philippine Computing Society - Student Chapter'], 'Faculty Adviser', '2015-06-01', null],
            [$faculty['r.bautista@ccs.edu.ph'], $orgs['CCS Robotics and Embedded Systems Club'], 'Faculty Adviser', '2019-06-01', null],

            // Student officers and members
            [$students['ana.garcia@student.ccs.edu.ph'], $orgs['Google Developer Student Club - CCS'], 'Vice President', '2024-08-01', null],
            [$students['angelica.navarro@student.ccs.edu.ph'], $orgs['Google Developer Student Club - CCS'], 'President', '2024-08-01', '2025-07-31'],
            [$students['juan.santos@student.ccs.edu.ph'], $orgs['Google Developer Student Club - CCS'], 'Member', '2025-09-01', null],
            [$students['mark.torres@student.ccs.edu.ph'], $orgs['Google Developer Student Club - CCS'], 'Member', '2025-09-01', null],

            [$students['maria.reyes@student.ccs.edu.ph'], $orgs['CCS Student Council'], 'Secretary', '2024-09-01', null],
            [$students['kristine.villanueva@student.ccs.edu.ph'], $orgs['CCS Student Council'], 'Member', '2024-09-01', null],

            [$students['ana.garcia@student.ccs.edu.ph'], $orgs['CCS Research Society'], 'Lead Researcher', '2024-06-01', null],
            [$students['patricia.mendoza@student.ccs.edu.ph'], $orgs['CCS Research Society'], 'Member', '2023-09-01', null],

            [$students['dennis.soriano@student.ccs.edu.ph'], $orgs['CCS Robotics and Embedded Systems Club'], 'Project Lead', '2024-08-01', null],
            [$students['ricardo.bautista@student.ccs.edu.ph'], $orgs['CCS Robotics and Embedded Systems Club'], 'Member', '2025-09-01', null],

            [$students['renaldo.delacruz@student.ccs.edu.ph'], $orgs['CCS Cybersecurity Guild'], 'Member', '2024-09-01', null],
            [$students['emmanuel.lim@student.ccs.edu.ph'], $orgs['CCS Cybersecurity Guild'], 'Member', '2024-09-01', null],

            [$students['jennifer.aquino@student.ccs.edu.ph'], $orgs['Philippine Computing Society - Student Chapter'], 'Member', '2024-09-15', null],
            [$students['ana.garcia@student.ccs.edu.ph'], $orgs['Philippine Computing Society - Student Chapter'], 'Officer', '2023-09-15', null],
        ];

        foreach ($memberships as [$userId, $orgId, $role, $dateJoined, $dateLeft]) {
            // guard against duplicate userId+orgId (unique constraint)
            $exists = DB::table('user_organization')
                ->where('userId', $userId)
                ->where('organizationId', $orgId)
                ->exists();

            if (!$exists) {
                DB::table('user_organization')->insert([
                    'userId' => $userId,
                    'organizationId' => $orgId,
                    'role' => $role,
                    'dateJoined' => $dateJoined,
                    'dateLeft' => $dateLeft,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}