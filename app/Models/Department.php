<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;
    protected $fillable=[
        'collegeId',
        'name'
    ];
    protected $appends=[
//        'courses'
    ];
    protected $hidden=[
        'created_at',
      '  updated_at'
    ];

    public function getCollegeAttribute(){
        return $this->hasOne(College::class,'id','collegeId')->first();
    }
    public function getCoursesAttribute(){
        return $this->hasMany(Course::class,'departmentId','id')->get();
    }
}
