<?php

namespace App\Repositories;

use App\Models\UserOrganization;
use Illuminate\Database\Eloquent\Collection;

class UserOrganizationRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new UserOrganization());
    }

    public function getByUser(int $userId): Collection
    {
        return UserOrganization::where('userId', $userId)->with('organization')->get();
    }

    public function getByOrganization(int $organizationId): Collection
    {
        return UserOrganization::where('organizationId', $organizationId)
            ->with('user.student.program', 'user.faculty')
            ->get();
    }
}