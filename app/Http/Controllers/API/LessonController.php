<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\LessonServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LessonController extends Controller
{
    public function add(Request $request){
        $data = $request->only(['name','unitId','description','photo','video','goalId','objectiveId','courseId']);
        $request->validate([
            'name'=>['required','string','max:255'],
            'unitId'=>['required','numeric'],
            'description'=>['required','string'],
            'photo'=>['image','mimes:jpeg,png,jpg'],
            'video'=>['file','mimes:mp4,mov'],
            'goalId'=>['required','numeric'],
            'courseId'=>['required','string','max:255'],
            'objectiveId'=>['string','max:255'],
        ],$data);
        if($request->has('photo')){
            $data['photo']=(new LessonServices())->saveFile('photo','/lesson/photos',$request->allFiles());
        }
        if($request->has('video')){
            $data['video']=(new LessonServices())->saveFile('video','/lesson/videos',$request->allFiles());
        }

        if(! (new LessonServices())->save($data)){
            throw new \Exception('Added Filed');
        }
        return response()->json([
           'error'=>0,
            'msg'=>'Added Successfully'
        ]);
    }
    public function edit(Request $request){
        $data = $request->only(['id','name','unitId','description','photo','video','objectiveId','goalId','courseId']);
        $request->validate([
            'id'=>['required','numeric'],
            'name'=>['string','max:255'],
            'unitId'=>['string','max:255'],
            'description'=>['string','max:255'],
            'photo'=>['image','mimes:jpeg,png,jpg'],
            'video'=>['file','mimes:mp4,mov'],
            'goalId'=>['string','max:255'],
            'courseId'=>['string','max:255'],
            'objectiveId'=>['string','max:255'],
        ],$data);
        if( $request->hasFile('photo')){
            $photo = (new LessonServices())->getFirst(['id' => $data['id']])->rowPhoto;
            if (Storage::exists($photo)) {
                Storage::disk('public')->delete($photo);
            }
            $data['photo']=(new LessonServices())->saveFile('photo','/lesson/photos',$request->allFiles());
        }
        if( $request->hasFile('video')){
            $video = (new LessonServices())->getFirst(['id' => $data['id']])->rowVideo;
            if (Storage::exists($video)) {
                Storage::disk('public')->delete($video);
            }
            $data['video']=(new LessonServices())->saveFile('photo','/lesson/videos',$request->allFiles());
        }
        if(! (new LessonServices())->update($data,['id'=>$data['id']])){
            throw new \Exception('not edited');
        }
        return response()->json([
            'error'=>0,
            'msg'=>'Edited Successfully'
        ]);
    }

    public function delete(Request $request){
        $data=$request->only('id');
        $request->validate([
            'id'=>['required','numeric']
        ],$data);
        if(! (new LessonServices())->delete(['id'=>$data['id']])){
            throw new \Exception('deleted failed');
        }
        return response()->json([
           'error'=>0,
           'msg'=>'deleted Successfully'
        ]);
    }

    public function view(Request $request , $uniqueId = null){
        if( $uniqueId != null){
            return response()->json([
               'error'=>0,
               'msg'=> (new LessonServices())->getFirst(['id'=>$uniqueId])
            ]);
        }
        $data=$request->only('search','goalId','objectiveId');
        $request->validate([
           'search'=>['string','max:255'],
            'goalId'=>['numeric'],
            'objectiveId'=>['numeric']
        ],$data);
        $lessons=(new LessonServices())->getListQuery();
        if($request->has('search')){
            $lessons=(new LessonServices())->getListQuery(['keyword'=>$data['search']]);
        }
        if($request->has('goalId')){
            $lessons=$lessons->where('goalId',$data['goalId']);
        }
        if($request->has('objectiveId')){
            $lessons=$lessons->where('objectiveId',$data['objectiveId']);
        }
        return response()->json([
           'error'=>0,
           'msg'=>$lessons->get()
        ]);
    }
}
