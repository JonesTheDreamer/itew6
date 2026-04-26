<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $table = 'section';
    protected $fillable = ['programId', 'sectionName', 'academicYear', 'yearLevel', 'semester'];

    public function program()
    {
        return $this->belongsTo(Program::class, 'programId');
    }
    // public function schedules()
    // {
    //     return $this->hasMany(Schedule::class, 'sectionId');
    // }
    public function studentSections()
    {
        return $this->hasMany(StudentSection::class, 'sectionId');
    }
}