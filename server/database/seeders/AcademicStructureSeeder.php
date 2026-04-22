<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AcademicStructureSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedColleges();
        $this->seedPrograms();
        $this->seedSections();
        $this->seedCurricula();
        $this->seedCourses();
        $this->seedLessons();
        $this->seedSchedules();
    }

    private function seedColleges(): void
    {
        DB::table('college')->insert([
            [
                'name'            => 'College of Computing Studies',
                'dean'            => 'Dr. Ana M. Dela Rosa',
                'dateEstablished' => '2000-06-01',
                'isActive'        => 1,
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
        ]);
    }

    private function seedPrograms(): void
    {
        $collegeId = DB::table('college')->where('name', 'College of Computing Studies')->value('id');

        DB::table('program')->insert([
            ['collegeId' => $collegeId, 'name' => 'BS Information Technology', 'type' => 'Undergraduate', 'dateEstablished' => '2000-06-01', 'isActive' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['collegeId' => $collegeId, 'name' => 'BS Computer Science',       'type' => 'Undergraduate', 'dateEstablished' => '2000-06-01', 'isActive' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['collegeId' => $collegeId, 'name' => 'BS Computer Engineering',   'type' => 'Undergraduate', 'dateEstablished' => '2005-06-01', 'isActive' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    private function seedSections(): void
    {
        $programIds = DB::table('program')->pluck('id');
        $sections   = [];

        foreach ($programIds as $programId) {
            for ($year = 1; $year <= 4; $year++) {
                foreach ([1, 2] as $sem) {
                    $sections[] = [
                        'programId'    => $programId,
                        'sectionName'  => $year . '-A',
                        'academicYear' => '2025-2026',
                        'yearLevel'    => $year,
                        'semester'     => $sem,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ];
                    // Second section for year 1-2 (larger class sizes)
                    if ($year <= 2) {
                        $sections[] = [
                            'programId'    => $programId,
                            'sectionName'  => $year . '-B',
                            'academicYear' => '2025-2026',
                            'yearLevel'    => $year,
                            'semester'     => $sem,
                            'created_at'   => now(),
                            'updated_at'   => now(),
                        ];
                    }
                }
            }
        }

        DB::table('section')->insert($sections);
    }

    private function seedCurricula(): void
    {
        $programs = DB::table('program')->pluck('id', 'name');

        DB::table('curriculum')->insert([
            [
                'programId'     => $programs['BS Information Technology'],
                'name'          => 'BSIT Curriculum 2022',
                'effectiveYear' => 2022,
                'isActive'      => 1,
                'description'   => 'Revised curriculum aligned with CHED CMO No. 25 series 2015 and updated industry standards. Covers application development, networking, database management, cybersecurity, and IT project management.',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'programId'     => $programs['BS Computer Science'],
                'name'          => 'BSCS Curriculum 2022',
                'effectiveYear' => 2022,
                'isActive'      => 1,
                'description'   => 'Revised curriculum aligned with ACM/IEEE Computing Curricula guidelines. Covers algorithms, programming, artificial intelligence, machine learning, and theoretical computer science foundations.',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'programId'     => $programs['BS Computer Engineering'],
                'name'          => 'BSCpE Curriculum 2022',
                'effectiveYear' => 2022,
                'isActive'      => 1,
                'description'   => 'Revised curriculum aligned with CHED CMO No. 7 series 2017. Combines computer science and electrical engineering to design and develop computer hardware and embedded systems.',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ]);
    }

    private function seedCourses(): void
    {
        $curricula = DB::table('curriculum')
            ->join('program', 'curriculum.programId', '=', 'program.id')
            ->pluck('curriculum.id', 'program.name');

        $allCourses = [];

        foreach ($this->getCoursePlans() as $programName => [$prefix, $plan]) {
            if (! isset($curricula[$programName])) {
                continue;
            }
            $curriculumId = $curricula[$programName];
            $counter      = 101;

            foreach ($plan as [$yearLevel, $semester, $type, $courses]) {
                foreach ($courses as $courseName) {
                    $isPe     = str_contains($courseName, 'Physical Education') || str_contains($courseName, 'NSTP');
                    $units    = $isPe ? 2 : 3;
                    $labUnits = ($type === 'lecture_lab') ? 1 : null;

                    $allCourses[] = [
                        'curriculumId' => $curriculumId,
                        'courseCode'   => $prefix . str_pad($counter, 3, '0', STR_PAD_LEFT),
                        'courseName'   => $courseName,
                        'units'        => $units,
                        'labUnits'     => $labUnits,
                        'yearLevel'    => $yearLevel,
                        'semester'     => $semester,
                        'courseType'   => $type,
                        'isRequired'   => ! str_contains($courseName, 'Elective'),
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ];
                    $counter++;
                }
            }
        }

        foreach (array_chunk($allCourses, 50) as $chunk) {
            DB::table('courses')->insert($chunk);
        }
    }

    private function seedLessons(): void
    {
        $courses = DB::table('courses')
            ->where('yearLevel', '<=', 2)
            ->get();

        $lessonTemplates = [
            ['Course Introduction and Learning Objectives',    'Overview of %s: objectives, learning outcomes, grading policy, and study guide.'],
            ['Fundamental Concepts and Definitions',           'Core theories, key terminologies, and foundational principles of %s.'],
            ['Historical Background and Evolution',            'Historical development, milestones, and evolution of concepts in %s.'],
            ['Practical Applications and Demonstrations',      'Hands-on exercises, worked examples, and real-world applications of %s.'],
            ['Advanced Topics and Current Trends',             'Advanced techniques, case studies, and emerging trends related to %s.'],
            ['Problem Solving and Exercises',                  'Practice problems, group activities, and reinforcement exercises for %s.'],
            ['Midterm Review and Integration',                 'Comprehensive review of first-half topics and integration activities for %s.'],
            ['Emerging Technologies and Industry Applications','Industry use-cases and recent innovations applied to %s.'],
            ['Ethical Considerations and Best Practices',      'Professional ethics, standards, and best practices in %s.'],
            ['Final Review and Assessment Preparation',        'Comprehensive course review, sample exam questions, and final preparation for %s.'],
        ];

        $lessons = [];
        foreach ($courses as $course) {
            foreach ($lessonTemplates as $order => [$title, $desc]) {
                $lessons[] = [
                    'courseId'    => $course->id,
                    'lessonOrder' => $order + 1,
                    'lessonTitle' => sprintf($title),
                    'description' => sprintf($desc, $course->courseName),
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
            }
        }

        foreach (array_chunk($lessons, 100) as $chunk) {
            DB::table('lessons')->insert($chunk);
        }
    }

    private function seedSchedules(): void
    {
        $curricula = DB::table('curriculum')->pluck('id', 'programId');
        $sections  = DB::table('section')->get();

        $timeSlots = [
            ['07:30:00', '09:00:00'],
            ['09:00:00', '10:30:00'],
            ['10:30:00', '12:00:00'],
            ['13:00:00', '14:30:00'],
            ['14:30:00', '16:00:00'],
            ['16:00:00', '17:30:00'],
        ];
        $rooms = ['CCS-101', 'CCS-102', 'CCS-103', 'CCS-201', 'CCS-202', 'CCS-LAB1', 'CCS-LAB2', 'CCS-LAB3', 'CCS-LAB4'];

        $schedules = [];
        foreach ($sections as $section) {
            if (! isset($curricula[$section->programId])) {
                continue;
            }
            $courses = DB::table('courses')
                ->where('curriculumId', $curricula[$section->programId])
                ->where('yearLevel', $section->yearLevel)
                ->where('semester', $section->semester)
                ->get();

            $i = 0;
            foreach ($courses as $course) {
                $roomIndex  = ($course->courseType === 'lecture_lab') ? (count($rooms) - 4 + ($i % 4)) : ($i % 5);
                $schedules[] = [
                    'sectionId'  => $section->id,
                    'courseId'   => $course->id,
                    'courseName' => $course->courseName,
                    'timeStart'  => $timeSlots[$i % 6][0],
                    'timeEnd'    => $timeSlots[$i % 6][1],
                    'room'       => $rooms[$roomIndex],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $i++;
            }
        }

        foreach (array_chunk($schedules, 100) as $chunk) {
            DB::table('schedule')->insert($chunk);
        }
    }

    private function getCoursePlans(): array
    {
        return [
            'BS Information Technology' => ['ITE', [
                // Year 1
                [1, 1, 'lecture',     ['Introduction to Computing',        'College Algebra and Trigonometry',   'Purposive Communication',            'NSTP 1',                             'Physical Education 1']],
                [1, 2, 'lecture_lab', ['Computer Programming 1 (Python)',  'Mathematics in the Modern World',    'Readings in Philippine History',      'NSTP 2',                             'Physical Education 2']],
                // Year 2
                [2, 1, 'lecture_lab', ['Computer Programming 2 (Java)',    'Data Structures and Algorithms',     'Platform Technologies',              'Discrete Mathematics',               'The Contemporary World']],
                [2, 2, 'lecture_lab', ['Web Systems and Technologies 1',   'Database Management Systems',        'Computer Networks and Communications','Object-Oriented Programming',        'Ethics (GE Elective)']],
                // Year 3
                [3, 1, 'lecture_lab', ['Web Systems and Technologies 2',   'Systems Analysis and Design',        'Information Assurance and Security 1','Application Development',            'Social Issues and Professional Practice']],
                [3, 2, 'lecture_lab', ['Mobile Application Development',   'Integrative Programming Technologies','Network Management',                 'Information Assurance and Security 2','Technopreneurship']],
                // Year 4
                [4, 1, 'lecture',     ['Capstone Project and Research 1',  'Systems Integration and Architecture','IT Project Management',              'Systems Quality Assurance',          'Elective 1: Cloud Computing']],
                [4, 2, 'lecture',     ['Capstone Project and Research 2',  'IT Practicum (600 hours)',            'IT Governance and Compliance',        'Elective 2: Machine Learning Basics', 'Elective 3: DevOps Fundamentals']],
            ]],

            'BS Computer Science' => ['CSC', [
                // Year 1
                [1, 1, 'lecture',     ['Introduction to Computer Science',  'Calculus 1',                         'Purposive Communication',             'NSTP 1',                             'Physical Education 1']],
                [1, 2, 'lecture_lab', ['Programming Languages',             'Calculus 2',                         'Readings in Philippine History',       'NSTP 2',                             'Physical Education 2']],
                // Year 2
                [2, 1, 'lecture_lab', ['Data Structures',                   'Object-Oriented Programming',        'Computer Organization and Architecture','Discrete Structures',                'Social Science 1']],
                [2, 2, 'lecture_lab', ['Design and Analysis of Algorithms', 'Database Systems',                   'Logic Design and Digital Circuits',    'Linear Algebra',                     'Social Science 2']],
                // Year 3
                [3, 1, 'lecture_lab', ['Theory of Computation',             'Software Engineering',               'Operating Systems',                   'Artificial Intelligence',            'CS Professional Ethics']],
                [3, 2, 'lecture_lab', ['Machine Learning',                  'Compiler Design',                    'Distributed Systems',                 'Computer Graphics and Visualization', 'CS Research Methods']],
                // Year 4
                [4, 1, 'lecture',     ['CS Thesis 1 (Research Proposal)',   'Computer Vision',                    'Natural Language Processing',         'CS Practicum 1',                     'CS Elective 1: Cybersecurity']],
                [4, 2, 'lecture',     ['CS Thesis 2 (Implementation)',       'CS Practicum 2',                    'Emerging CS Technologies',            'Entrepreneurship for CS',             'CS Elective 2: Blockchain Technology']],
            ]],

            'BS Computer Engineering' => ['CPE', [
                // Year 1
                [1, 1, 'lecture',     ['Engineering Drawing and CAD',       'Calculus 1',                         'Physics 1 (Mechanics)',               'NSTP 1',                             'Physical Education 1']],
                [1, 2, 'lecture_lab', ['C Programming for Engineers',       'Calculus 2',                         'Physics 2 (Electricity and Magnetism)','NSTP 2',                             'Physical Education 2']],
                // Year 2
                [2, 1, 'lecture_lab', ['Circuit Theory 1',                  'Digital Logic Design',               'Computer Organization',              'Engineering Mathematics 1',          'Social Science 1']],
                [2, 2, 'lecture_lab', ['Microprocessors and Microcontrollers','Data Communications',              'Signals and Systems',                 'Engineering Mathematics 2',          'Technical Writing for Engineers']],
                // Year 3
                [3, 1, 'lecture_lab', ['Embedded Systems Design',           'Computer Architecture',              'VLSI Circuit Design',                'Control Systems Engineering',        'Engineering Ethics and Professionalism']],
                [3, 2, 'lecture_lab', ['Computer Networks and Protocols',   'Real-Time Operating Systems',        'Digital Signal Processing',           'Engineering Economics',              'Philippine History']],
                // Year 4
                [4, 1, 'lecture',     ['CPE Thesis 1',                      'Internet of Things Systems',         'Wireless and Mobile Communications',  'CPE Practicum 1',                    'CPE Elective 1: Robotics']],
                [4, 2, 'lecture',     ['CPE Thesis 2',                      'CPE Practicum 2',                   'Smart Systems and Automation',         'Engineering Laws and Ethics',        'CPE Elective 2: FPGA Design']],
            ]],
        ];
    }
}
