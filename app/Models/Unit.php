<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $appends = [
      "lessons"
    ];

    protected $fillable=[
        'teacherCourseId',
        'name',
        'level',
        'goalId'
    ];
    public function getTeacherCourseAttribute(){
        return $this->hasOne(TeacherCourse::class,'id','teacherCourseId')->firstOrFail();
    }
    public function getLessonsAttribute(){
        return $this->hasMany(Lesson::class,'unitId','id')->get();
    }
    public function getGoalAttribute(){
        return $this->hasOne(Goal::class,'id','goalId')->firstOrFail();
    }
}
