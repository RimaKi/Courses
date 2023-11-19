<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\StudentExamService;
use Illuminate\Http\Request;


class StudentExamsController extends Controller
{


    public function view(Request $request){
        $data=$request->only(['studentId','id','examId']);
        $request->validate([
            'id' =>['numeric'],
            'studentId' => [ 'string', 'max:255'],
            'examId'=> ['numeric']
        ]);
        $studentExam=(new StudentExamService())->getListQuery();
        if ($request->has('id')){
            $studentExam = $studentExam->where('id',$data['id']);
        }
        if ($request->has('studentId')){
            $studentExam = $studentExam->where('studentId',$data['studentId']);
        }
        if ($request->has('examId')){
            $studentExam = $studentExam->where('examId',$data['examId']);
        }
        return response()->json([
            'error' => 0,
            'StudentExams' => $studentExam,
        ]);
    }

    public function delete(Request $request){
        $data=$request->only(['id']);
        $request->validate([
            'id' =>['numeric'],
        ]);
        if( !(new StudentExamService())->delete(['id'=>$data['id']])){
            throw new \Exception('Failed');
        }
        return response()->json([
            'error'=>0,
            'msg'=>'Successfully Deleted'
        ]);
    }


    }
