<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Violation extends Model
{
    protected $table = 'violation';
    protected $fillable = ['studentId', 'title', 'violationDate', 'description'];

    public function student()
    {
        return $this->belongsTo(User::class, 'studentId');
    }

    public function notes()
    {
        return $this->hasMany(ViolationNote::class, 'violationId');
    }
}