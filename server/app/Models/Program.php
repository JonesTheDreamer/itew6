<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $table = 'program';
    protected $fillable = ['collegeId', 'name', 'type', 'dateEstablished', 'isActive'];
    protected $casts = ['isActive' => 'boolean'];

    public function college()
    {
        return $this->belongsTo(College::class, 'collegeId');
    }
    public function students()
    {
        return $this->hasMany(Student::class, 'programId');
    }
    public function sections()
    {
        return $this->hasMany(Section::class, 'programId');
    }
    public function curricula()
    {
        return $this->hasMany(Curriculum::class, 'programId');
    }
}