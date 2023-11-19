<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $primaryKey = 'uniqueId';
    public $incrementing = false;
    protected $appends = [
        'options',
        'teacher'
    ];
    protected $fillable = [
        'uniqueId',
        'question',
        'questionPhoto',
        'createdBy',
        'goalId',
        'level',
        'courseId',
        'isEssential',
        'duration',
        'objectiveId'
    ];



    public function getGaolAttribute()
    {
        return $this->hasOne(Goal::class, 'id', 'goalId')->firstOrFail();
    }

    public function getTeacherAttribute()
    {
        return $this->hasOne(User::class, 'uniqueId', 'createdBy')->firstOrFail();
    }

    public function getAnswersAttribute()
    {
        return $this->hasMany(Answer::class, 'questionId', 'uniqueId')->get();
    }

    public function getCourseAttribute()
    {
        return $this->hasMany(Course::class,  'uniqueId','courseId')->get();
    }

    public function getOptionsAttribute()
    {
        return $this->hasMany(Option::class, 'questionId', 'uniqueId')->get();
    }

    public function getCorrectOptionAttribute()
    {
        return $this->hasMany(Option::class, 'questionId', 'uniqueId')->where("isCorrect", "=", 1)->first();
    }

    public function getCorrectOptionIdAttribute()
    {
        return $this->getCorrectOptionAttribute() != null ? $this->getCorrectOptionAttribute()->id : null;
    }

    public function isOptionCorrect($optionId): bool
    {
        $result = false;

        foreach ($this->getOptionsAttribute() as $o) {
            if ($o->id == $optionId) {
                $result = $o->isCorrect;
            }
        }
        return $result;
    }
}
