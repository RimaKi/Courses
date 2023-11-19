<?php

namespace App\Models;

use App\Http\Controllers\HelperController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;
    protected $fillable=[
        'name',
        'unitId',
        'description',
        'photo',
        'video',
        'goalId',
        'courseId',
        'objectiveId'
    ];

    protected $hidden=[ //TODO NEW (removed goalID)
        'rowPhoto',
        'rowVideo'
    ];
    protected $appends=[
        'rowPhoto',
        'rowVideo'
    ];

    public function getCourseAttribute(){
        return $this->hasOne(Course::class,'uniqueId','courseId')->first();
    }

    public function getUnitAttribute(){
        return $this->hasOne(Unit::class,'id','unitId')->first();
    }

    public function getObjectiveAttribute(){
        return $this->hasOne(Objective::class,'id','objectiveId')->first();
    }
    public function getRowPhotoAttribute(){
        return $this->attributes['photo'];
    }
    public function getPhotoAttribute(){
        return HelperController::viewPhoto($this->attributes['photo']);
    }
    public function getRowVideoAttribute(){
        return $this->attributes['photo'];
    }
    public function getVideoAttribute(){
        return HelperController::viewPhoto($this->attributes['video']);
    }
}
