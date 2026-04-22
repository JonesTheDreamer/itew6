<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $table = 'grades';
    protected $fillable = ['studentId', 'sectionId', 'courseId', 'academicYear', 'semester', 'term', 'grade', 'remarks'];
    protected $casts = ['grade' => 'float'];
    public function student()
    {
        return $this->belongsTo(Student::class, 'studentId');
    }
    public function section()
    {
        return $this->belongsTo(Section::class, 'sectionId');
    }
    public function course()
    {
        return $this->belongsTo(Course::class, 'courseId');
    }
}