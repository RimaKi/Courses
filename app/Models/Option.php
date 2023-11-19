<?php

namespace App\Models;

use App\Http\Controllers\HelperController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;
    protected $fillable=[
      'questionId',
      'content',
      'isPhoto',
      'isCorrect'
    ];
    protected $hidden=[
        'isCorrect',
    ];
    protected $appends=['photo'];

    public function getAnswerAttribute(){
        return $this->hasOne(Option::class,'optionId','id')->firstOrFail();
    }

    public function getQuestionAttribute(){
        return $this->hasOne(Question::class,'uniqueId','questionId')->firstOrFail();
    }

    public function getPhotoAttribute() {
        $photo = null;
        if($this->attributes['isPhoto']==1){
            $photo = $this->attributes['content'];
        }
        return HelperController::viewPhoto($photo);
    }


}
