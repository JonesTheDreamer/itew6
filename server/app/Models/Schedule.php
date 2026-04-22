<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $table = 'schedule';
    protected $fillable = ['sectionId', 'courseId', 'courseName', 'timeStart', 'timeEnd', 'room'];

    public function section()
    {
        return $this->belongsTo(Section::class, 'sectionId');
    }
}