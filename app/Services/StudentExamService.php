<?php

namespace App\Services;

use App\Models\StudentExam;

class StudentExamService extends ServiceHelper{
    public function __construct()
    {
        $this->model=new StudentExam();
        $this->searchBy=['studentId','examId'];
        $this->orderBy='created_at';
        $this->isAscending=false;
        $this->attributes=[
            'id',
            'studentId',
            'examId',
            'mark',
            'isPassed'
        ];
    }
}
