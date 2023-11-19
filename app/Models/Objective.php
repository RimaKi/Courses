<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Objective extends Model
{
    use HasFactory;
    protected $fillable=[
        'id',
        'goalId',
        'name'
    ];
    public function getGoalAttribute(){
        return $this->hasOne(Goal::class,'id','goalId')->firstOrFail();
    }
}
