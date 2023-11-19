<?php

namespace App\Models;

use App\Services\ExamService;
use App\Services\LessonServices;
use App\Services\ObjectiveService;
use App\Services\OptionService;
use App\Services\UserService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'studentId',
        'questionId',
        'optionId',
        'duration',
        'studentExamId',

    ];
    protected $hidden = [
        'studentId',
        'questionId',
//        'optionId'
    ];
    protected $appends = [
         'option',
        'student',
        'question',

    ];

    public function getOptionAttribute()
    {
        return $this->hasOne(Option::class, 'id', 'optionId')->firstOrFail();
    }

    public function getStudentAttribute()
    {
        return $this->hasOne(User::class, 'uniqueId', 'studentId')->firstOrFail();
    }

    public function getQuestionAttribute()
    {
        return $this->hasOne(Question::class, 'uniqueId', 'questionId')->firstOrFail();
    }

    public function getStudentExamAttribute()
    {
        return $this->hasOne(StudentExam::class, 'id', 'studentExamId')->first();
    }



    public function getMarkForAnswerAttribute()
    {
        $q = $this->getQuestionAttribute();
        $option = $this->getOptionAttribute();
        $exam = $this->getStudentExamAttribute()->exam;
        $mark = $exam->questions['goalMark'];
        $isTrue=false;
        $isContinue=true;
        $lesson=[];
        $trueOption=[];
        foreach ($exam->goalsOrObjectives as $i => $goal) {
            if (($q->goalId == $goal['Id'] || $q->objectiveId == $goal['Id']) && $q->level == $goal['level'] && $q->isEssential == $goal['isEssential']) {
                if ($option->isCorrect == 1) {
//                    if ($q->duration / 2 >= $this->attributes['duration']) {
//                        $student = (new UserService())->getFirst(['uniqueId' => $this->attributes['studentId']]);
//                        $score = $student->score;
//                        $score += 5;
//                        (new UserService())->update(['score' => $score], ['uniqueId' => $this->attributes['studentId']]);
//                    }
                    $isTrue=true;
                    $answerMark=$mark[$i];
                }
// else {
//                    if ($exam->unitId != null && $goal['isEssential'] == 0) {
//                        $trueOption= (new OptionService())->getFirst(['questionId' => $this->attributes['questionId'], 'isCorrect' => 1]);
//                    } elseif (($exam->unitId != null || $exam->courseId != null) && $goal['isEssential'] == 1) {
//                        if ($exam->goalIds != null) {
//                            $lesson= (new LessonServices())->getList(['goalId' => $q->goalId]);
//                            $isContinue=false;
//                        } elseif ($exam->objectiveIds != null) {
//                            $lesson =(new LessonServices())->getList(['objectiveId' => $q->goalId]);
//                            $isContinue=false;
//                        }
//                    }
//                }
            }
        }
//        return ['isTrue'=>$isTrue,'isContinue'=>$isContinue,'option'=>$trueOption,'suggestion'=>$lesson];



    }

}
