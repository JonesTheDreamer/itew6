<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedAdminUsers();
        $this->seedFacultyUsers();
        $this->seedStudentUsers();
    }

    // ─────────────────────────────────────────────
    //  ADMINS
    // ─────────────────────────────────────────────

    private function seedAdminUsers(): void
    {
        $admins = [
            [
                // 'name'         => 'System Administrator',
                'firstName' => 'System',
                'lastName' => 'Administrator',
                'middleName' => null,
                'age' => 38,
                'birthDate' => '1987-04-10',
                'birthProvince' => 'Metro Manila',
                'subdivision' => 'BF Homes',
                'street' => 'Acacia St.',
                'barangay' => 'Almanza Dos',
                'city' => 'Las Piñas',
                'province' => 'Metro Manila',
                'postalCode' => '1750',
                'mobileNumber' => '09171000001',
                'email' => 'admin@email.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Maria Remedios Delos Santos',
                'firstName' => 'Maria Remedios',
                'lastName' => 'Delos Santos',
                'middleName' => 'Bautista',
                'age' => 45,
                'birthDate' => '1980-09-22',
                'birthProvince' => 'Laguna',
                'subdivision' => 'Villa Sta. Rosa',
                'street' => 'Dahlia St.',
                'barangay' => 'Dila',
                'city' => 'Santa Rosa',
                'province' => 'Laguna',
                'postalCode' => '4026',
                'mobileNumber' => '09181000002',
                'email' => 'registrar@ccs.edu.ph',
                'password' => Hash::make('Admin@12345'),
            ],
            [
                'name' => 'Eduardo Luis Villanueva',
                'firstName' => 'Eduardo',
                'lastName' => 'Villanueva',
                'middleName' => 'Luis',
                'age' => 41,
                'birthDate' => '1984-02-14',
                'birthProvince' => 'Batangas',
                'subdivision' => 'Greenville Subd.',
                'street' => 'Sampaguita Ave.',
                'barangay' => 'Wawa',
                'city' => 'San Jose',
                'province' => 'Batangas',
                'postalCode' => '4227',
                'mobileNumber' => '09261000003',
                'email' => 'hradmin@ccs.edu.ph',
                'password' => Hash::make('Admin@12345'),
            ],
        ];

        foreach ($admins as $admin) {
            DB::table('users')->insert(array_merge($this->userFields($admin), [
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    // ─────────────────────────────────────────────
    //  FACULTY
    // ─────────────────────────────────────────────

    private function seedFacultyUsers(): void
    {
        $facultyData = [
            [
                'user' => [
                    'name' => 'Roberto Alfonso Aquino',
                    'firstName' => 'Roberto',
                    'lastName' => 'Aquino',
                    'middleName' => 'Alfonso',
                    'age' => 48,
                    'birthDate' => '1977-07-18',
                    'birthProvince' => 'Cavite',
                    'subdivision' => 'Metrogate Silang',
                    'street' => 'Orchid Drive',
                    'barangay' => 'Puting Kahoy',
                    'city' => 'Silang',
                    'province' => 'Cavite',
                    'postalCode' => '4118',
                    'mobileNumber' => '09171111001',
                    'email' => 'r.aquino@ccs.edu.ph',
                    'password' => Hash::make('Faculty@12345'),
                ],
                'faculty' => [
                    'position' => 'Associate Professor',
                    'employmentDate' => '2010-06-01',
                    'employmentType' => 'Full-time',
                    'monthlyIncome' => 65000.00,
                    'department' => 'Information Technology',
                ],
                'jobHistory' => [
                    [
                        'position' => 'Software Engineer',
                        'employmentDate' => '2001-06-01',
                        'employmentEndDate' => '2006-05-31',
                        'employmentType' => 'Full-time',
                        'company' => 'Accenture Philippines',
                        'workLocation' => 'Taguig City, Metro Manila',
                    ],
                    [
                        'position' => 'Senior Software Engineer',
                        'employmentDate' => '2006-06-15',
                        'employmentEndDate' => '2010-05-31',
                        'employmentType' => 'Full-time',
                        'company' => 'IBM Philippines',
                        'workLocation' => 'Eastwood City, Quezon City',
                    ],
                ],
                'eduBackground' => [
                    ['schoolUniversity' => 'De La Salle University', 'startYear' => 1993, 'graduateYear' => 1997, 'type' => 'College', 'award' => 'Cum Laude'],
                    ['schoolUniversity' => 'Ateneo de Manila University', 'startYear' => 2000, 'graduateYear' => 2003, 'type' => 'Graduate', 'award' => null],
                    ['schoolUniversity' => 'University of the Philippines Diliman', 'startYear' => 2007, 'graduateYear' => 2010, 'type' => 'Post-Graduate', 'award' => null],
                ],
                'awards' => [
                    ['title' => 'Outstanding Faculty Award', 'awardingDate' => '2023-04-15', 'awardingOrganization' => 'College of Computing Studies', 'awardingLocation' => 'CCS Auditorium, Laguna'],
                    ['title' => 'Best Research Paper - AI and Systems', 'awardingDate' => '2022-11-20', 'awardingOrganization' => 'Philippine IT Educators Conference', 'awardingLocation' => 'Manila Hotel, Manila'],
                ],
            ],
            [
                'user' => [
                    'name' => 'Cecilia Marasigan Santos',
                    'firstName' => 'Cecilia',
                    'lastName' => 'Santos',
                    'middleName' => 'Marasigan',
                    'age' => 52,
                    'birthDate' => '1973-03-05',
                    'birthProvince' => 'Laguna',
                    'subdivision' => 'Springville Homes',
                    'street' => 'Jade St.',
                    'barangay' => 'Bagong Bayan',
                    'city' => 'Biñan',
                    'province' => 'Laguna',
                    'postalCode' => '4024',
                    'mobileNumber' => '09181111002',
                    'email' => 'c.santos@ccs.edu.ph',
                    'password' => Hash::make('Faculty@12345'),
                ],
                'faculty' => [
                    'position' => 'Full Professor',
                    'employmentDate' => '2005-06-01',
                    'employmentType' => 'Full-time',
                    'monthlyIncome' => 85000.00,
                    'department' => 'Computer Science',
                ],
                'jobHistory' => [
                    [
                        'position' => 'Systems Analyst',
                        'employmentDate' => '1998-03-01',
                        'employmentEndDate' => '2002-02-28',
                        'employmentType' => 'Full-time',
                        'company' => 'Globe Telecom',
                        'workLocation' => 'Bonifacio Global City, Taguig',
                    ],
                    [
                        'position' => 'Database Administrator',
                        'employmentDate' => '2002-03-15',
                        'employmentEndDate' => '2005-05-31',
                        'employmentType' => 'Full-time',
                        'company' => 'BDO Unibank',
                        'workLocation' => 'Mandaluyong City, Metro Manila',
                    ],
                ],
                'eduBackground' => [
                    ['schoolUniversity' => 'University of Santo Tomas', 'startYear' => 1990, 'graduateYear' => 1994, 'type' => 'College', 'award' => 'Magna Cum Laude'],
                    ['schoolUniversity' => 'De La Salle University', 'startYear' => 1995, 'graduateYear' => 1998, 'type' => 'Graduate', 'award' => null],
                    ['schoolUniversity' => 'Ateneo de Manila University', 'startYear' => 2003, 'graduateYear' => 2007, 'type' => 'Post-Graduate', 'award' => 'Academic Excellence Award'],
                ],
                'awards' => [
                    ['title' => 'Teacher of the Year', 'awardingDate' => '2024-04-20', 'awardingOrganization' => 'College of Computing Studies', 'awardingLocation' => 'CCS Convention Hall, Laguna'],
                    ['title' => 'Best Research Mentor Award', 'awardingDate' => '2021-10-05', 'awardingOrganization' => 'Philippine Computing Society', 'awardingLocation' => 'SMX Convention Center, Pasay'],
                    ['title' => 'Excellence in Teaching - Computer Science', 'awardingDate' => '2019-08-30', 'awardingOrganization' => 'Commission on Higher Education Region IV-A', 'awardingLocation' => 'CHED-CALABARZON Office, Calamba'],
                ],
            ],
            [
                'user' => [
                    'name' => 'Fernando Cruz Reyes',
                    'firstName' => 'Fernando',
                    'lastName' => 'Reyes',
                    'middleName' => 'Cruz',
                    'age' => 38,
                    'birthDate' => '1987-11-30',
                    'birthProvince' => 'Quezon',
                    'subdivision' => 'Pacific Malayan',
                    'street' => 'Palm Drive',
                    'barangay' => 'San Antonio',
                    'city' => 'San Pablo',
                    'province' => 'Laguna',
                    'postalCode' => '4000',
                    'mobileNumber' => '09271111003',
                    'email' => 'f.reyes@ccs.edu.ph',
                    'password' => Hash::make('Faculty@12345'),
                ],
                'faculty' => [
                    'position' => 'Assistant Professor',
                    'employmentDate' => '2016-06-01',
                    'employmentType' => 'Full-time',
                    'monthlyIncome' => 50000.00,
                    'department' => 'Information Technology',
                ],
                'jobHistory' => [
                    [
                        'position' => 'Web Developer',
                        'employmentDate' => '2011-01-15',
                        'employmentEndDate' => '2014-12-31',
                        'employmentType' => 'Full-time',
                        'company' => 'Pointwest Technologies',
                        'workLocation' => 'Quezon City, Metro Manila',
                    ],
                    [
                        'position' => 'Full Stack Developer',
                        'employmentDate' => '2015-02-01',
                        'employmentEndDate' => '2016-05-31',
                        'employmentType' => 'Full-time',
                        'company' => 'Expedock Inc.',
                        'workLocation' => 'Makati City, Metro Manila',
                    ],
                ],
                'eduBackground' => [
                    ['schoolUniversity' => 'Laguna State Polytechnic University', 'startYear' => 2004, 'graduateYear' => 2008, 'type' => 'College', 'award' => null],
                    ['schoolUniversity' => 'University of the Philippines Los Baños', 'startYear' => 2013, 'graduateYear' => 2016, 'type' => 'Graduate', 'award' => null],
                ],
                'awards' => [
                    ['title' => 'Best Capstone Project Adviser', 'awardingDate' => '2022-04-10', 'awardingOrganization' => 'College of Computing Studies', 'awardingLocation' => 'CCS Auditorium, Laguna'],
                ],
            ],
            [
                'user' => [
                    'name' => 'Luz Bernardo Villanueva',
                    'firstName' => 'Luz',
                    'lastName' => 'Villanueva',
                    'middleName' => 'Bernardo',
                    'age' => 44,
                    'birthDate' => '1981-06-17',
                    'birthProvince' => 'Batangas',
                    'subdivision' => 'Camella Homes',
                    'street' => 'Rose Street',
                    'barangay' => 'Balagtas',
                    'city' => 'Batangas City',
                    'province' => 'Batangas',
                    'postalCode' => '4200',
                    'mobileNumber' => '09191111004',
                    'email' => 'l.villanueva@ccs.edu.ph',
                    'password' => Hash::make('Faculty@12345'),
                ],
                'faculty' => [
                    'position' => 'Associate Professor',
                    'employmentDate' => '2012-06-01',
                    'employmentType' => 'Full-time',
                    'monthlyIncome' => 68000.00,
                    'department' => 'Computer Science',
                ],
                'jobHistory' => [
                    [
                        'position' => 'Network Engineer',
                        'employmentDate' => '2004-08-01',
                        'employmentEndDate' => '2009-07-31',
                        'employmentType' => 'Full-time',
                        'company' => 'PLDT Enterprise',
                        'workLocation' => 'Makati City, Metro Manila',
                    ],
                    [
                        'position' => 'Senior Network Architect',
                        'employmentDate' => '2009-08-15',
                        'employmentEndDate' => '2012-05-31',
                        'employmentType' => 'Full-time',
                        'company' => 'Converge ICT Solutions',
                        'workLocation' => 'Quezon City, Metro Manila',
                    ],
                ],
                'eduBackground' => [
                    ['schoolUniversity' => 'Batangas State University', 'startYear' => 1999, 'graduateYear' => 2003, 'type' => 'College', 'award' => 'Cum Laude'],
                    ['schoolUniversity' => 'Mapúa University', 'startYear' => 2006, 'graduateYear' => 2009, 'type' => 'Graduate', 'award' => null],
                    ['schoolUniversity' => 'University of Santo Tomas', 'startYear' => 2014, 'graduateYear' => 2018, 'type' => 'Post-Graduate', 'award' => null],
                ],
                'awards' => [
                    ['title' => 'Outstanding Research Publication Award', 'awardingDate' => '2023-09-15', 'awardingOrganization' => 'Philippine Journal of Information Technology', 'awardingLocation' => 'Cebu City, Cebu'],
                ],
            ],
            [
                'user' => [
                    'name' => 'Rodrigo Tiongson Bautista',
                    'firstName' => 'Rodrigo',
                    'lastName' => 'Bautista',
                    'middleName' => 'Tiongson',
                    'age' => 35,
                    'birthDate' => '1990-01-25',
                    'birthProvince' => 'Laguna',
                    'subdivision' => 'Avida Settings Sta. Rosa',
                    'street' => 'Magnolia Drive',
                    'barangay' => 'Tagapo',
                    'city' => 'Santa Rosa',
                    'province' => 'Laguna',
                    'postalCode' => '4026',
                    'mobileNumber' => '09361111005',
                    'email' => 'r.bautista@ccs.edu.ph',
                    'password' => Hash::make('Faculty@12345'),
                ],
                'faculty' => [
                    'position' => 'Instructor',
                    'employmentDate' => '2019-06-01',
                    'employmentType' => 'Full-time',
                    'monthlyIncome' => 42000.00,
                    'department' => 'Computer Engineering',
                ],
                'jobHistory' => [
                    [
                        'position' => 'Embedded Systems Engineer',
                        'employmentDate' => '2014-07-01',
                        'employmentEndDate' => '2018-06-30',
                        'employmentType' => 'Full-time',
                        'company' => 'Texas Instruments Philippines',
                        'workLocation' => 'Baguio City, Benguet',
                    ],
                    [
                        'position' => 'IoT Solutions Specialist',
                        'employmentDate' => '2018-07-15',
                        'employmentEndDate' => '2019-05-31',
                        'employmentType' => 'Full-time',
                        'company' => 'Trends & Technologies Inc.',
                        'workLocation' => 'Pasig City, Metro Manila',
                    ],
                ],
                'eduBackground' => [
                    ['schoolUniversity' => 'Mapúa University', 'startYear' => 2008, 'graduateYear' => 2012, 'type' => 'College', 'award' => null],
                    ['schoolUniversity' => 'De La Salle University', 'startYear' => 2019, 'graduateYear' => 2022, 'type' => 'Graduate', 'award' => null],
                ],
                'awards' => [],
            ],
        ];

        foreach ($facultyData as $data) {
            $userId = DB::table('users')->insertGetId(array_merge($this->userFields($data['user']), [
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]));

            $facultyId = DB::table('faculty')->insertGetId(array_merge($data['faculty'], [
                'userId' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]));

            foreach ($data['jobHistory'] as $job) {
                DB::table('job_history')->insert(array_merge($job, [
                    'facultyId' => $facultyId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }

            foreach ($data['eduBackground'] as $edu) {
                DB::table('educational_background')->insert(array_merge($edu, [
                    'userId' => $userId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }

            foreach ($data['awards'] as $award) {
                DB::table('awards')->insert(array_merge($award, [
                    'userId' => $userId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }
    }

    // ─────────────────────────────────────────────
    //  STUDENTS
    // ─────────────────────────────────────────────

    private function seedStudentUsers(): void
    {
        $programs = DB::table('program')->pluck('id', 'name');

        $studentData = [
            /* ── BSIT ── */
            [
                'user' => [
                    'name' => 'Juan Miguel Cruz Santos',
                    'firstName' => 'Juan Miguel',
                    'lastName' => 'Santos',
                    'middleName' => 'Cruz',
                    'age' => 19,
                    'birthDate' => '2006-03-15',
                    'birthProvince' => 'Laguna',
                    'subdivision' => 'Sunvalley Subd.',
                    'street' => 'Sampaguita St.',
                    'barangay' => 'Putol',
                    'city' => 'San Pedro',
                    'province' => 'Laguna',
                    'postalCode' => '4023',
                    'mobileNumber' => '09171200001',
                    'email' => 'juan.santos@student.ccs.edu.ph',
                    'password' => Hash::make('Student@12345'),
                ],
                'student' => ['studentId' => '2025-00001', 'program' => 'BS Information Technology', 'yearLevel' => 1, 'unitsTaken' => 18, 'unitsLeft' => 117, 'dateEnrolled' => '2025-08-15', 'gpa' => 1.50, 'status' => 'Active'],
                'extraCurriculars' => [
                    ['activity' => 'Web Development Competition', 'role' => 'Contestant', 'organization' => 'Google Developer Student Club', 'startDate' => '2025-10-01', 'endDate' => '2025-10-30'],
                    ['activity' => 'CCS Acquaintance Party Committee', 'role' => 'Member', 'organization' => 'CCS Student Council', 'startDate' => '2025-08-20', 'endDate' => '2025-09-15'],
                ],
                'awards' => [
                    ['title' => "Dean's List - 1st Semester 2025-2026", 'awardingDate' => '2026-01-20', 'awardingOrganization' => 'College of Computing Studies', 'awardingLocation' => 'CCS Hall, Laguna'],
                ],
                'violations' => [],
                'eduBackground' => [
                    ['schoolUniversity' => 'San Pedro National High School', 'startYear' => 2021, 'graduateYear' => 2025, 'type' => 'Senior High School', 'award' => 'With Honors'],
                    ['schoolUniversity' => 'San Pedro Elementary School', 'startYear' => 2015, 'graduateYear' => 2021, 'type' => 'Elementary', 'award' => null],
                ],
                'sem1Grades' => [1.25, 1.50, 1.50, 1.75, 1.25],
                'skills' => [
                    ['name' => 'Python Programming', 'isAcademic' => true],
                    ['name' => 'HTML and CSS', 'isAcademic' => true],
                    ['name' => 'Git Version Control', 'isAcademic' => true],
                    ['name' => 'Problem Solving', 'isAcademic' => false],
                    ['name' => 'Teamwork', 'isAcademic' => false],
                ],
            ],
            [
                'user' => [
                    'name' => 'Maria Clara Aguilar Reyes',
                    'firstName' => 'Maria Clara',
                    'lastName' => 'Reyes',
                    'middleName' => 'Aguilar',
                    'age' => 20,
                    'birthDate' => '2005-07-22',
                    'birthProvince' => 'Laguna',
                    'subdivision' => 'Vista Verde',
                    'street' => 'Ilang-Ilang St.',
                    'barangay' => 'Aplaya',
                    'city' => 'Calamba',
                    'province' => 'Laguna',
                    'postalCode' => '4027',
                    'mobileNumber' => '09181200002',
                    'email' => 'maria.reyes@student.ccs.edu.ph',
                    'password' => Hash::make('Student@12345'),
                ],
                'student' => ['studentId' => '2024-00001', 'program' => 'BS Information Technology', 'yearLevel' => 2, 'unitsTaken' => 45, 'unitsLeft' => 90, 'dateEnrolled' => '2024-08-12', 'gpa' => 1.75, 'status' => 'Active'],
                'extraCurriculars' => [
                    ['activity' => 'Student Government Officer', 'role' => 'Secretary', 'organization' => 'CCS Student Council', 'startDate' => '2024-09-01', 'endDate' => '2025-08-31'],
                    ['activity' => 'Dance Troupe - Cultural Night', 'role' => 'Lead Performer', 'organization' => 'CCS Dance Club', 'startDate' => '2025-02-01', 'endDate' => '2025-03-15'],
                ],
                'awards' => [],
                'violations' => [],
                'eduBackground' => [
                    ['schoolUniversity' => 'Calamba National High School', 'startYear' => 2020, 'graduateYear' => 2024, 'type' => 'Senior High School', 'award' => null],
                ],
                'sem1Grades' => [1.75, 1.75, 2.00, 1.50, 1.75],
                'skills' => [
                    ['name' => 'Java Programming', 'isAcademic' => true],
                    ['name' => 'Web Development', 'isAcademic' => true],
                    ['name' => 'MySQL Database', 'isAcademic' => true],
                    ['name' => 'Leadership', 'isAcademic' => false],
                    ['name' => 'Public Speaking', 'isAcademic' => false],
                ],
            ],
            [
                'user' => [
                    'name' => 'Jose Emmanuel Briones Cruz',
                    'firstName' => 'Jose Emmanuel',
                    'lastName' => 'Cruz',
                    'middleName' => 'Briones',
                    'age' => 21,
                    'birthDate' => '2004-11-10',
                    'birthProvince' => 'Batangas',
                    'subdivision' => 'Richwood Homes',
                    'street' => 'Mango St.',
                    'barangay' => 'Bolbok',
                    'city' => 'Batangas City',
                    'province' => 'Batangas',
                    'postalCode' => '4200',
                    'mobileNumber' => '09271200003',
                    'email' => 'jose.cruz@student.ccs.edu.ph',
                    'password' => Hash::make('Student@12345'),
                ],
                'student' => ['studentId' => '2023-00001', 'program' => 'BS Information Technology', 'yearLevel' => 3, 'unitsTaken' => 82, 'unitsLeft' => 53, 'dateEnrolled' => '2023-08-16', 'gpa' => 2.25, 'status' => 'Active'],
                'extraCurriculars' => [
                    ['activity' => 'Basketball Varsity Team', 'role' => 'Player', 'organization' => 'CCS Sports Club', 'startDate' => '2023-09-01', 'endDate' => null],
                ],
                'awards' => [],
                'violations' => [
                    ['title' => 'Academic Dishonesty', 'violationDate' => '2024-03-10', 'description' => 'Submitted a plagiarized lab report in Programming 2. Student was issued a written warning and required to resubmit the report.'],
                ],
                'eduBackground' => [
                    ['schoolUniversity' => 'San Jose National High School', 'startYear' => 2019, 'graduateYear' => 2023, 'type' => 'Senior High School', 'award' => null],
                ],
                'sem1Grades' => [2.25, 2.00, 2.25, 2.50, 2.25],
                'skills' => [
                    ['name' => 'PHP and Laravel', 'isAcademic' => true],
                    ['name' => 'JavaScript', 'isAcademic' => true],
                    ['name' => 'Critical Thinking', 'isAcademic' => false],
                    ['name' => 'Basketball', 'isAcademic' => false],
                ],
            ],
            [
                'user' => [
                    'name' => 'Ana Luisa Guzman Garcia',
                    'firstName' => 'Ana Luisa',
                    'lastName' => 'Garcia',
                    'middleName' => 'Guzman',
                    'age' => 22,
                    'birthDate' => '2003-05-28',
                    'birthProvince' => 'Laguna',
                    'subdivision' => 'BF Resort Village',
                    'street' => 'Camia St.',
                    'barangay' => 'Almanza Uno',
                    'city' => 'Las Piñas',
                    'province' => 'Metro Manila',
                    'postalCode' => '1750',
                    'mobileNumber' => '09361200004',
                    'email' => 'ana.garcia@student.ccs.edu.ph',
                    'password' => Hash::make('Student@12345'),
                ],
                'student' => ['studentId' => '2022-00001', 'program' => 'BS Information Technology', 'yearLevel' => 4, 'unitsTaken' => 117, 'unitsLeft' => 18, 'dateEnrolled' => '2022-08-15', 'gpa' => 1.25, 'status' => 'Active'],
                'extraCurriculars' => [
                    ['activity' => 'Research Team Member', 'role' => 'Lead Researcher', 'organization' => 'CCS Research Society', 'startDate' => '2024-06-01', 'endDate' => null],
                    ['activity' => 'Hackathon 2024', 'role' => 'Team Leader', 'organization' => 'Google Developer Student Club', 'startDate' => '2024-10-15', 'endDate' => '2024-10-16'],
                ],
                'awards' => [
                    ['title' => "Summa Cum Laude Candidate 2025-2026", 'awardingDate' => '2026-03-01', 'awardingOrganization' => 'College of Computing Studies', 'awardingLocation' => 'CCS Auditorium, Laguna'],
                    ['title' => 'Best Capstone Project - 1st Place', 'awardingDate' => '2026-02-15', 'awardingOrganization' => 'CCS Annual Research Symposium', 'awardingLocation' => 'CCS Hall, Laguna'],
                    ["title" => "Dean's List - Four Consecutive Semesters", 'awardingDate' => '2026-01-10', 'awardingOrganization' => 'College of Computing Studies', 'awardingLocation' => 'CCS, Laguna'],
                ],
                'violations' => [],
                'eduBackground' => [
                    ['schoolUniversity' => 'Las Piñas National High School', 'startYear' => 2018, 'graduateYear' => 2022, 'type' => 'Senior High School', 'award' => 'With Highest Honors'],
                ],
                'sem1Grades' => [1.25, 1.00, 1.25, 1.25, 1.50],
                'skills' => [
                    ['name' => 'Python and Machine Learning', 'isAcademic' => true],
                    ['name' => 'React.js', 'isAcademic' => true],
                    ['name' => 'Research and Technical Writing', 'isAcademic' => true],
                    ['name' => 'Leadership', 'isAcademic' => false],
                    ['name' => 'Project Management', 'isAcademic' => false],
                ],
            ],

            /* ── BSIT (cont.) ── */
            [
                'user' => [
                    'name' => 'Kristine Marie Domingo Villanueva',
                    'firstName' => 'Kristine Marie',
                    'lastName' => 'Villanueva',
                    'middleName' => 'Domingo',
                    'age' => 20,
                    'birthDate' => '2005-09-14',
                    'birthProvince' => 'Laguna',
                    'subdivision' => 'Greenfield District',
                    'street' => 'Acacia Lane',
                    'barangay' => 'Tagapo',
                    'city' => 'Santa Rosa',
                    'province' => 'Laguna',
                    'postalCode' => '4026',
                    'mobileNumber' => '09171200005',
                    'email' => 'kristine.villanueva@student.ccs.edu.ph',
                    'password' => Hash::make('Student@12345'),
                ],
                'student' => ['studentId' => '2024-00002', 'program' => 'BS Information Technology', 'yearLevel' => 2, 'unitsTaken' => 48, 'unitsLeft' => 87, 'dateEnrolled' => '2024-08-12', 'gpa' => 1.50, 'status' => 'Active'],
                'extraCurriculars' => [
                    ['activity' => 'Debate Club - Intercollegiate Competition', 'role' => 'Debater', 'organization' => 'CCS Debate Club', 'startDate' => '2024-10-01', 'endDate' => '2024-11-30'],
                ],
                'awards' => [
                    ['title' => "Dean's List - 2nd Semester 2024-2025", 'awardingDate' => '2025-06-15', 'awardingOrganization' => 'College of Computing Studies', 'awardingLocation' => 'CCS Hall, Laguna'],
                ],
                'violations' => [],
                'eduBackground' => [
                    ['schoolUniversity' => 'St. Rose of Lima Academy', 'startYear' => 2020, 'graduateYear' => 2024, 'type' => 'Senior High School', 'award' => 'With Honors'],
                ],
                'sem1Grades' => [1.50, 1.50, 1.75, 1.25, 1.50],
                'skills' => [
                    ['name' => 'HTML, CSS, and JavaScript', 'isAcademic' => true],
                    ['name' => 'Node.js', 'isAcademic' => true],
                    ['name' => 'UI/UX Design with Figma', 'isAcademic' => true],
                    ['name' => 'Debate and Public Speaking', 'isAcademic' => false],
                    ['name' => 'Analytical Thinking', 'isAcademic' => false],
                ],
            ],

            /* ── BSCS ── */
            [
                'user' => [
                    'name' => 'Mark Anthony Tolentino Torres',
                    'firstName' => 'Mark Anthony',
                    'lastName' => 'Torres',
                    'middleName' => 'Tolentino',
                    'age' => 19,
                    'birthDate' => '2006-01-07',
                    'birthProvince' => 'Cavite',
                    'subdivision' => 'Breezemont Subd.',
                    'street' => 'Narra St.',
                    'barangay' => 'Tejero',
                    'city' => 'General Trias',
                    'province' => 'Cavite',
                    'postalCode' => '4107',
                    'mobileNumber' => '09181200006',
                    'email' => 'mark.torres@student.ccs.edu.ph',
                    'password' => Hash::make('Student@12345'),
                ],
                'student' => ['studentId' => '2025-00002', 'program' => 'BS Computer Science', 'yearLevel' => 1, 'unitsTaken' => 16, 'unitsLeft' => 119, 'dateEnrolled' => '2025-08-15', 'gpa' => 1.75, 'status' => 'Active'],
                'extraCurriculars' => [
                    ['activity' => 'Chess Club Regular Member', 'role' => 'Member', 'organization' => 'CCS Chess Club', 'startDate' => '2025-09-01', 'endDate' => null],
                ],
                'awards' => [],
                'violations' => [],
                'eduBackground' => [
                    ['schoolUniversity' => 'General Trias Senior High School', 'startYear' => 2021, 'graduateYear' => 2025, 'type' => 'Senior High School', 'award' => null],
                ],
                'sem1Grades' => [1.75, 2.00, 1.75, 1.75, 2.00],
                'skills' => [
                    ['name' => 'C++ Programming', 'isAcademic' => true],
                    ['name' => 'Python Programming', 'isAcademic' => true],
                    ['name' => 'Data Structures', 'isAcademic' => true],
                    ['name' => 'Chess', 'isAcademic' => false],
                    ['name' => 'Mathematical Reasoning', 'isAcademic' => false],
                ],
            ],
            [
                'user' => [
                    'name' => 'Renaldo Christian Dela Cruz',
                    'firstName' => 'Renaldo Christian',
                    'lastName' => 'Dela Cruz',
                    'middleName' => 'Mendez',
                    'age' => 21,
                    'birthDate' => '2004-08-03',
                    'birthProvince' => 'Quezon',
                    'subdivision' => 'Bel Air Subd.',
                    'street' => 'Niyog St.',
                    'barangay' => 'Iyam',
                    'city' => 'Lucena City',
                    'province' => 'Quezon',
                    'postalCode' => '4301',
                    'mobileNumber' => '09261200007',
                    'email' => 'renaldo.delacruz@student.ccs.edu.ph',
                    'password' => Hash::make('Student@12345'),
                ],
                'student' => ['studentId' => '2023-00002', 'program' => 'BS Computer Science', 'yearLevel' => 3, 'unitsTaken' => 78, 'unitsLeft' => 57, 'dateEnrolled' => '2023-08-16', 'gpa' => 2.00, 'status' => 'Active'],
                'extraCurriculars' => [
                    ['activity' => 'Basketball Varsity Team', 'role' => 'Player', 'organization' => 'CCS Sports Club', 'startDate' => '2023-09-01', 'endDate' => null],
                    ['activity' => 'Coding Bootcamp Participant', 'role' => 'Participant', 'organization' => 'CCS Developer Hub', 'startDate' => '2025-01-10', 'endDate' => '2025-01-20'],
                ],
                'awards' => [],
                'violations' => [],
                'eduBackground' => [
                    ['schoolUniversity' => 'Lucena Senior High School', 'startYear' => 2019, 'graduateYear' => 2023, 'type' => 'Senior High School', 'award' => null],
                ],
                'sem1Grades' => [2.00, 2.00, 2.25, 1.75, 2.00],
                'skills' => [
                    ['name' => 'Java Programming', 'isAcademic' => true],
                    ['name' => 'Algorithm Design', 'isAcademic' => true],
                    ['name' => 'Operating Systems', 'isAcademic' => true],
                    ['name' => 'Basketball', 'isAcademic' => false],
                    ['name' => 'Team Collaboration', 'isAcademic' => false],
                ],
            ],
            [
                'user' => [
                    'name' => 'Patricia Anne Manalo Mendoza',
                    'firstName' => 'Patricia Anne',
                    'lastName' => 'Mendoza',
                    'middleName' => 'Manalo',
                    'age' => 22,
                    'birthDate' => '2003-12-19',
                    'birthProvince' => 'Laguna',
                    'subdivision' => 'Portofino Heights',
                    'street' => 'Via Roma',
                    'barangay' => 'Aldana',
                    'city' => 'Las Piñas',
                    'province' => 'Metro Manila',
                    'postalCode' => '1748',
                    'mobileNumber' => '09361200008',
                    'email' => 'patricia.mendoza@student.ccs.edu.ph',
                    'password' => Hash::make('Student@12345'),
                ],
                'student' => ['studentId' => '2022-00002', 'program' => 'BS Computer Science', 'yearLevel' => 4, 'unitsTaken' => 113, 'unitsLeft' => 22, 'dateEnrolled' => '2022-08-15', 'gpa' => 1.75, 'status' => 'Active'],
                'extraCurriculars' => [
                    ['activity' => 'AWS Cloud Practitioner Study Group', 'role' => 'Facilitator', 'organization' => 'CCS Developer Hub', 'startDate' => '2025-03-01', 'endDate' => null],
                ],
                'awards' => [
                    ['title' => 'Best Thesis Defense - Computer Science Track', 'awardingDate' => '2026-03-20', 'awardingOrganization' => 'CCS Research and Development Office', 'awardingLocation' => 'CCS Auditorium, Laguna'],
                ],
                'violations' => [],
                'eduBackground' => [
                    ['schoolUniversity' => 'Las Piñas Girls High School', 'startYear' => 2018, 'graduateYear' => 2022, 'type' => 'Senior High School', 'award' => 'With High Honors'],
                ],
                'sem1Grades' => [1.75, 1.75, 1.50, 2.00, 1.75],
                'skills' => [
                    ['name' => 'Python and TensorFlow', 'isAcademic' => true],
                    ['name' => 'Machine Learning', 'isAcademic' => true],
                    ['name' => 'Research Methodology', 'isAcademic' => true],
                    ['name' => 'Technical Writing', 'isAcademic' => false],
                    ['name' => 'Presentation Skills', 'isAcademic' => false],
                ],
            ],
            [
                'user' => [
                    'name' => 'Emmanuel Xavier Lim',
                    'firstName' => 'Emmanuel Xavier',
                    'lastName' => 'Lim',
                    'middleName' => 'Chan',
                    'age' => 20,
                    'birthDate' => '2005-04-08',
                    'birthProvince' => 'Metro Manila',
                    'subdivision' => 'Mandala Park',
                    'street' => 'Shaw Blvd.',
                    'barangay' => 'Highway Hills',
                    'city' => 'Mandaluyong',
                    'province' => 'Metro Manila',
                    'postalCode' => '1550',
                    'mobileNumber' => '09171200009',
                    'email' => 'emmanuel.lim@student.ccs.edu.ph',
                    'password' => Hash::make('Student@12345'),
                ],
                'student' => ['studentId' => '2024-00003', 'program' => 'BS Computer Science', 'yearLevel' => 2, 'unitsTaken' => 42, 'unitsLeft' => 93, 'dateEnrolled' => '2024-08-12', 'gpa' => 1.75, 'status' => 'Active'],
                'extraCurriculars' => [
                    ['activity' => 'Online Journalism and Tech Blog', 'role' => 'Editor-in-Chief', 'organization' => 'CCS Tech Writers Guild', 'startDate' => '2024-10-01', 'endDate' => null],
                ],
                'awards' => [],
                'violations' => [],
                'eduBackground' => [
                    ['schoolUniversity' => 'Lourdes School of Mandaluyong', 'startYear' => 2020, 'graduateYear' => 2024, 'type' => 'Senior High School', 'award' => null],
                ],
                'sem1Grades' => [1.75, 2.00, 1.75, 2.00, 1.75],
                'skills' => [
                    ['name' => 'JavaScript and React', 'isAcademic' => true],
                    ['name' => 'Cybersecurity Fundamentals', 'isAcademic' => true],
                    ['name' => 'Content Writing', 'isAcademic' => false],
                    ['name' => 'Communication', 'isAcademic' => false],
                    ['name' => 'Creative Thinking', 'isAcademic' => false],
                ],
            ],
            [
                'user' => [
                    'name' => 'Sandra Beth Ramos Ocampo',
                    'firstName' => 'Sandra Beth',
                    'lastName' => 'Ocampo',
                    'middleName' => 'Ramos',
                    'age' => 21,
                    'birthDate' => '2004-06-30',
                    'birthProvince' => 'Laguna',
                    'subdivision' => 'Camella Sta. Rosa',
                    'street' => 'Santan St.',
                    'barangay' => 'Don Jose',
                    'city' => 'Santa Rosa',
                    'province' => 'Laguna',
                    'postalCode' => '4026',
                    'mobileNumber' => '09181200010',
                    'email' => 'sandra.ocampo@student.ccs.edu.ph',
                    'password' => Hash::make('Student@12345'),
                ],
                'student' => ['studentId' => '2023-00003', 'program' => 'BS Computer Science', 'yearLevel' => 3, 'unitsTaken' => 76, 'unitsLeft' => 59, 'dateEnrolled' => '2023-08-16', 'gpa' => 2.25, 'status' => 'Active'],
                'extraCurriculars' => [],
                'awards' => [],
                'violations' => [
                    ['title' => 'Excessive Tardiness', 'violationDate' => '2025-02-05', 'description' => 'Student accumulated more than 10 late arrivals in Data Structures class during the 1st semester. Verbal warning issued by the department chair.'],
                ],
                'eduBackground' => [
                    ['schoolUniversity' => 'Sta. Rosa Science and Technology High School', 'startYear' => 2019, 'graduateYear' => 2023, 'type' => 'Senior High School', 'award' => null],
                ],
                'sem1Grades' => [2.25, 2.50, 2.25, 2.00, 2.25],
                'skills' => [
                    ['name' => 'Java Programming', 'isAcademic' => true],
                    ['name' => 'Database Design', 'isAcademic' => true],
                    ['name' => 'Linux Administration', 'isAcademic' => true],
                    ['name' => 'Time Management', 'isAcademic' => false],
                    ['name' => 'Perseverance', 'isAcademic' => false],
                ],
            ],

            /* ── BS Computer Engineering ── */
            [
                'user' => [
                    'name' => 'Ricardo Jose Bautista',
                    'firstName' => 'Ricardo Jose',
                    'lastName' => 'Bautista',
                    'middleName' => 'Natividad',
                    'age' => 19,
                    'birthDate' => '2006-02-20',
                    'birthProvince' => 'Batangas',
                    'subdivision' => 'Villa Angela Subd.',
                    'street' => 'Calachuchi St.',
                    'barangay' => 'Alangilan',
                    'city' => 'Batangas City',
                    'province' => 'Batangas',
                    'postalCode' => '4200',
                    'mobileNumber' => '09271200011',
                    'email' => 'ricardo.bautista@student.ccs.edu.ph',
                    'password' => Hash::make('Student@12345'),
                ],
                'student' => ['studentId' => '2025-00003', 'program' => 'BS Computer Engineering', 'yearLevel' => 1, 'unitsTaken' => 18, 'unitsLeft' => 117, 'dateEnrolled' => '2025-08-15', 'gpa' => 2.00, 'status' => 'Active'],
                'extraCurriculars' => [
                    ['activity' => 'Robotics Club - Build Season', 'role' => 'Hardware Member', 'organization' => 'CCS Robotics Club', 'startDate' => '2025-09-01', 'endDate' => null],
                ],
                'awards' => [],
                'violations' => [],
                'eduBackground' => [
                    ['schoolUniversity' => 'Batangas National High School', 'startYear' => 2021, 'graduateYear' => 2025, 'type' => 'Senior High School', 'award' => null],
                ],
                'sem1Grades' => [2.00, 2.00, 2.25, 1.75, 2.00],
                'skills' => [
                    ['name' => 'C Programming', 'isAcademic' => true],
                    ['name' => 'Electronics Fundamentals', 'isAcademic' => true],
                    ['name' => 'Circuit Analysis', 'isAcademic' => true],
                    ['name' => 'Technical Drawing', 'isAcademic' => false],
                    ['name' => 'Mathematical Aptitude', 'isAcademic' => false],
                ],
            ],
            [
                'user' => [
                    'name' => 'Jennifer Lynn Aquino',
                    'firstName' => 'Jennifer Lynn',
                    'lastName' => 'Aquino',
                    'middleName' => 'Soriano',
                    'age' => 20,
                    'birthDate' => '2005-10-11',
                    'birthProvince' => 'Laguna',
                    'subdivision' => 'Golden Meadows',
                    'street' => 'Cypress St.',
                    'barangay' => 'Mayapa',
                    'city' => 'Calamba',
                    'province' => 'Laguna',
                    'postalCode' => '4027',
                    'mobileNumber' => '09361200012',
                    'email' => 'jennifer.aquino@student.ccs.edu.ph',
                    'password' => Hash::make('Student@12345'),
                ],
                'student' => ['studentId' => '2024-00004', 'program' => 'BS Computer Engineering', 'yearLevel' => 2, 'unitsTaken' => 44, 'unitsLeft' => 91, 'dateEnrolled' => '2024-08-12', 'gpa' => 1.75, 'status' => 'Active'],
                'extraCurriculars' => [
                    ['activity' => 'Engineering Society Volunteer', 'role' => 'Committee Member', 'organization' => 'CCS Engineering Society', 'startDate' => '2024-09-15', 'endDate' => null],
                ],
                'awards' => [],
                'violations' => [],
                'eduBackground' => [
                    ['schoolUniversity' => 'Calamba National High School', 'startYear' => 2020, 'graduateYear' => 2024, 'type' => 'Senior High School', 'award' => 'With Honors'],
                ],
                'sem1Grades' => [1.75, 2.00, 1.75, 2.00, 1.75],
                'skills' => [
                    ['name' => 'Embedded C Programming', 'isAcademic' => true],
                    ['name' => 'Microcontroller Programming', 'isAcademic' => true],
                    ['name' => 'PCB Design Basics', 'isAcademic' => true],
                    ['name' => 'Teamwork', 'isAcademic' => false],
                    ['name' => 'Problem Solving', 'isAcademic' => false],
                ],
            ],
            [
                'user' => [
                    'name' => 'Carlo Antonio Estrada',
                    'firstName' => 'Carlo Antonio',
                    'lastName' => 'Estrada',
                    'middleName' => 'Perez',
                    'age' => 21,
                    'birthDate' => '2004-04-05',
                    'birthProvince' => 'Cavite',
                    'subdivision' => 'Fil-Estate Subd.',
                    'street' => 'Angsana Drive',
                    'barangay' => 'Bancal',
                    'city' => 'Carmona',
                    'province' => 'Cavite',
                    'postalCode' => '4116',
                    'mobileNumber' => '09171200013',
                    'email' => 'carlo.estrada@student.ccs.edu.ph',
                    'password' => Hash::make('Student@12345'),
                ],
                'student' => ['studentId' => '2023-00004', 'program' => 'BS Computer Engineering', 'yearLevel' => 3, 'unitsTaken' => 74, 'unitsLeft' => 61, 'dateEnrolled' => '2023-08-16', 'gpa' => 2.50, 'status' => 'Active'],
                'extraCurriculars' => [],
                'awards' => [],
                'violations' => [
                    ['title' => 'Disruptive Behavior', 'violationDate' => '2024-09-22', 'description' => 'Student was found using a mobile phone during a long quiz in Circuit Theory, in direct violation of the no-gadget policy during examinations. Issued a written warning.'],
                ],
                'eduBackground' => [
                    ['schoolUniversity' => 'Carmona National High School', 'startYear' => 2019, 'graduateYear' => 2023, 'type' => 'Senior High School', 'award' => null],
                ],
                'sem1Grades' => [2.50, 2.50, 2.75, 2.25, 2.50],
                'skills' => [
                    ['name' => 'VHDL and Digital Logic', 'isAcademic' => true],
                    ['name' => 'Computer Architecture', 'isAcademic' => true],
                    ['name' => 'Hardware Troubleshooting', 'isAcademic' => true],
                    ['name' => 'Resilience', 'isAcademic' => false],
                ],
            ],
            [
                'user' => [
                    'name' => 'Sheila Marie Castillo',
                    'firstName' => 'Sheila Marie',
                    'lastName' => 'Castillo',
                    'middleName' => 'Dimaculangan',
                    'age' => 19,
                    'birthDate' => '2006-08-17',
                    'birthProvince' => 'Laguna',
                    'subdivision' => 'Laguna Bel Air 2',
                    'street' => 'Magnolia Blvd.',
                    'barangay' => 'Market Area',
                    'city' => 'Biñan',
                    'province' => 'Laguna',
                    'postalCode' => '4024',
                    'mobileNumber' => '09181200014',
                    'email' => 'sheila.castillo@student.ccs.edu.ph',
                    'password' => Hash::make('Student@12345'),
                ],
                'student' => ['studentId' => '2025-00004', 'program' => 'BS Computer Engineering', 'yearLevel' => 1, 'unitsTaken' => 17, 'unitsLeft' => 118, 'dateEnrolled' => '2025-08-15', 'gpa' => 1.75, 'status' => 'Active'],
                'extraCurriculars' => [
                    ['activity' => 'CCS Orientation Program', 'role' => 'Volunteer Usher', 'organization' => 'CCS Student Council', 'startDate' => '2025-08-10', 'endDate' => '2025-08-15'],
                ],
                'awards' => [],
                'violations' => [],
                'eduBackground' => [
                    ['schoolUniversity' => 'Biñan National High School', 'startYear' => 2021, 'graduateYear' => 2025, 'type' => 'Senior High School', 'award' => null],
                ],
                'sem1Grades' => [1.75, 2.00, 1.75, 1.75, 2.00],
                'skills' => [
                    ['name' => 'C Programming', 'isAcademic' => true],
                    ['name' => 'Physics and Electronics', 'isAcademic' => true],
                    ['name' => 'Technical Drawing', 'isAcademic' => true],
                    ['name' => 'Curiosity and Creativity', 'isAcademic' => false],
                    ['name' => 'Adaptability', 'isAcademic' => false],
                ],
            ],
            [
                'user' => [
                    'name' => 'Daniel Luis Ramos',
                    'firstName' => 'Daniel Luis',
                    'lastName' => 'Ramos',
                    'middleName' => 'Francisco',
                    'age' => 22,
                    'birthDate' => '2003-03-25',
                    'birthProvince' => 'Laguna',
                    'subdivision' => 'Maharlika Village',
                    'street' => 'Kamagong St.',
                    'barangay' => 'Bagong Silang',
                    'city' => 'San Pedro',
                    'province' => 'Laguna',
                    'postalCode' => '4023',
                    'mobileNumber' => '09261200015',
                    'email' => 'daniel.ramos@student.ccs.edu.ph',
                    'password' => Hash::make('Student@12345'),
                ],
                'student' => ['studentId' => '2022-00003', 'program' => 'BS Computer Engineering', 'yearLevel' => 4, 'unitsTaken' => 112, 'unitsLeft' => 23, 'dateEnrolled' => '2022-08-15', 'gpa' => 2.00, 'status' => 'Active'],
                'extraCurriculars' => [
                    ['activity' => 'IoT Project Showcase', 'role' => 'Team Lead', 'organization' => 'CCS Robotics Club', 'startDate' => '2025-11-01', 'endDate' => '2025-11-30'],
                ],
                'awards' => [],
                'violations' => [],
                'eduBackground' => [
                    ['schoolUniversity' => 'San Pedro National High School', 'startYear' => 2018, 'graduateYear' => 2022, 'type' => 'Senior High School', 'award' => null],
                ],
                'sem1Grades' => [2.00, 2.00, 2.25, 1.75, 2.25],
                'skills' => [
                    ['name' => 'IoT Development', 'isAcademic' => true],
                    ['name' => 'Embedded Systems', 'isAcademic' => true],
                    ['name' => 'MATLAB', 'isAcademic' => true],
                    ['name' => 'Project Management', 'isAcademic' => false],
                    ['name' => 'Leadership', 'isAcademic' => false],
                ],
            ],
            [
                'user' => [
                    'name' => 'Angelica Faith Navarro',
                    'firstName' => 'Angelica Faith',
                    'lastName' => 'Navarro',
                    'middleName' => 'Espino',
                    'age' => 22,
                    'birthDate' => '2003-01-03',
                    'birthProvince' => 'Laguna',
                    'subdivision' => 'Laguna Heights',
                    'street' => 'Rosal St.',
                    'barangay' => 'Tubigan',
                    'city' => 'Biñan',
                    'province' => 'Laguna',
                    'postalCode' => '4024',
                    'mobileNumber' => '09171200016',
                    'email' => 'angelica.navarro@student.ccs.edu.ph',
                    'password' => Hash::make('Student@12345'),
                ],
                'student' => ['studentId' => '2022-00004', 'program' => 'BS Computer Engineering', 'yearLevel' => 4, 'unitsTaken' => 118, 'unitsLeft' => 17, 'dateEnrolled' => '2022-08-15', 'gpa' => 1.50, 'status' => 'Active'],
                'extraCurriculars' => [
                    ['activity' => 'Google Developer Student Club Lead', 'role' => 'Club President', 'organization' => 'Google Developer Student Club', 'startDate' => '2024-08-01', 'endDate' => '2025-07-31'],
                ],
                'awards' => [
                    ['title' => 'Outstanding Student Leader Award', 'awardingDate' => '2025-04-10', 'awardingOrganization' => 'College of Computing Studies', 'awardingLocation' => 'CCS Hall, Laguna'],
                    ['title' => 'National Hackathon 2nd Place', 'awardingDate' => '2025-02-20', 'awardingOrganization' => 'Philippine Software Industry Association', 'awardingLocation' => 'SMX Convention Center, Manila'],
                ],
                'violations' => [],
                'eduBackground' => [
                    ['schoolUniversity' => 'Biñan City National High School', 'startYear' => 2018, 'graduateYear' => 2022, 'type' => 'Senior High School', 'award' => 'With High Honors'],
                ],
                'sem1Grades' => [1.50, 1.50, 1.75, 1.25, 1.50],
                'skills' => [
                    ['name' => 'PCB Design', 'isAcademic' => true],
                    ['name' => 'Python for Hardware', 'isAcademic' => true],
                    ['name' => 'IoT System Architecture', 'isAcademic' => true],
                    ['name' => 'Public Speaking', 'isAcademic' => false],
                    ['name' => 'Community Leadership', 'isAcademic' => false],
                ],
            ],
            [
                'user' => [
                    'name' => 'Michael John Fernandez',
                    'firstName' => 'Michael John',
                    'lastName' => 'Fernandez',
                    'middleName' => 'Santiago',
                    'age' => 20,
                    'birthDate' => '2005-05-09',
                    'birthProvince' => 'Metro Manila',
                    'subdivision' => 'Valle Verde',
                    'street' => 'Green Valley Rd.',
                    'barangay' => 'Ugong',
                    'city' => 'Pasig',
                    'province' => 'Metro Manila',
                    'postalCode' => '1604',
                    'mobileNumber' => '09181200017',
                    'email' => 'michael.fernandez@student.ccs.edu.ph',
                    'password' => Hash::make('Student@12345'),
                ],
                'student' => ['studentId' => '2024-00005', 'program' => 'BS Computer Science', 'yearLevel' => 2, 'unitsTaken' => 40, 'unitsLeft' => 95, 'dateEnrolled' => '2024-08-12', 'gpa' => 2.50, 'status' => 'Active'],
                'extraCurriculars' => [],
                'awards' => [],
                'violations' => [
                    ['title' => 'Excessive Absences', 'violationDate' => '2025-04-03', 'description' => 'Student accumulated 12 absences in Data Structures class, exceeding the allowable 20% absence threshold. Placed on probationary status for the next semester.'],
                ],
                'eduBackground' => [
                    ['schoolUniversity' => 'Pasig City Science High School', 'startYear' => 2020, 'graduateYear' => 2024, 'type' => 'Senior High School', 'award' => null],
                ],
                'sem1Grades' => [2.50, 2.75, 2.50, 2.25, 2.50],
                'skills' => [
                    ['name' => 'Java Programming', 'isAcademic' => true],
                    ['name' => 'Database Fundamentals', 'isAcademic' => true],
                    ['name' => 'Linux Command Line', 'isAcademic' => true],
                    ['name' => 'Self-Improvement', 'isAcademic' => false],
                ],
            ],
            [
                'user' => [
                    'name' => 'Rosario Kathleen Morales',
                    'firstName' => 'Rosario Kathleen',
                    'lastName' => 'Morales',
                    'middleName' => 'De Guzman',
                    'age' => 20,
                    'birthDate' => '2005-11-28',
                    'birthProvince' => 'Laguna',
                    'subdivision' => 'Sunrise Valley Homes',
                    'street' => 'Jasmine St.',
                    'barangay' => 'Mamatid',
                    'city' => 'Cabuyao',
                    'province' => 'Laguna',
                    'postalCode' => '4025',
                    'mobileNumber' => '09271200018',
                    'email' => 'rosario.morales@student.ccs.edu.ph',
                    'password' => Hash::make('Student@12345'),
                ],
                'student' => ['studentId' => '2024-00006', 'program' => 'BS Computer Science', 'yearLevel' => 2, 'unitsTaken' => 43, 'unitsLeft' => 92, 'dateEnrolled' => '2024-08-12', 'gpa' => 1.75, 'status' => 'Active'],
                'extraCurriculars' => [
                    ['activity' => 'Quiz Bee - Computer Fundamentals', 'role' => 'Contestant', 'organization' => 'CCS Academic Team', 'startDate' => '2025-03-10', 'endDate' => '2025-03-11'],
                ],
                'awards' => [],
                'violations' => [],
                'eduBackground' => [
                    ['schoolUniversity' => 'Cabuyao National High School', 'startYear' => 2020, 'graduateYear' => 2024, 'type' => 'Senior High School', 'award' => 'With Honors'],
                ],
                'sem1Grades' => [1.75, 2.00, 1.75, 2.00, 1.75],
                'skills' => [
                    ['name' => 'Python and Data Analysis', 'isAcademic' => true],
                    ['name' => 'Statistics', 'isAcademic' => true],
                    ['name' => 'SQL Querying', 'isAcademic' => true],
                    ['name' => 'Quiz Bowl and Academics', 'isAcademic' => false],
                    ['name' => 'Attention to Detail', 'isAcademic' => false],
                ],
            ],
            [
                'user' => [
                    'name' => 'Dennis Paul Soriano',
                    'firstName' => 'Dennis Paul',
                    'lastName' => 'Soriano',
                    'middleName' => 'Evangelista',
                    'age' => 21,
                    'birthDate' => '2004-07-14',
                    'birthProvince' => 'Batangas',
                    'subdivision' => 'Bukal Hills',
                    'street' => 'Sampaguita Lane',
                    'barangay' => 'Sta. Clara',
                    'city' => 'Sto. Tomas',
                    'province' => 'Batangas',
                    'postalCode' => '4234',
                    'mobileNumber' => '09361200019',
                    'email' => 'dennis.soriano@student.ccs.edu.ph',
                    'password' => Hash::make('Student@12345'),
                ],
                'student' => ['studentId' => '2023-00005', 'program' => 'BS Computer Engineering', 'yearLevel' => 3, 'unitsTaken' => 79, 'unitsLeft' => 56, 'dateEnrolled' => '2023-08-16', 'gpa' => 2.00, 'status' => 'Active'],
                'extraCurriculars' => [
                    ['activity' => 'Robotics Club Project Lead', 'role' => 'Project Lead', 'organization' => 'CCS Robotics Club', 'startDate' => '2024-08-01', 'endDate' => null],
                    ['activity' => 'Technical Workshop Facilitator', 'role' => 'Facilitator', 'organization' => 'CCS Engineering Society', 'startDate' => '2025-02-15', 'endDate' => '2025-02-16'],
                ],
                'awards' => [],
                'violations' => [],
                'eduBackground' => [
                    ['schoolUniversity' => 'Sto. Tomas National High School', 'startYear' => 2019, 'graduateYear' => 2023, 'type' => 'Senior High School', 'award' => null],
                ],
                'sem1Grades' => [2.00, 2.00, 2.25, 2.00, 2.25],
                'skills' => [
                    ['name' => 'Arduino and Robotics', 'isAcademic' => true],
                    ['name' => 'Embedded C', 'isAcademic' => true],
                    ['name' => '3D Modeling (Tinkercad)', 'isAcademic' => true],
                    ['name' => 'Workshop Facilitation', 'isAcademic' => false],
                    ['name' => 'Technical Presentation', 'isAcademic' => false],
                ],
            ],
            [
                'user' => [
                    'name' => 'Maricel Grace Padilla',
                    'firstName' => 'Maricel Grace',
                    'lastName' => 'Padilla',
                    'middleName' => 'Reyes',
                    'age' => 19,
                    'birthDate' => '2006-09-02',
                    'birthProvince' => 'Laguna',
                    'subdivision' => 'Woodland Park Homes',
                    'street' => 'Acacia Drive',
                    'barangay' => 'Crisanto M. Delos Reyes',
                    'city' => 'San Pedro',
                    'province' => 'Laguna',
                    'postalCode' => '4023',
                    'mobileNumber' => '09171200020',
                    'email' => 'maricel.padilla@student.ccs.edu.ph',
                    'password' => Hash::make('Student@12345'),
                ],
                'student' => ['studentId' => '2025-00005', 'program' => 'BS Information Technology', 'yearLevel' => 1, 'unitsTaken' => 16, 'unitsLeft' => 119, 'dateEnrolled' => '2025-08-15', 'gpa' => 2.00, 'status' => 'Active'],
                'extraCurriculars' => [
                    ['activity' => 'CCS Theater Arts - Founding Member', 'role' => 'Actor', 'organization' => 'CCS Theater Arts Society', 'startDate' => '2025-10-01', 'endDate' => null],
                ],
                'awards' => [],
                'violations' => [],
                'eduBackground' => [
                    ['schoolUniversity' => 'San Pedro Relocation Center National High School', 'startYear' => 2021, 'graduateYear' => 2025, 'type' => 'Senior High School', 'award' => null],
                ],
                'sem1Grades' => [2.00, 2.25, 2.00, 1.75, 2.00],
                'skills' => [
                    ['name' => 'Python Basics', 'isAcademic' => true],
                    ['name' => 'HTML and Web Basics', 'isAcademic' => true],
                    ['name' => 'MS Office Suite', 'isAcademic' => false],
                    ['name' => 'Theater Arts and Acting', 'isAcademic' => false],
                    ['name' => 'Creativity and Design', 'isAcademic' => false],
                ],
            ],
        ];

        // Pre-fetch program and section data
        $programIds = DB::table('program')->pluck('id', 'name');
        $curricula = DB::table('curriculum')->pluck('id', 'programId');
        $sections = DB::table('section')
            ->where('academicYear', '2025-2026')
            ->where('sectionName', 'LIKE', '%-A')
            ->get()
            ->groupBy(fn($s) => $s->programId . '_' . $s->yearLevel . '_' . $s->semester);

        $studentSkillsMap = [];

        foreach ($studentData as $data) {
            $userId = DB::table('users')->insertGetId(array_merge($this->userFields($data['user']), [
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]));

            $programId = $programIds[$data['student']['program']];

            $studentId = DB::table('student')->insertGetId([
                'userId' => $userId,
                'studentId' => $data['student']['studentId'],
                'programId' => $programId,
                'yearLevel' => $data['student']['yearLevel'],
                'unitsTaken' => $data['student']['unitsTaken'],
                'unitsLeft' => $data['student']['unitsLeft'],
                'dateEnrolled' => $data['student']['dateEnrolled'],
                'gpa' => $data['student']['gpa'],
                'status' => $data['student']['status'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Student Program
            DB::table('student_program')->insert([
                'studentId' => $studentId,
                'programId' => $programId,
                'dateEnrolled' => $data['student']['dateEnrolled'],
                'dateLeft' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Student Section (semester 1 and 2 of their year level)
            foreach ([1, 2] as $sem) {
                $sectionKey = $programId . '_' . $data['student']['yearLevel'] . '_' . $sem;
                if (isset($sections[$sectionKey])) {
                    $section = $sections[$sectionKey]->first();
                    DB::table('student_section')->insert([
                        'studentId' => $studentId,
                        'sectionId' => $section->id,
                        'academicYear' => '2025-2026',
                        'semester' => $sem,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Grades (semester 1 only using provided grade values)
            $this->seedStudentGrades($studentId, $programId, $data['student']['yearLevel'], $data['sem1Grades'], $curricula, $sections);

            // Optional data
            foreach ($data['extraCurriculars'] as $ec) {
                DB::table('extra_curricular')->insert(array_merge($ec, [
                    'studentId' => $studentId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }

            foreach ($data['awards'] as $award) {
                DB::table('awards')->insert(array_merge($award, [
                    'userId' => $userId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }

            foreach ($data['violations'] as $violation) {
                DB::table('violation')->insert(array_merge($violation, [
                    'studentId' => $studentId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }

            foreach ($data['eduBackground'] as $edu) {
                DB::table('educational_background')->insert(array_merge($edu, [
                    'userId' => $userId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }

            $studentSkillsMap[$studentId] = $data['skills'];
        }

        $this->seedNormalizedSkills($studentSkillsMap);
    }

    private function seedNormalizedSkills(array $studentSkillsMap): void
    {
        // Collect every unique skill name → isAcademic across all students
        $uniqueSkills = [];
        foreach ($studentSkillsMap as $skills) {
            foreach ($skills as $s) {
                $uniqueSkills[$s['name']] = $s['isAcademic'];
            }
        }

        // Upsert master skills table
        foreach ($uniqueSkills as $name => $isAcademic) {
            DB::table('skills')->insertOrIgnore([
                'name' => $name,
                'isAcademic' => $isAcademic ? 1 : 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $skillIds = DB::table('skills')->pluck('id', 'name');

        // Build pivot rows
        $pivotRows = [];
        foreach ($studentSkillsMap as $studentId => $skills) {
            foreach ($skills as $s) {
                if (isset($skillIds[$s['name']])) {
                    $pivotRows[] = [
                        'studentId' => $studentId,
                        'skillId' => $skillIds[$s['name']],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        DB::table('student_skills')->insert($pivotRows);
    }

    private function userFields(array $data): array
    {
        unset($data['name']);
        return $data;
    }

    private function seedStudentGrades(
        int $studentId,
        int $programId,
        int $yearLevel,
        array $gradeValues,
        mixed $curricula,
        mixed $sections
    ): void {
        if (!isset($curricula[$programId])) {
            return;
        }

        $sectionKey = $programId . '_' . $yearLevel . '_1';
        if (!isset($sections[$sectionKey])) {
            return;
        }
        $section = $sections[$sectionKey]->first();

        $courses = DB::table('courses')
            ->where('curriculumId', $curricula[$programId])
            ->where('yearLevel', $yearLevel)
            ->where('semester', 1)
            ->get();

        $terms = ['preliminary', 'midterm', 'finals'];

        foreach ($courses as $index => $course) {
            $baseGrade = $gradeValues[$index] ?? 2.00;

            foreach ($terms as $term) {
                // Slight variation per term — prelim usually highest, finals consolidate
                $offset = match ($term) {
                    'preliminary' => 0.00,
                    'midterm' => 0.25,
                    'finals' => -0.25,
                    default => 0.00,
                };
                $grade = round(max(1.00, min(3.00, $baseGrade + $offset)), 2);
                $remarks = $grade <= 3.00 ? 'passed' : 'failed';

                DB::table('grades')->insert([
                    'studentId' => $studentId,
                    'sectionId' => $section->id,
                    'courseId' => $course->id,
                    'academicYear' => '2025-2026',
                    'semester' => 1,
                    'term' => $term,
                    'grade' => $grade,
                    'remarks' => $remarks,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}