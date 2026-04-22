<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExtraCurricular extends Model
{
    protected $table = 'extra_curricular';
    protected $fillable = ['studentId', 'activity', 'role', 'organization', 'startDate', 'endDate'];

    public function student()
    {
        return $this->belongsTo(Student::class, 'studentId');
    }
}