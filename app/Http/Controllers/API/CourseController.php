<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Goal;
use App\Models\Objective;
use App\Services\CollegeService;
use App\Services\CourseServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class CourseController extends Controller
{
    public function add(Request $request)
    {
        $data = $request->only(['name','departmentId','goalIds','newGoals','level']);
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'goalIds' => [ 'string', 'max:255'],
            'departmentId' => ['required'],
//            'newGoals'=>['string'],
            'level'=>['numeric','min:1','max:3']
        ], $data);
        $newGoals=json_decode($data['newGoals']);
        $goals=[];
        foreach ($newGoals as $goal){
            $g=new Goal();
            $g->name=$goal->name;
            $g->level=$data['level'];
            $g->save();
            $goals[]=$g->id;
            foreach ($goal->objectives as $object){
                $ob=new Objective();
                $ob->name=$object;
                $ob->goalId=$g->id;
                $ob->save();
            }
        }
        if(!$request->has('goalIds')){
            $data['goalIds']=implode(';',$goals);
        }else{
            $data['goalIds']=$data['goalIds'] . ( count($goals) > 0 ? (';'.implode(';',$goals)) : "" );
        }
        $data['uniqueId'] = Str::random(12);
        if (!(new CourseServices())->save($data)) {
            throw new \Exception('Failed');
        }
        return response()->json([
            'error' => 0,
            'msg' => 'successfully added'
        ]);


    }

    public function edit(Request $request)
    {
        $data = $request->only(['uniqueId', 'name','goalIds']);
        $request->validate([
            'uniqueId' => ['required', 'string', 'max:255'],
            'name' => ['string', 'max:255'],
            'goalIds' => ['string', 'max:255'],
            'departmentId' => ['string', 'max:255'],
        ], $data);
        if (!(new CourseServices())->update($data, ['uniqueId' => $data['uniqueId']])) {
            throw new \Exception('Failed');
        }
        return response()->json([
            'error' => 0,
            'msg' => 'successfully updated'
        ]);


    }

    public function view(Request $request)
    {
        $data = $request->only(['collegeId','departmentId']);
        $request->validate([
            'collegeId' => ['string', 'max:255'],
            'departmentId' => ['string', 'max:255'],
        ], $data);
        if ($request->has("collegeId") && !$request->has("departmentId")) {
            $result = [];
            $departments = (new CollegeService())->getOne($data["collegeId"])->departments;
            foreach ($departments as $d) {
                $result = [...$result, ...$d->courses->toArray()];
            }
            return response()->json([
                'error' => 0,
                'Courses' => $result
            ]);
        }
        $courses = (new CourseServices())->getListQuery();
        if ($request->has('departmentId')) {
            $courses = $courses->where('departmentId',$data['departmentId']);
        }
        return response()->json([
            'error' => 0,
            'Courses' => $courses->get()
        ]);


    }

    public function delete(Request $request)
    {
        $data = $request->only(['uniqueId']);
        $request->validate([
            'uniqueId' => ['required', 'string']
        ], $data);
        if (!(new CourseServices())->delete(['uniqueId' => $data['uniqueId']])) {
            throw new \Exception('Failed');
        }
        return response()->json([
            'error' => 0,
            'msg' => 'Successfully Deleted'
        ]);
    }
}
