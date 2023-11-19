<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    use HasFactory;
    protected $fillable=[
        'id',
        'name',
        'level'
    ];
    protected $hidden=[ //TODO NEW removed id
        'created_at',
        'updated_at'
    ];
    protected $appends=['objectives'];
    public function getQuestionAttribute(){
        return $this->hasMany(Question::class,'goalId','id')->get();
    }
    public function getObjectivesAttribute(){
        return $this->hasMany(Objective::class,'goalId','id')->get();
    }

}
