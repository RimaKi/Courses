<?php

namespace App\Services;
use App\Models\User;
use App\Services\ServiceHelper;
class UserService extends ServiceHelper {
    public function __construct() {
        $this->model = new User();
        $this->searchBy = ['name','phone','email'];
        $this->attributes = [
            'uniqueId',
            'name',
            'isMale',
            'email',
            'password',
            'phone',
            'photo',
            'education',
            'birthday',
            'score',
        ];
    }


}
