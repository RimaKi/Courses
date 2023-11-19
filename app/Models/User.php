<?php

namespace App\Models;

use App\Http\Controllers\HelperController;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'uniqueId',
        'birthday',
        'isMale',
        'photo',
        'education',
        'phone',
        'score'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
        'email_verified_at',
        'rowPhoto'

    ];
    protected $appends=['rowPhoto'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getAnswersAttribute(){
        return $this->hasMany(Answer::class,'studentId','uniqueId')->get();
    }

    public function getQuestionAttribute(){
        return $this->hasMany(Question::class,'createdBy','uniqueId')->get();
    }

    public function getPhotoAttribute(){
        $this->attributes['rowPhoto']=$this->attributes['photo'];
        return HelperController::viewPhoto($this->attributes['photo']);
    }
}
