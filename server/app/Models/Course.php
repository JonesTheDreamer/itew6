<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $table = 'courses';
    protected $fillable = [
        'curriculumId',
        'courseCode',
        'courseName',
        'units',
        'labUnits',
        'yearLevel',
        'semester',
        'courseType',
        'isRequired',
    ];
    protected $casts = ['isRequired' => 'boolean'];

    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class, 'curriculumId');
    }
}