<?php

namespace App\Models;

use App\Http\Controllers\HelperController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherCourse extends Model
{
    use HasFactory;
    protected $fillable=[
        'name',
        'teacherId',
        'courseId',
        'summary',
        'photo',
    ];
    protected $hidden=[
        'teacherId',
        'rowPhoto'
    ];
    protected $appends=[
        'teacher',
        'course',
        'rowPhoto'
    ];

    public  function getTeacherAttribute(){
        return $this->hasMany(User::class,'uniqueId','teacherId')->get();
    }
    public  function getCourseAttribute(){
        return $this->hasOne(Course::class,'uniqueId','courseId')->firstOrFail();
    }
    public function getRowPhotoAttribute(){
        return $this->attributes['photo'];
    }
    public function getPhotoAttribute(){
        return HelperController::viewPhoto($this->attributes['photo']);
    }
}
