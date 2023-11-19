<?php

namespace App\Services;


use App\Models\Department;

class DepartmentService extends ServiceHelper{
    public function __construct()
    {
        $this->model=new Department();
        $this->attributes=[
            'id',
            'collegeId',
            'name'
        ];
    }
}
