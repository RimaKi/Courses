<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\GoalService;
use http\Env\Response;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    public function edit(Request $request)
    {
        $data = $request->only(['id', 'name', 'level']);
        $request->validate([
            'id' => ['required', 'numeric'],
            'name' => ['string', 'max:255'],
            'level' => ['numeric'],
        ], $data);
        if (!(new GoalService())->update($data,['id'=>$data['id']])) {
            throw new \Exception('Failed');
        }
        return response()->json([
            'error' => 0,
            'msg' => 'successfully updated'
        ]);


    }
    public function view(Request $request)
    {
        $data = $request->only(['name','level']);
        $request->validate([
            'name' => ['string', 'max:255'],
            'level' => ['required', 'numeric'],
        ], $data);
        $goals=(new GoalService())->getList(['level'=>$data['level']]);


        if($request->has('name')&& $request->has('level')){
          $goal=(new GoalService())->getFirst($data);
          if($goal==null){
              (new GoalService())->save($data);
          }
        }
        return response()->json([
            'error' => 0,
            'Goals' => (new GoalService())->getList(['level'=>$data['level']])
        ]);
    }
    public function delete(Request $request){
        $data=$request->only(['id']);
        $request->validate([
            'id' => ['numeric']
        ],$data);
        if( !(new GoalService())->delete(['id'=>$data['id']])){
            throw new \Exception('Failed');
        }
        return response()->json([
            'error'=>0,
            'msg'=>'Successfully Deleted'
        ]);
    }
}
