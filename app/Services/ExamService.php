<?php

namespace App\Services;

use App\Models\Answer;
use App\Models\Exam;

class ExamService extends ServiceHelper{
    public function __construct()
    {
        $this->model=new Exam();
//        $this->searchBy=['studentId','questionId'];
        $this->orderBy='created_at';
        $this->isAscending=false;
        $this->attributes=[
            'id',
            'goalIds',
            'objectiveIds',
            'quantity',
            'createdBy',
            'courseId',
            'unitId',
            'isCompetition',
            'succeedMark',
            'totalMark'
        ];
    }
}
