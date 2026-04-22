<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    protected $table = 'skills';
    protected $fillable = ['name', 'isAcademic'];
    protected $casts = ['isAcademic' => 'boolean'];

    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_skills', 'skillId', 'studentId');
    }
}
