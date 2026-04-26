<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LargeStudentSeeder extends Seeder
{
    // ── Name pools ──────────────────────────────────────────────────────────
    private array $firstNames = [
        'Juan Miguel','Maria Clara','Jose Emmanuel','Ana Luisa','Kristine Marie',
        'Mark Anthony','Renaldo Christian','Patricia Anne','Emmanuel Xavier','Sandra Beth',
        'Ricardo Jose','Jennifer Lynn','Carlo Antonio','Sheila Marie','Daniel Luis',
        'Angelica Faith','Michael John','Rosario Kathleen','Dennis Paul','Maricel Grace',
        'Angelo','Beatriz','Carlos','Diana','Eduardo','Fatima','Gabriel','Hannah',
        'Ivan','Julia','Karl','Lorraine','Manuel','Natalie','Oscar','Pamela',
        'Quincy','Regina','Samuel','Teresa','Ulysses','Vanessa','Walter','Ximena',
        'Yolanda','Zachary','Aldrin','Bernadette','Cesar','Delia','Efren','Florencia',
        'Gregorio','Herminia','Ignacio','Josephine','Kevin','Lourdes','Mariano',
        'Natividad','Orlando','Paz','Quirino','Remedios','Silverio','Teresita',
        'Urbano','Victoria','Wenceslao','Xandra','Yolanda','Zeus','Adrian','Bea',
        'Christian','Danica','Erwin','Faye','Gerald','Hazel','Ian','Joyce',
        'Kenneth','Lyka','Martin','Nica','Oliver','Princess','Ralph','Shaina',
        'Timothy','Uma','Vincent','Wendy','Xavier','Yasmin','Zandro','Abigail',
        'Benedict','Camille','Derick','Elaine','Francis','Grace','Harold','Irene',
        'Jerome','Karen','Lance','Mary','Nathan','Olivia','Patrick','Queen',
        'Robert','Sophia','Tristan','Ursula','Victor','Wilhelmina','Xian','Ynez',
    ];

    private array $lastNames = [
        'Santos','Reyes','Cruz','Garcia','Villanueva','Torres','Dela Cruz','Mendoza',
        'Bautista','Ocampo','Aquino','Estrada','Castillo','Ramos','Navarro',
        'Fernandez','Morales','Soriano','Padilla','Lopez','Gonzales','Perez',
        'Martinez','Flores','Rivera','Diaz','Romero','Alvarado','Jimenez','Hernandez',
        'Mercado','Pascual','Aguilar','Domingo','Andres','Batungbakal','Cayabyab',
        'Delos Santos','Enriquez','Francisco','Guerrero','Hidalgo','Ilagan',
        'Jacinto','Kalaw','Lacson','Magno','Nepomuceno','Ong','Pineda',
        'Quiambao','Resurreccion','Salazar','Tolentino','Umali','Vergara',
        'Yap','Zamora','Abalos','Buenaventura','Camacho','Datu','Espiritu',
        'Feliciano','Galang','Hilario','Ilustre','Javier','Katigbak','Legaspi',
        'Manalastas','Natividad','Obligado','Pagdanganan','Quizon','Ramirez',
        'Soriano','Tengco','Ulep','Valenzuela','Wenceslao','Ybañez','Zabala',
        'Abaya','Brillantes','Catacutan','Dimaculangan','Evangelista','Fajardo',
        'Gatmaitan','Hernandez','Ingles','Jalandoni','Katipunan','Laurel',
    ];

    private array $middleNames = [
        'Cruz','Santos','Reyes','Garcia','Lopez','Flores','Rivera','Diaz',
        'Romero','Alvarado','Jimenez','Hernandez','Mercado','Pascual','Aguilar',
        'Domingo','Andres','Batungbakal','Cayabyab','Enriquez','Francisco',
        'Guerrero','Hidalgo','Ilagan','Jacinto','Kalaw','Lacson','Magno',
        'Nepomuceno','Pineda','Quiambao','Resurreccion','Salazar','Tolentino',
        'Umali','Vergara','Yap','Zamora','Abalos','Buenaventura','Camacho',
        'Espiritu','Feliciano','Galang','Hilario','Ilustre','Javier','Legaspi',
        'Manalastas','Natividad','Obligado','Ramirez','Tengco','Valenzuela',
    ];

    private array $streets = [
        'Sampaguita St.','Orchid Drive','Acacia Lane','Magnolia Blvd.','Rose Street',
        'Ilang-Ilang St.','Mango St.','Narra St.','Palm Drive','Camia St.',
        'Jasmine St.','Cypress St.','Angsana Drive','Rosal St.','Kamagong St.',
        'Bamboo Lane','Mahogany Ave.','Molave St.','Yakal St.','Ipil St.',
        'Katmon St.','Batikuling Ave.','Dao St.','Tindalo St.','Amugis Lane',
    ];

    private array $barangays = [
        'Putol','Puting Kahoy','Tagapo','Aplaya','Bolbok','Alangilan','Tejero',
        'Iyam','Aldana','Highway Hills','Ugong','Mamatid','Bagong Silang',
        'Tubigan','Don Jose','Market Area','Dila','Balagtas','Wawa','San Antonio',
        'Almanza Uno','Almanza Dos','Bagong Bayan','Mayapa','Bancal',
    ];

    private array $cityProvinces = [
        ['San Pedro', 'Laguna', '4023'],
        ['Calamba', 'Laguna', '4027'],
        ['Santa Rosa', 'Laguna', '4026'],
        ['Biñan', 'Laguna', '4024'],
        ['Cabuyao', 'Laguna', '4025'],
        ['Batangas City', 'Batangas', '4200'],
        ['Sto. Tomas', 'Batangas', '4234'],
        ['Lipa City', 'Batangas', '4217'],
        ['Tanauan', 'Batangas', '4232'],
        ['General Trias', 'Cavite', '4107'],
        ['Imus', 'Cavite', '4103'],
        ['Bacoor', 'Cavite', '4102'],
        ['Dasmariñas', 'Cavite', '4114'],
        ['Carmona', 'Cavite', '4116'],
        ['Lucena City', 'Quezon', '4301'],
        ['San Pablo', 'Laguna', '4000'],
        ['Las Piñas', 'Metro Manila', '1750'],
        ['Muntinlupa', 'Metro Manila', '1770'],
        ['Parañaque', 'Metro Manila', '1700'],
        ['Mandaluyong', 'Metro Manila', '1550'],
        ['Pasig', 'Metro Manila', '1604'],
        ['Marikina', 'Metro Manila', '1800'],
        ['Taguig', 'Metro Manila', '1630'],
        ['Makati', 'Metro Manila', '1200'],
    ];

    private array $subdivisions = [
        'Sunvalley Subd.','Greenfield District','BF Homes','Camella Homes',
        'Vista Verde','Richwood Homes','Pacific Malayan','Breezemont Subd.',
        'Golden Meadows','Fil-Estate Subd.','Laguna Bel Air','Maharlika Village',
        'Laguna Heights','Avida Settings','Portofino Heights','Valle Verde',
        'Sunrise Valley Homes','Bukal Hills','Woodland Park Homes','Spring Homes',
        'Greenville Subd.','BF Resort Village','Metrogate','Villa Angela Subd.',
        'Mandala Park','Lakeview Homes','Belvedere Subd.','Cityview Subd.',
    ];

    private array $schools = [
        ['San Pedro National High School','Senior High School'],
        ['Calamba National High School','Senior High School'],
        ['Santa Rosa Science and Technology High School','Senior High School'],
        ['Biñan National High School','Senior High School'],
        ['Batangas National High School','Senior High School'],
        ['General Trias Senior High School','Senior High School'],
        ['Las Piñas National High School','Senior High School'],
        ['Lucena Senior High School','Senior High School'],
        ['Carmona National High School','Senior High School'],
        ['Sto. Tomas National High School','Senior High School'],
        ['Pasig City Science High School','Senior High School'],
        ['Cabuyao National High School','Senior High School'],
        ['Lipa City National High School','Senior High School'],
        ['Imus National High School','Senior High School'],
        ['Tanauan City National High School','Senior High School'],
        ['Bacoor National High School','Senior High School'],
        ['Dasmariñas National High School','Senior High School'],
        ['Biñan City National High School','Senior High School'],
        ['San Pablo City National High School','Senior High School'],
        ['Marikina Science High School','Senior High School'],
    ];

    private array $activities = [
        ['Web Development Competition','Contestant','Google Developer Student Club'],
        ['Basketball Varsity Team','Player','CCS Sports Club'],
        ['Student Government Officer','Secretary','CCS Student Council'],
        ['Research Team Member','Member','CCS Research Society'],
        ['Debate Club','Debater','CCS Debate Club'],
        ['Chess Club','Member','CCS Chess Club'],
        ['Robotics Club','Member','CCS Robotics Club'],
        ['Dance Troupe','Performer','CCS Dance Club'],
        ['Coding Bootcamp','Participant','CCS Developer Hub'],
        ['Hackathon','Team Member','Google Developer Student Club'],
        ['Quiz Bee','Contestant','CCS Academic Team'],
        ['Theater Arts','Actor','CCS Theater Arts Society'],
        ['Engineering Society','Volunteer','CCS Engineering Society'],
        ['Tech Blog','Writer','CCS Tech Writers Guild'],
        ['AWS Study Group','Member','CCS Developer Hub'],
    ];

    private array $awardTitles = [
        "Dean's List - 1st Semester",
        "Dean's List - 2nd Semester",
        'Best in Programming Award',
        'Academic Excellence Award',
        'Leadership Award',
        'Most Outstanding Student',
        'Best Capstone Project',
        'Loyalty Award',
    ];

    private array $violationTitles = [
        ['Academic Dishonesty','Submitted a plagiarized report. Written warning issued.'],
        ['Excessive Tardiness','Accumulated excessive late arrivals. Verbal warning issued.'],
        ['Disruptive Behavior','Found using mobile phone during examination. Written warning.'],
        ['Excessive Absences','Exceeded allowable absence threshold. Placed on probation.'],
        ['Dress Code Violation','Reported to class not in proper uniform. Written warning.'],
    ];

    private array $academicSkills = [
        'Python Programming','Java Programming','C++ Programming','JavaScript',
        'PHP and Laravel','HTML and CSS','React.js','Node.js','SQL Querying',
        'Data Structures','Algorithm Design','Database Design','Linux Administration',
        'Computer Networks','Cybersecurity Fundamentals','Machine Learning Basics',
        'Embedded C Programming','Circuit Analysis','Electronics Fundamentals',
        'MATLAB','Arduino and Robotics','PCB Design Basics','IoT Development',
        'Web Development','Mobile App Development','Software Engineering',
        'Object-Oriented Programming','Computer Architecture','Operating Systems',
    ];

    private array $softSkills = [
        'Leadership','Teamwork','Problem Solving','Critical Thinking','Communication',
        'Public Speaking','Time Management','Attention to Detail','Adaptability',
        'Creativity','Project Management','Analytical Thinking','Perseverance',
        'Research Skills','Technical Writing','Presentation Skills',
    ];

    public function run(): void
    {
        $this->command->info('Seeding 1000 large student dataset...');

        $programIds   = DB::table('program')->pluck('id', 'name');
        $curricula    = DB::table('curriculum')->pluck('id', 'programId');
        $sections     = DB::table('section')
            ->where('academicYear', '2025-2026')
            ->where('sectionName', 'LIKE', '%-A')
            ->get()
            ->groupBy(fn($s) => $s->programId . '_' . $s->yearLevel . '_' . $s->semester);

        $programs = [
            'BS Information Technology',
            'BS Computer Science',
            'BS Computer Engineering',
        ];

        // Seed master skills first
        $this->seedMasterSkills();
        $skillIds = DB::table('skills')->pluck('id', 'name');

        $usedEmails   = DB::table('users')->pluck('email')->flip()->toArray();
        $usedStudentIds = DB::table('student')->pluck('studentId')->flip()->toArray();

        $studentCount = 0;
        $batchSize    = 50;
        $skillPivots  = [];

        $yearCounters = [2022 => 5, 2023 => 6, 2024 => 7, 2025 => 6]; // start after existing

        while ($studentCount < 1000) {
            $program   = $programs[array_rand($programs)];
            $programId = $programIds[$program];
            $yearLevel = rand(1, 4);

            // Enrollment year based on year level
            $enrollYear = 2026 - $yearLevel;
            if ($enrollYear < 2022) $enrollYear = 2022;

            // Generate unique studentId
            $yearCounters[$enrollYear] = ($yearCounters[$enrollYear] ?? 0) + 1;
            $studentIdStr = $enrollYear . '-' . str_pad($yearCounters[$enrollYear], 5, '0', STR_PAD_LEFT);
            if (isset($usedStudentIds[$studentIdStr])) {
                $yearCounters[$enrollYear]++;
                $studentIdStr = $enrollYear . '-' . str_pad($yearCounters[$enrollYear], 5, '0', STR_PAD_LEFT);
            }
            $usedStudentIds[$studentIdStr] = true;

            // Generate unique email
            $firstName  = $this->firstNames[array_rand($this->firstNames)];
            $lastName   = $this->lastNames[array_rand($this->lastNames)];
            $middleName = $this->middleNames[array_rand($this->middleNames)];

            $emailBase = strtolower(
                preg_replace('/\s+/', '.', $firstName) . '.' .
                strtolower(str_replace(' ', '', $lastName))
            );
            $email = $emailBase . rand(100, 999) . '@student.ccs.edu.ph';
            $tries = 0;
            while (isset($usedEmails[$email]) && $tries < 10) {
                $email = $emailBase . rand(1000, 9999) . '@student.ccs.edu.ph';
                $tries++;
            }
            $usedEmails[$email] = true;

            [$city, $province, $postal] = $this->cityProvinces[array_rand($this->cityProvinces)];
            $birthYear  = rand(2000, 2007);
            $birthMonth = rand(1, 12);
            $birthDay   = rand(1, 28);
            $age        = 2026 - $birthYear;

            // GPA based on year level (slight randomization)
            $gpaOptions = [1.00,1.25,1.50,1.75,2.00,2.25,2.50,2.75,3.00];
            $gpa        = $gpaOptions[array_rand($gpaOptions)];

            // Units
            $totalUnits  = 135;
            $unitsTaken  = match($yearLevel) {
                1 => rand(15, 22),
                2 => rand(40, 55),
                3 => rand(70, 90),
                4 => rand(100, 130),
            };
            $unitsLeft = max(0, $totalUnits - $unitsTaken);

            // Insert user
            $userId = DB::table('users')->insertGetId([
                'firstName'        => $firstName,
                'lastName'         => $lastName,
                'middleName'       => $middleName,
                'age'              => $age,
                'birthDate'        => "$birthYear-" . str_pad($birthMonth, 2, '0', STR_PAD_LEFT) . '-' . str_pad($birthDay, 2, '0', STR_PAD_LEFT),
                'birthProvince'    => $province,
                'subdivision'      => $this->subdivisions[array_rand($this->subdivisions)],
                'street'           => $this->streets[array_rand($this->streets)],
                'barangay'         => $this->barangays[array_rand($this->barangays)],
                'city'             => $city,
                'province'         => $province,
                'postalCode'       => $postal,
                'mobileNumber'     => '09' . rand(100000000, 999999999),
                'email'            => $email,
                'password'         => Hash::make('Student@12345'),
                'email_verified_at'=> now(),
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            // Determine status
            $status = 'Active';
            if ($yearLevel === 4 && rand(0, 10) > 8) $status = 'Graduated';
            if (rand(0, 20) === 0) $status = 'Dropped';

            // Insert student
            $studentId = DB::table('student')->insertGetId([
                'userId'       => $userId,
                'studentId'    => $studentIdStr,
                'programId'    => $programId,
                'yearLevel'    => $yearLevel,
                'unitsTaken'   => $unitsTaken,
                'unitsLeft'    => $unitsLeft,
                'dateEnrolled' => "$enrollYear-08-" . rand(12, 20),
                'gpa'          => $gpa,
                'status'       => $status,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            // Student program
            DB::table('student_program')->insert([
                'studentId'   => $studentId,
                'programId'   => $programId,
                'dateEnrolled'=> "$enrollYear-08-15",
                'dateLeft'    => null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            // Student sections
            foreach ([1, 2] as $sem) {
                $sectionKey = $programId . '_' . $yearLevel . '_' . $sem;
                if (isset($sections[$sectionKey])) {
                    $section = $sections[$sectionKey]->first();
                    DB::table('student_section')->insert([
                        'studentId'   => $studentId,
                        'sectionId'   => $section->id,
                        'academicYear'=> '2025-2026',
                        'semester'    => $sem,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ]);
                }
            }

            // Grades
            $this->seedGrades($studentId, $programId, $yearLevel, $gpa, $curricula, $sections);

            // Educational background
            [$school, $type] = $this->schools[array_rand($this->schools)];
            $gradYear = $enrollYear;
            DB::table('educational_background')->insert([
                'userId'        => $userId,
                'schoolUniversity'=> $school,
                'startYear'     => $gradYear - 4,
                'graduateYear'  => $gradYear,
                'type'          => $type,
                'award'         => rand(0, 5) === 0 ? 'With Honors' : null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            // Extracurriculars (0-2)
            $numActivities = rand(0, 2);
            $usedActivities = [];
            for ($a = 0; $a < $numActivities; $a++) {
                $actIndex = array_rand($this->activities);
                if (in_array($actIndex, $usedActivities)) continue;
                $usedActivities[] = $actIndex;
                [$activity, $role, $org] = $this->activities[$actIndex];
                DB::table('extra_curricular')->insert([
                    'studentId'  => $studentId,
                    'activity'   => $activity,
                    'role'       => $role,
                    'organization'=> $org,
                    'startDate'  => '2025-09-01',
                    'endDate'    => rand(0, 1) ? null : '2026-05-31',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Awards (0-2, more likely for high GPA)
            $numAwards = $gpa <= 1.50 ? rand(1, 2) : (rand(0, 4) === 0 ? 1 : 0);
            for ($aw = 0; $aw < $numAwards; $aw++) {
                DB::table('awards')->insert([
                    'userId'               => $userId,
                    'title'               => $this->awardTitles[array_rand($this->awardTitles)],
                    'awardingDate'        => '2026-01-' . str_pad(rand(10, 28), 2, '0', STR_PAD_LEFT),
                    'awardingOrganization'=> 'College of Computing Studies',
                    'awardingLocation'    => 'CCS Hall, Laguna',
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ]);
            }

            // Violations (rare, more likely for low GPA)
            $hasViolation = $gpa >= 2.50 ? rand(0, 3) === 0 : rand(0, 10) === 0;
            if ($hasViolation) {
                [$vTitle, $vDesc] = $this->violationTitles[array_rand($this->violationTitles)];
                DB::table('violation')->insert([
                    'studentId'    => $studentId,
                    'title'        => $vTitle,
                    'violationDate'=> '2025-' . str_pad(rand(9, 12), 2, '0', STR_PAD_LEFT) . '-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT),
                    'description'  => $vDesc,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }

            // Skills (2-4 academic + 1-2 soft)
            $assignedSkillNames = [];
            $numAcademic = rand(2, 4);
            $numSoft     = rand(1, 2);

            $academicPool = $this->academicSkills;
            shuffle($academicPool);
            $softPool = $this->softSkills;
            shuffle($softPool);

            foreach (array_slice($academicPool, 0, $numAcademic) as $skillName) {
                if (isset($skillIds[$skillName])) {
                    $skillPivots[] = [
                        'studentId'  => $studentId,
                        'skillId'    => $skillIds[$skillName],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
            foreach (array_slice($softPool, 0, $numSoft) as $skillName) {
                if (isset($skillIds[$skillName])) {
                    $skillPivots[] = [
                        'studentId'  => $studentId,
                        'skillId'    => $skillIds[$skillName],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            $studentCount++;

            // Flush skill pivots every batch
            if ($studentCount % $batchSize === 0) {
                foreach (array_chunk($skillPivots, 200) as $chunk) {
                    DB::table('student_skills')->insert($chunk);
                }
                $skillPivots = [];
                $this->command->info("  Seeded $studentCount students...");
            }
        }

        // Flush remaining skill pivots
        if (!empty($skillPivots)) {
            foreach (array_chunk($skillPivots, 200) as $chunk) {
                DB::table('student_skills')->insert($chunk);
            }
        }

        $this->command->info('Done! 1000 students seeded.');
    }

    private function seedMasterSkills(): void
    {
        $allSkills = array_merge(
            array_map(fn($s) => ['name' => $s, 'isAcademic' => 1], $this->academicSkills),
            array_map(fn($s) => ['name' => $s, 'isAcademic' => 0], $this->softSkills),
        );

        foreach ($allSkills as $skill) {
            DB::table('skills')->insertOrIgnore(array_merge($skill, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    private function seedGrades(
        int $studentId,
        int $programId,
        int $yearLevel,
        float $gpa,
        mixed $curricula,
        mixed $sections
    ): void {
        if (!isset($curricula[$programId])) return;

        $sectionKey = $programId . '_' . $yearLevel . '_1';
        if (!isset($sections[$sectionKey])) return;

        $section = $sections[$sectionKey]->first();

        $courses = DB::table('courses')
            ->where('curriculumId', $curricula[$programId])
            ->where('yearLevel', $yearLevel)
            ->where('semester', 1)
            ->get();

        $terms = ['preliminary', 'midterm', 'finals'];

        foreach ($courses as $course) {
            // Randomize slightly around the student's GPA
            $baseGrade = round(max(1.00, min(3.00, $gpa + (rand(-25, 25) / 100))), 2);

            foreach ($terms as $term) {
                $offset = match($term) {
                    'preliminary' => 0.00,
                    'midterm'     => 0.25,
                    'finals'      => -0.25,
                    default       => 0.00,
                };
                $grade   = round(max(1.00, min(3.00, $baseGrade + $offset)), 2);
                $remarks = $grade <= 3.00 ? 'passed' : 'failed';

                DB::table('grades')->insert([
                    'studentId'   => $studentId,
                    'sectionId'   => $section->id,
                    'courseId'    => $course->id,
                    'academicYear'=> '2025-2026',
                    'semester'    => 1,
                    'term'        => $term,
                    'grade'       => $grade,
                    'remarks'     => $remarks,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        }
    }
}