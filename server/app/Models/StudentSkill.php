<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentSkill extends Model
{
    protected $table = 'student_skills';
    protected $fillable = ['studentId', 'skillId'];

    public function student()
    {
        return $this->belongsTo(Student::class, 'studentId');
    }

    public function skill()
    {
        return $this->belongsTo(Skill::class, 'skillId');
    }
}
