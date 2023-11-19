<?php

namespace App\Services;
use App\Models\Objective;
use App\Services\ServiceHelper;

class ObjectiveService extends ServiceHelper {
    public function __construct() {
        $this->model = new Objective();
        $this->searchBy = ['name'];
        $this->attributes = [
            'id',
            'name',
            'goalId',
            'created_at',
            'updated_at'
        ];
    }

}
