<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $table = 'organization';
    protected $fillable = ['collegeId', 'organizationName', 'organizationDescription', 'dateCreated', 'isActive'];
    protected $casts = ['isActive' => 'boolean'];

    public function college()
    {
        return $this->belongsTo(College::class, 'collegeId');
    }
    public function members()
    {
        return $this->hasMany(UserOrganization::class, 'organizationId');
    }
}