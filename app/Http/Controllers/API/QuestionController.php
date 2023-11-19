<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\QuestionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QuestionController extends Controller
{
    public function add(Request $request)
    {
        $data = $request->only(['goalId','question','courseId','questionPhoto','level','isEssential','duration','objectiveId']);
        $request->validate([
            'goalId' => ['string', 'max:255'],
            'courseId' => ['string', 'max:255'],
            'level'=>['required', 'numeric','min:1','max:3'],
            'duration'=>['required', 'numeric'],
            'isEssential'=>['boolean'],
            'question' => ['required','string', 'max:255'],
            'questionPhoto' => ['file', 'mimes:png,jpg,jpeg'],
            'objectiveId'=>['numeric']
        ], $data);
        $data['createdBy']=auth()->user()->uniqueId;
        $data['uniqueId'] = md5(Carbon::now());
        $question=(new QuestionService())->getFirst(['question'=>$data['question']]);
        if($question!=null ||$question!=''){
            throw new \Exception('th question is already exit');
        }
        if ($request->hasFile("questionPhoto")) {
            $data["questionPhoto"] = (new QuestionService())->saveFile("questionPhoto","public/QuestionBank",$request->allFiles());
        }
        if (!(new QuestionService())->save($data)) {
            throw new \Exception('Failed');
        }
        return response()->json([
            'error' => 0,
            'msg' => 'successfully added'
        ]);
    }

    public function edit(Request $request)
    {
        $data = $request->only(['goalId','courseId','uniqueId','question','questionPhoto','level','isEssential','duration','objectiveId']);
        $request->validate([
            'uniqueId' => ['required', 'string', 'max:255'],
            'goalId' => [ 'string', 'max:255'],
            'courseId' => [ 'string', 'max:255'],
            'level'=>[ 'numeric'],
            'duration'=>['numeric'],
            'isEssential'=>['boolean'],
            'question' => ['string', 'max:255'],
            'questionPhoto' => ['file', 'mimes:png,jpg,jpeg'],
            'objectiveId'=>['numeric']
        ], $data);
        if (!(new QuestionService())->update($data,['uniqueId'=>$data['uniqueId']])) {
            throw new \Exception('Failed');
        }
        return response()->json([
            'error' => 0,
            'msg' => 'successfully updated'
        ]);
    }

    public function view(Request $request)
    {
        $data = $request->only(['goalId','courseId','objectiveId','level',"createdBy"]);
        $request->validate([
            'goalId' => ['string', 'max:255'],
            'courseId' => ['string', 'max:255'],
            'level'=> ['numeric'],
            'objectiveId'=> ['numeric'],
            'createdBy'=>['string']
        ], $data);
        $questions=(new QuestionService())->getListQuery();
        if($request->has('goalId')){
            $questions=$questions->where('goalId',$data['goalId']);
        }
        if($request->has('courseId')){
            $questions=$questions->where('courseId',$data['courseId']);
        }
        if($request->has('objectiveId')){
            $questions=$questions->where('objectiveId',$data['objectiveId']);
        }
        if($request->has('level')){
            $questions=$questions->where('level',$data['level']);
        }
        if($request->has('createdBy')){
            $questions=$questions->where('createdBy',$data['createdBy']);
        }
        return response()->json([
            'error' => 0,
            'Questions' => $questions->get()
        ]);
    }

    public function delete(Request $request){
        $data=$request->only(['uniqueId']);
        $request->validate([
            'uniqueId' => ['required','string']
        ],$data);
        if( !(new QuestionService())->delete(['uniqueId'=>$data['uniqueId']])){
            throw new \Exception('Failed');
        }
        return response()->json([
            'error'=>0,
            'msg'=>'Successfully Deleted'
        ]);
    }
}
