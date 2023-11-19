<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\UnitService;
use App\Services\UserService;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function add(Request $request)
    {
        $data = $request->only(['teacherCourseId', 'name', 'level','goalId']);
        $request->validate([
            'teacherCourseId' => ['required','numeric'],
            'name' => ['required', 'string', 'max:255'],
            'level' => ['required', 'numeric'],
            'goalId' => ['required', 'numeric']
        ], $data);

        if (!(new UnitService())->save($data)) {
            throw new \Exception('already exit');
        }
        return response()->json([
            'error' => 0,
            'msg' => 'save successfully'
        ]);
    }

    public function edit(Request $request)
    {
        $data = $request->only(['id', 'teacherCourseId', 'name', 'level','goalId']);
        $request->validate([
            'id' => ['required', 'numeric'],
            'teacherCourseId' => ['numeric'],
            'name' => ['string', 'max:255'],
            'level' => ['numeric'],
            'goalId' => ['numeric'],
        ], $data);
        if (!(new UnitService())->update($data, ['id' => $data['id']])) {
            throw new \Exception('failed');
        }
        return response()->json([
            'error' => 0,
            'msg' => 'update successfully'
        ]);
    }

    public function view(Request $request,$id = null)
    {
        if ($id != null) {
            return response()->json([
                'error' => 0,
                'units' => (new UnitService())->getFirst(['id' => $id])
            ]);
        }
        $data=$request->only(['teacherCourseId','goalId']);
        $request->validate([
            'teacherCourseId'=>['numeric']
        ],$data);
        $units=(new UnitService())->getListQuery();
        if($request->has('teacherCourseId')&&$data['teacherCourseId']){
            $units=$units->where('teacherCourseId',$data['teacherCourseId']);
        }
        if($request->has('goalId')&&$data['goalId']){
            $units=$units->where('goalId',$data['goalId']);
        }
        return response()->json([
            'error' => 0,
            'units' => $units->get()
        ]);
    }

    public function delete(Request $request)
    {
        $data = $request->only(['id']);
        $request->validate([
            'id' => ['required', 'string']
        ]);
        if (!(new UnitService())->delete(['id' => $data['id']])) {
            throw new \Exception('failed');
        }
        return response()->json([
            'error' => 0,
            'msg' => 'deleted successfully'
        ]);
    }

}
