<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Lesson;
use App\Services\ServiceHelper;


class CourseServices extends ServiceHelper {
    public function __construct() {
        $this->model = new Course();
        $this->searchBy = ['name'];
        $this->orderBy='created_at';
        $this->attributes = [
            'uniqueId',
            'name',
            'goalIds',
            'departmentId',
            'created_at',
            'updated_at'
        ];
    }


}
