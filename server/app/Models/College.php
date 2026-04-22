<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class College extends Model
{
    protected $table = 'college';
    protected $fillable = ['name', 'dean', 'dateEstablished', 'isActive'];
    protected $casts = ['isActive' => 'boolean'];

    public function programs()
    {
        return $this->hasMany(Program::class, 'collegeId');
    }
    public function organizations()
    {
        return $this->hasMany(Organization::class, 'collegeId');
    }
}