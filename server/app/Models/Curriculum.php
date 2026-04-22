<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curriculum extends Model
{
    protected $table = 'curriculum';
    protected $fillable = ['programId', 'name', 'effectiveYear', 'isActive', 'description'];

    public function program()
    {
        return $this->belongsTo(Program::class, 'programId');
    }
    public function courses()
    {
        return $this->hasMany(Course::class, 'curriculumId');
    }
}