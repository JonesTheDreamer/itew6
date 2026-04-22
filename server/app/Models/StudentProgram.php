<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentProgram extends Model
{
    protected $table = 'student_program';
    protected $fillable = ['studentId', 'programId', 'dateEnrolled', 'dateLeft'];

    public function student()
    {
        return $this->belongsTo(Student::class, 'studentId');
    }
    public function program()
    {
        return $this->belongsTo(Program::class, 'programId');
    }
}