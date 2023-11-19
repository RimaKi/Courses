<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\OptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use phpDocumentor\Reflection\Types\Expression;

class OptionController extends Controller
{
    public function add(Request $request){
        $data = $request->only(['questionId','photo','isCorrect','option']);
        $request->validate([
            'questionId'=>['required','string','max:255'],
            'option'=>['string'],
            'photo'=>['file','mimes:png,jpg,jpeg'],
            'isCorrect'=>['boolean']
        ],$data);
        if($request->hasFile('photo')){
            $data['content']=(new OptionService())->saveFile('photo','/content',$request->allFiles());
            $data['isPhoto'] = true;
        }
        if($data['option']!=null &&$request->has('option')){
            $data['content']=$data['option'];
        }
        if($data['content']==null){
            throw new \Exception('option or photo is required');
        }

        $option = (new OptionService())->getFirst(['questionId'=>$data['questionId'],'content'=>$data['content']]);
        if($option!=null || $option!=''){
            throw new \Exception('already exist');
        }
        if(! (new OptionService())->save($data)){
            throw new \Exception('Added Filed');
        }
        return response()->json([
            'error'=>0,
            'msg'=>'Added Successfully'
        ]);
    }

    public function edit(Request $request){
        $data = $request->only(['id','questionId','option','photo','isCorrect']);
        $request->validate([
            'id'=>['required','numeric'],
            'questionId'=>['string','max:255'],
            'option'=>['string'],
            'photo'=>['file','mimes:png,jpg,jpeg'],
            'isCorrect'=>['boolean']
        ],$data);
        if( $request->hasFile('photo')){
            $photo = (new OptionService())->getFirst(['id' => $data['id']])->content;
            if (Storage::exists($photo)) {
                Storage::disk('public')->delete($photo);
            }
            $data['content']=(new OptionService())->saveFile('photo','/content',$request->allFiles());
            $data["isPhoto"] = true;
        }
        if($request->has('option') && $data['option']!=null){
            $data['content']=$data['option'];
        }
        if(! (new OptionService())->update($data,['id'=>$data['id']])){
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
        if(! (new OptionService())->delete(['id'=>$data['id']])){
            throw new \Exception('deleted failed');
        }
        return response()->json([
            'error'=>0,
            'msg'=>'deleted Successfully'
        ]);
    }

    public function view(Request $request , $id = null){
        if( $id != null){
            $option= (new OptionService())->getFirst(['id'=>$id]);
            return response()->json([
                'error'=>0,
                'msg'=>$option
            ]);
        }
        $data=$request->only(['search','questionId']);
        $request->validate([
            'search'=>['string','max:255'],
            'questionId'=>['string']
        ],$data);
        $options=(new OptionService())->getListQuery();
        if($request->has('questionId')){
            $options=$options->where('questionId','=',$data['questionId']);
        }
        if($request->has('search')){
            $options=(new OptionService())->getListQuery(['keyword'=>$data['search']]);
        }
        return response()->json([
            'error'=>0,
            'options'=>$options->get()
        ]);
    }
}
