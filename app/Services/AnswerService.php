<?php

namespace App\Services;

use App\Models\Answer;

class AnswerService extends ServiceHelper{
    public function __construct()
    {
        $this->model=new Answer();
        $this->searchBy=['studentId','questionId'];
        $this->attributes=[
            'id',
            'studentId',
            'questionId',
            'optionId',
            'duration',
            'studentExamId',
            'examId'
        ];
    }
}
