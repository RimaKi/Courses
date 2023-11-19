<?php

namespace App\Models;

use App\Services\GoalService;
use App\Services\ObjectiveService;
use App\Services\QuestionService;
use App\Services\UnitService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'goalIds',
        'objectiveIds',
        'quantity',
        'createdBy',
        'courseId',
        'unitId',
        'succeedMark',
        'isCompetition',
        'totalMark',
    ];

    protected $hidden = [
        'courseId',
        'unitId',
    ];
    protected $appends=[
        'Director',
        'GoalsOrObjectives'
    ];

    public function getGoalsOrObjectivesAttribute()
    {
        if ($this->attributes['objectiveIds'] == null && $this->attributes['goalIds'] == null) {
            throw new \Exception('there are no models');
        }
        $GOO = $this->attributes['goalIds'] != null && $this->attributes['objectiveIds'] == null ? explode(';', $this->attributes['goalIds']) : explode(';', $this->attributes['objectiveIds']);
        $r = [];//2:20-3,N;3:30-2,I
        //goalId:questionNumber-level,isEssential;.....
        foreach ($GOO as $goo) {
            $Id = explode(':', $goo)[0];
            $g=(new GoalService())->getOne($Id);
            $part = explode('-', explode(':', $goo)[1]);
            $typeQuestion = explode(',', $part[1])[1];
            $isEssential = $typeQuestion == 'I' ? 1 : 0;
            $r[] = ['Id' => $Id,
                'goal'=>$g,
                'percent' => $part[0], //TODO change to numberOfQuestions
                'level' => explode(',', $part[1])[0],
                'isEssential' => $isEssential];
        }
        return $r;
    }

    public function getDirectorAttribute()
    {
        return $this->hasOne(User::class, 'uniqueId', 'createdBy')->firstOrFail();
    }

    public function getUnitAttribute()
    {
        return $this->hasOne(Unit::class, 'id', 'unitId')->first();
    }

    public function getCourseAttribute()
    {
        return $this->hasOne(Course::class, 'uniqueId', 'courseId')->first();
    }

    private function mark($num = null)
    {

        $sum = 0;
        $result = [];
        $total = 0;

        $goals = $this->getGoalsOrObjectivesAttribute();
        $qs = [];
        $sumQuestions = 0;
        foreach ($goals as $goal) {
            $sumQuestions += $goal['percent'];
            $m = ($num ?? $goal["percent"]) * $goal['level'];
            if ($goal['isEssential'] == 1) $m *= 1.2;
            $sum += $m;
            if ($num != null) {
                $result[] = $m;
                $total += $goal["percent"] * $m;
                $questions = $this->getAllQuestions($goal['Id'], $goal['level'], $goal['isEssential'], $this->attributes['courseId'] , $this->attributes['unitId']);
                $questions = $questions->get()->shuffle()->take($goal['percent']);
                $qq = [];

                foreach ($questions as $question) {
                    $q = array_merge($question->toArray(), ['mark' => $m]);
                    $qq[] = $q;
                }
                $qs = array_merge($qs, $qq);
            }
        }
        return [
            "goalMark" => $result,
            "sum" => $sum,
            "total" => $total,
            'questions' => $qs,
            'sumQuestions' => $sumQuestions
        ];
    }

    public function getQuestionsAttribute()
    {
        $markQuestion = $this->attributes['totalMark'] / $this->mark()["sum"];
        $result = $this->mark($markQuestion);
        if ($result['sumQuestions'] != $this->attributes['quantity']) {
            throw new \Exception('number Questions are not  equal quantity ');
        }
        return $result;
    }

    public function getAllQuestions($id, $level, $isEssential, $courseId, $unitId)
    {
        if ($this->attributes['unitId'] != null) {
            $unit = (new UnitService())->getOne($unitId);

            $courseId = $unit->teacherCourse->courseId;
        }
        $questions = (new QuestionService())->getListQuery();

        if ($courseId!= null) {

            $questions = $questions->where('courseId', $courseId);
        }
        if($this->attributes['goalIds'] != null || $this->attributes['goalIds'] != ''){
            $questions = $questions->where('goalId', $id);
        }
        if($this->attributes['objectiveIds'] != null || $this->attributes['objectiveIds'] != '' ){
            $questions = $questions->where('objectiveId', $id);
        }
        $questions = $questions->where('level', $level);
        $questions = $questions->where('isEssential', $isEssential);
        return $questions;
    }

}
