<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentExam extends Model
{
    use HasFactory;
    protected $fillable=[
        'studentId',
        'examId',
        'mark',
        'isPassed'
    ];
    protected $hidden=[
        'studentId',
        'examId',
        'created_at',
        'updated_at'
    ];
    protected $appends=[
        'student',
        'exam'
    ];

    public function getStudentAttribute(){
        return $this->hasOne(User::class ,'uniqueId','studentId')->firstOrFail();
    }

    public function getExamAttribute(){
        return $this->hasOne(Exam::class ,'id','examId')->firstOrFail();
    }

}
