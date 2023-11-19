<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\AnswerService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AnswerController extends Controller
{
    //student
    public function view(Request $request){
        $data=$request->only(['studentId']);
       $answers=(new AnswerService())->getListQuery(['studentId'=>$data['studentId']])->whereDate('created_at',Carbon::today())->get();
       return response()->json([
           'error'=>0,
           'answers'=>$answers
       ],200);

    }

    public function views(Request $request){
        $data=$request->only(['questionId','optionId','studentId']);
        $request->validate([
            'questionId'=>['string'],
            'optionId'=>['numeric'],
            'studentId'=>['string']
        ],$data);

        $answers=(new AnswerService())->getListQuery();
        if($request->has('questionId')){
            $answers=$answers->where('questionId',$data['questionId']);
        }
        if($request->has('optionId')){
            $answers=$answers->where('optionId',$data['optionId']);
        }
        if($request->has('studentId')){
            $answers=$answers->where('studentId',$data['studentId']);
        }
        return response()->json([
            'error'=>0,
            'answers'=>$answers->get()
        ]);
    }

}
