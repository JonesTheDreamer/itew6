<?php

namespace App\Repositories;

use App\Models\College;

class CollegeRepository extends BaseRepository
{
    public function __construct() { parent::__construct(new College()); }
}
