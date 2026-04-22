<?php

namespace App\Repositories;

use App\Models\Lesson;
use Illuminate\Database\Eloquent\Collection;

class LessonRepository extends BaseRepository
{
    public function __construct() { parent::__construct(new Lesson()); }

    public function getByCourse(int $courseId): Collection
    {
        return Lesson::where('courseId', $courseId)
            ->orderBy('lessonOrder')
            ->get();
    }
}
