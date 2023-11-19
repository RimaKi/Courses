<?php

namespace App\Services;

use App\Models\Option;
use Illuminate\Support\Str;

class OptionService extends ServiceHelper{
    public function __construct()
    {
        $this->model=new Option();
        $this->searchBy=['content','isCorrect'];
        $this->attributes=[
            'id',
            'questionId',
            'content',
            'isPhoto',
            'isCorrect'
        ];
    }

}
