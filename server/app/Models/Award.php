<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Award extends Model
{
    protected $table = 'awards';
    protected $fillable = ['userId', 'title', 'awardingDate', 'awardingOrganization', 'awardingLocation'];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }
}