<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class College extends Model
{
    use HasFactory;
    protected $fillable=[
        'id',
        'name'
    ];
    protected $appends=[
        'departments'
    ];
    protected $hidden=[
        'created_at',
        'updated_at'
    ];
    public function getDepartmentsAttribute(){
        return $this->hasMany(Department::class,'collegeId','id')->get();
    }
}
