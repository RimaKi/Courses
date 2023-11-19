<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\TeacherCourse;
use App\Services\ServiceHelper;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class TeacherCourseServices extends ServiceHelper {
    public function __construct() {
        $this->model = new TeacherCourse();
        $this->searchBy = ['name'];
        $this->orderBy='created_at';
        $this->attributes = [
            'id',
            'name',
            'teacherId',
            'courseId',
            'summary',
            'photo',
            'created_at',
            'updated_at'
        ];
    }


}
