<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentSection extends Model
{
    protected $table = 'student_section';
    protected $fillable = ['studentId', 'sectionId', 'academicYear', 'semester'];

    public function student()
    {
        return $this->belongsTo(Student::class, 'studentId');
    }
    public function section()
    {
        return $this->belongsTo(Section::class, 'sectionId');
    }
}