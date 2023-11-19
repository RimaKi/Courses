<?php

namespace App\Services;

use App\Models\Answer;
use App\Models\College;

class CollegeService extends ServiceHelper{
    public function __construct()
    {
        $this->model=new College();
        $this->attributes=[
            'id',
            'name'
        ];
    }
}
