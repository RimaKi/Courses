<?php

namespace App\Services;

use App\Models\Goal;
use App\Services\ServiceHelper;

class GoalService extends ServiceHelper {
    public function __construct() {
        $this->model = new Goal();
        $this->searchBy = ['name'];
        $this->attributes = [
            'id',
            'name',
            'level',
            'created_at',
            'updated_at'
        ];
    }

}
