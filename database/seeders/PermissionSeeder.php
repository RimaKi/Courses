<?php

namespace Database\Seeders;

use App\Models\College;
use App\Models\Course;
use App\Models\Department;
use App\Models\Exam;
use App\Models\Option;
use App\Models\Question;
use App\Models\User;
use Dotenv\Util\Str;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Database\Eloquent\Factories\Factory;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $admin = Role::create(['name' => 'admin']);
        $director = Role::create(['name' => 'director']);
        $teacher = Role::create(['name' => 'teacher']);
        $student = Role::create(['name' => 'student']);




        $user1=new User();
        $user1->name='jd3an';
        $user1->uniqueId='qwerrttt';
        $user1->email='jd3an@gmail.com';
        $user1->password=Hash::make('123123123');
        $user1->birthday='1998-3-3';
        $user1->phone='0997455326';
        $user1->isMale=true;
        $user1->save();
        $user1->assignRole('admin');

        $exam=new Exam();
        $exam->goalIds='1:3-3,N;2:4-2,I;3:3-1,N';
        $exam->createdBy='qwerrttt';
        $exam->quantity='10';
        $exam->succeedMark='60';
        $exam->totalMark='100';
        $exam->courseId='jdgiheriouyh';
        $exam->save();
        $department=new Department();
        $department->name='it';
        $department->collegeId=1;
        $department->save();
        $college=new College();
        $college->name='damascus';
        $college->save();

        $course=new Course();
        $course->uniqueId='jdgiheriouyh';
        $course->name='arbic';
        $course->goalIds='1;2;3;4;5';
        $course->departmentId=1;
        $course->save();

for($i=0;$i<20;$i++){
    $question=new Question();
    $question->uniqueId=\Illuminate\Support\Str::random(20);
    $question->question='blabll'.$i;
    $question->level=1;
    $question->goalId=1;
    $question->createdBy='qwerrttt';
    $question->duration=14;
    $question->save();
    for ($j=0;$j<4;$j++){
        $option=new Option();
        $option->questionId=$question->uniqueId;
        $option->content='hello'.$i.$j;
        $option->save();
    }
}
}

}
