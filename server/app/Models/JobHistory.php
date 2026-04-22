<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobHistory extends Model
{
    protected $table = 'job_history';
    protected $fillable = ['facultyId', 'position', 'employmentDate', 'employmentEndDate', 'employmentType', 'company', 'workLocation'];

    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'facultyId');
    }
}