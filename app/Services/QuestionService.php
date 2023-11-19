<?php

namespace App\Services;

use App\Models\Question;
use App\Services\ServiceHelper;

class QuestionService extends ServiceHelper{
    public function __construct() {
        $this->model = new Question();
        $this->searchBy = ['goalId', 'level'];
        $this->orderBy='created_at';
        $this->attributes = [
            'uniqueId',
            'question',
            'questionPhoto',
            'courseId',
            'goalId',
            'createdBy',
            'level',
            'isEssential',
            'duration',
            'objectiveId',
            'created_at',
            'updated_at'
        ];
    }

}
