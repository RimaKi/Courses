<?php

namespace App\Services;

use App\Models\Lesson;
use App\Services\ServiceHelper;


class LessonServices extends ServiceHelper {
    public function __construct() {
        $this->model = new Lesson();
        $this->searchBy = ['name','description'];
        $this->orderBy='created_at';
        $this->attributes = [
            'id',
            'name',
            'unitId',
            'description',
            'photo',
            'video',
            'goalId',
            'objectiveId',
            'courseId'

        ];
    }


}
