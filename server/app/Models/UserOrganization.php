<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserOrganization extends Model
{
    protected $table = 'user_organization';
    protected $fillable = ['userId', 'organizationId', 'role', 'dateJoined', 'dateLeft'];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }
    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organizationId');
    }
}