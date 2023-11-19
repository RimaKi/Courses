<?php

namespace App\Services;


use App\Models\Competition;

class CompetitionService extends ServiceHelper {
    public function __construct() {
        $this->model = new Competition();
        $this->attributes = [
            'id',
            'questionIds',
            'examId',
            'title',
            'startsOn',
            'secretKey'
        ];
    }


}
