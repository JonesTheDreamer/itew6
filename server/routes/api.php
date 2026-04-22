<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CollegeController;
use App\Http\Controllers\Api\ProgramController;
use App\Http\Controllers\Api\CurriculumController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\SectionController;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\ExtraCurricularController;
use App\Http\Controllers\Api\AwardController;
use App\Http\Controllers\Api\EducationalBackgroundController;
use App\Http\Controllers\Api\StudentSectionController;
use App\Http\Controllers\Api\StudentProgramController;
use App\Http\Controllers\Api\FacultyController;
use App\Http\Controllers\Api\JobHistoryController;
use App\Http\Controllers\Api\OrganizationController;
use App\Http\Controllers\Api\UserOrganizationController;
use App\Http\Controllers\Api\LessonController;
use App\Http\Controllers\Api\GradeController;
use App\Http\Controllers\Api\SkillController;
use App\Http\Controllers\Api\ViolationController;
use App\Http\Controllers\Api\ViolationNoteController;
use Illuminate\Support\Facades\Route;

// Public auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Colleges
    Route::get('/colleges', [CollegeController::class, 'index']);
    Route::get('/colleges/{id}', [CollegeController::class, 'show']);

    // Programs
    Route::get('/programs', [ProgramController::class, 'index']);
    Route::get('/programs/{id}', [ProgramController::class, 'show']);

    // Curriculum
    Route::get('/curriculum', [CurriculumController::class, 'index']);
    Route::post('/curriculum', [CurriculumController::class, 'store']);
    Route::get('/curriculum/{id}', [CurriculumController::class, 'show']);
    Route::put('/curriculum/{id}', [CurriculumController::class, 'update']);
    Route::delete('/curriculum/{id}', [CurriculumController::class, 'destroy']);
    Route::patch('/curriculum/{id}/activate', [CurriculumController::class, 'activate']);

    // Courses
    Route::get('/courses', [CourseController::class, 'index']);
    Route::post('/courses', [CourseController::class, 'store']);
    Route::get('/courses/{id}', [CourseController::class, 'show']);
    Route::put('/courses/{id}', [CourseController::class, 'update']);
    Route::delete('/courses/{id}', [CourseController::class, 'destroy']);

    // Sections
    Route::get('/sections', [SectionController::class, 'index']);
    Route::post('/sections', [SectionController::class, 'store']);
    Route::get('/sections/{id}', [SectionController::class, 'show']);
    Route::put('/sections/{id}', [SectionController::class, 'update']);
    Route::delete('/sections/{id}', [SectionController::class, 'destroy']);

    // Schedules
    Route::get('/schedules', [ScheduleController::class, 'index']);
    Route::get('/schedules/student/{studentId}', [ScheduleController::class, 'byStudent']);

    // Students — stats BEFORE {id}
    Route::get('/students/stats', [StudentController::class, 'stats']);
    Route::get('/students', [StudentController::class, 'index']);
    Route::post('/students', [StudentController::class, 'store']);
    Route::get('/students/{id}', [StudentController::class, 'show']);
    Route::put('/students/{id}', [StudentController::class, 'update']);
    Route::delete('/students/{id}', [StudentController::class, 'destroy']);
    Route::patch('/students/{id}/status', [StudentController::class, 'updateStatus']);

    // Student sub-resources
    Route::get('/extra-curricular', [ExtraCurricularController::class, 'index']);
    Route::post('/extra-curricular', [ExtraCurricularController::class, 'store']);
    Route::put('/extra-curricular/{id}', [ExtraCurricularController::class, 'update']);
    Route::delete('/extra-curricular/{id}', [ExtraCurricularController::class, 'destroy']);

    Route::get('/awards', [AwardController::class, 'index']);
    Route::post('/awards', [AwardController::class, 'store']);
    Route::put('/awards/{id}', [AwardController::class, 'update']);
    Route::delete('/awards/{id}', [AwardController::class, 'destroy']);

    Route::get('/educational-background', [EducationalBackgroundController::class, 'index']);
    Route::post('/educational-background', [EducationalBackgroundController::class, 'store']);
    Route::put('/educational-background/{id}', [EducationalBackgroundController::class, 'update']);
    Route::delete('/educational-background/{id}', [EducationalBackgroundController::class, 'destroy']);

    Route::get('/student-sections', [StudentSectionController::class, 'index']);
    Route::post('/student-sections', [StudentSectionController::class, 'store']);
    Route::delete('/student-sections/{id}', [StudentSectionController::class, 'destroy']);

    Route::get('/student-programs', [StudentProgramController::class, 'index']);
    Route::post('/student-programs', [StudentProgramController::class, 'store']);
    Route::delete('/student-programs/{id}', [StudentProgramController::class, 'destroy']);

    // Faculty — stats BEFORE {id}
    Route::get('/faculty/stats', [FacultyController::class, 'stats']);
    Route::get('/faculty', [FacultyController::class, 'index']);
    Route::post('/faculty', [FacultyController::class, 'store']);
    Route::get('/faculty/{id}', [FacultyController::class, 'show']);
    Route::put('/faculty/{id}', [FacultyController::class, 'update']);
    Route::delete('/faculty/{id}', [FacultyController::class, 'destroy']);

    // Job History
    Route::get('/job-history', [JobHistoryController::class, 'index']);
    Route::post('/job-history', [JobHistoryController::class, 'store']);
    Route::put('/job-history/{id}', [JobHistoryController::class, 'update']);
    Route::delete('/job-history/{id}', [JobHistoryController::class, 'destroy']);

    // Organizations — stats BEFORE {id}
    Route::get('/organizations/stats', [OrganizationController::class, 'stats']);
    Route::get('/organizations', [OrganizationController::class, 'index']);
    Route::post('/organizations', [OrganizationController::class, 'store']);
    Route::get('/organizations/{id}', [OrganizationController::class, 'show']);
    Route::put('/organizations/{id}', [OrganizationController::class, 'update']);
    Route::delete('/organizations/{id}', [OrganizationController::class, 'destroy']);

    // Entity Organizations
    Route::get('/user-organizations', [UserOrganizationController::class, 'index']);
    Route::post('/user-organizations', [UserOrganizationController::class, 'store']);
    Route::put('/user-organizations/{id}', [UserOrganizationController::class, 'update']);
    Route::delete('/user-organizations/{id}', [UserOrganizationController::class, 'destroy']);

    // Lessons
    Route::get('/lessons', [LessonController::class, 'index']);
    Route::post('/lessons', [LessonController::class, 'store']);
    Route::get('/lessons/{id}', [LessonController::class, 'show']);
    Route::put('/lessons/{id}', [LessonController::class, 'update']);
    Route::delete('/lessons/{id}', [LessonController::class, 'destroy']);

    // Grades
    Route::get('/grades', [GradeController::class, 'index']);
    Route::post('/grades', [GradeController::class, 'store']);
    Route::put('/grades/{id}', [GradeController::class, 'update']);
    Route::delete('/grades/{id}', [GradeController::class, 'destroy']);

    // Skills
    Route::get('/skills', [SkillController::class, 'index']);
    Route::post('/skills', [SkillController::class, 'store']);
    Route::put('/skills/{id}', [SkillController::class, 'update']);
    Route::delete('/skills/{id}', [SkillController::class, 'destroy']);

    // Violations
    Route::get('/students/{studentId}/violations', [ViolationController::class, 'index']);
    Route::post('/students/{studentId}/violations', [ViolationController::class, 'store']);
    Route::put('/violations/{id}', [ViolationController::class, 'update']);

    // Violation notes
    Route::post('/violations/{violationId}/notes', [ViolationNoteController::class, 'store']);
});