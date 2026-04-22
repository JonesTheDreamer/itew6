<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EducationalBackground extends Model
{
    protected $table = 'educational_background';
    protected $fillable = ['userId', 'schoolUniversity', 'startYear', 'graduateYear', 'type', 'award'];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }
}