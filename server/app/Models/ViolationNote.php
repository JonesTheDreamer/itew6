<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViolationNote extends Model
{
    protected $table = 'violation_notes';
    protected $fillable = ['violationId', 'note', 'addedBy'];

    public function violation()
    {
        return $this->belongsTo(Violation::class, 'violationId');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'addedBy');
    }
}
