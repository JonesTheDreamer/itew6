<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $table = 'schedule';
    protected $fillable = [
        'sectionId', 'courseId', 'courseName',
        'timeStart', 'timeEnd', 'room', 'facultyId'
    ];

    public function section()
    {
        return $this->belongsTo(Section::class, 'sectionId');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'courseId');
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'facultyId');
    }
}