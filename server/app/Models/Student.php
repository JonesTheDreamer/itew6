<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'student';

    protected $fillable = [
        'userId',
        'studentId',
        'programId',
        'yearLevel',
        'unitsTaken',
        'unitsLeft',
        'dateEnrolled',
        'dateGraduated',
        'dateDropped',
        'gpa',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function grades()
    {
        return $this->hasMany(Grade::class, 'studentId');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'programId');
    }
    public function sections()
    {
        return $this->hasMany(StudentSection::class, 'studentId');
    }
    public function programs()
    {
        return $this->hasMany(StudentProgram::class, 'studentId');
    }
    public function extraCurricular()
    {
        return $this->hasMany(ExtraCurricular::class, 'studentId');
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'student_skills', 'studentId', 'skillId');
    }

    public function studentSkills()
    {
        return $this->hasMany(StudentSkill::class, 'studentId');
    }
}