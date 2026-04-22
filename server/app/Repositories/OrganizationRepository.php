<?php

namespace App\Repositories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class OrganizationRepository extends BaseRepository
{
    public function __construct() { parent::__construct(new Organization()); }

    public function getAll(): Collection
    {
        return Organization::with('college')->get();
    }

    public function getById(int $id): ?Model
    {
        return Organization::with('college')->find($id);
    }

    public function getStats(): array
    {
        return [
            'totalOrganizations'  => Organization::count(),
            'activeOrganizations' => Organization::where('isActive', true)->count(),
        ];
    }
}
