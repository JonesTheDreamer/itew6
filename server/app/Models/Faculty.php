<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    protected $table = 'faculty';
    protected $fillable = ['userId', 'position', 'employmentDate', 'employmentType', 'monthlyIncome', 'department'];
    protected $casts = ['monthlyIncome' => 'float'];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }
    public function jobHistory()
    {
        return $this->hasMany(JobHistory::class, 'facultyId');
    }
}