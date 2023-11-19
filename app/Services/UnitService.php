<?php

namespace App\Services;

use App\Models\Unit;

class UnitService extends ServiceHelper{
    public function __construct()
    {
        $this->model=new Unit();
        $this->searchBy=['teacherCourseId','name'];
        $this->attributes=[
            'id',
            'teacherCourseId',
            'name',
            'level',
            'goalId'
        ];
    }
}
