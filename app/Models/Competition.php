<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Services\QuestionService;


class Competition extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'questionIds',
        'examId',
        'title',
        'startsOn',
        'secretKey'
    ];
    protected $appends = [
      'durations'
    ];
    protected $hidden = [
        'questionIds',
        'examId'
    ];

    public function getQuestionsAttribute()
    {
        $questions = [];
        foreach (explode(';', $this->attributes['questionIds']) as $questionId) {
            $questions[] = (new QuestionService())->getOne($questionId);
        }
        return $questions;
    }

    public function getDurationsAttribute()
    {
        $result = 0;
        foreach ($this->getQuestionsAttribute() as $question) {
            $result += $question->duration;
        }
        return ($result + 600);
    }

    public function getExamAttribute()
    {
        return $this->hasOne(Exam::class, 'id', 'examId')->firstOrFail();

    }
}
