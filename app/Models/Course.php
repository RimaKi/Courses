<?php

namespace App\Models;

use App\Services\GoalService;
use App\Services\LessonServices;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $primaryKey = 'uniqueId';
    protected $fillable = [
        'uniqueId',
        'name',
        'goalIds',
        'departmentId'
    ];
    protected $appends=[
        'goals',
        'hasExam'
    ];


    public function getLessonsAttribute()
    {
        return $this->hasMany(Lesson::class, 'courseId', 'uniqueId')->first();
    }

    public function getDepartmentAttribute()
    {
        return $this->hasOne(Department::class, 'id', 'departmentId')->firstOrFail();
    }
    public function getTeacherCourseAttribute()
    {
        return $this->hasMany(TeacherCourse::class, 'courseId', 'uniqueId')->get();
    }
    public function getGoalsAttribute(){
      $goalIds=explode(';',$this->attributes['goalIds']);
      $goals=[];
      foreach($goalIds as $goalId){
          $goals[]=(new GoalService())->getOne($goalId);
      }
      return $goals;
    }

    public function getHasExamAttribute(){
        $exam = $this->hasOne(Exam::class, 'courseId', 'uniqueId')->first();
        if ($exam != null) {
            return true;
        }
        return false;
    }
}
