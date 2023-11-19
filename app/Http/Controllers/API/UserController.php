<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\FlareClient\Http\Exceptions\NotFound;


class UserController extends Controller
{
    public function singUp(Request $request)
    {
        $data = $request->only(['name','email','password','phone','isMale',
            'birthday','photo','education','password_confirmation']);
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['required', 'digits:10,14',],
            'isMale' => ['required', 'boolean'],
            'birthday' => ['required', 'date'],
            'photo' => ['image', 'mimes:png,jpg,jpeg'],
            'education' => ['string', 'max:255']
        ], $data);

        $data['password'] = Hash::make($data['password']);
        $data['uniqueId'] = md5($data['email']);
        if($request->has('photo')){
            $data['photo']=(new UserService())->saveFile('photo','/avatars',$request->allFiles());
        }
        if(!(new UserService())->save($data)){
            throw new \Exception('failed');
        }
        Auth::attempt([
            'email' => $data['email'],
            'password' => $data['password_confirmation'],
        ]);
        $user = auth()->user();
        $user->assignRole('student');

        return response()->json([
            'error' => 0,
            'msg' => 'singUp successfully',
            'token' => $user->createToken($request->ip() ?? $user->name)->plainTextToken,
            'role'=> "student"
        ]);


    }

    public function logIn(Request $request)
    {
        $data = $request->all();
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ], $data);
        if (!Auth::attempt([
            'email' => $data['email'],
            'password' => $data['password']
        ])) {
            throw new \Exception('wrong email or password');
        }
        $user = auth()->user();

        $role = $user->getRoleNames()[0];
        return response()->json([
            'error' => 0,
            'msg' => 'log in successfully',
            'token' => $user->createToken($request->ip() ?? $user->name)->plainTextToken,
            'role'=>$role
        ]);
    }

    public function logOut()
    {
        \auth()->user()->tokens()->delete();
        return response()->json([
            'error' => 0,
            'msg' => 'logged out successfully'
        ]);
    }
    public function view(){
        $users=(new UserService())->getList();
        $result=[];
        foreach ($users as $user){
            $result[]= ['user'=>$user,'roles'=>$user->getRoleNames()];
        }
        return response()->json([
           'error'=>0,
           'users'=>$result
        ]);
    }

    public function profile($uniqueId = null)
    {
        if ($uniqueId != null) {
            $user = (new UserService())->getFirst(['uniqueId' => $uniqueId]);
            if (!$user) throw new NotFound("user is not exist");
        } else {
            if (!\auth("sanctum")->check()) throw new AuthenticationException();
            $user = \auth("sanctum")->user();
        }
        return response()->json([
            "error" => 0,
            "user" => $user
        ]);
    }

    public function edit(Request $request)
    {
        $data = $request->only(['name','phone','birthday','photo','education']);
        $request->validate([
            'name' => ['string', 'max:255'],
            'phone' => ['digits:10,14',],
            'birthday' => ['date'],
            'photo' => ['image', 'mimes:png,jpg,jpeg'],
            'education' => ['string', 'max:255']
        ], $data);
        if ($request->hasFile("photo")) {
            $photo = (new UserService())->getFirst(['uniqueId' => \auth()->user()->uniqueId])->photo;
            if (Storage::exists($photo)) {
                Storage::disk('public')->delete($photo);
            }
            $data["photo"] = (new UserService())->saveFile("photo", "public/avatars", $request->allFiles());
        }
        $isUpdated = (new UserService())->update($data, ["uniqueId" => \auth()->user()->uniqueId]);

        if (!$isUpdated) {
            throw new \Exception("couldn't update your information");
        }
        return response()->json([
            'error' => 0,
            'msg' => "successfully updated",
        ]);


    }

    public function change_password(Request $request)
    {
        $data = $request->only(['password', 'oldPassword', 'password_confirmation']);
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'max:255', "confirmed"],
            'oldPassword' => ['required', 'string', 'min:8', 'max:255',]
        ], $data);
        $user = auth()->user();
        if (!password_verify($data['oldPassword'], $user->password)) {
            throw new \Exception('wrong password');
        }
        $data['password'] = Hash::make($data['password']);
        if (!(new UserService())->update(['password' => $data['password']], ['uniqueId' => $user->uniqueId])) {
            throw new \Exception('your password not change');
        }
        return response()->json([
            'error' => 0,
            'msg' => 'change password successfully'
        ]);
    }

    public function delete(Request $request){
        $data=$request->only(['uniqueId']);
        $request->validate([
            'uniqueId'=>['required','string']
        ]);
        if(! (new UserService())->delete(['uniqueId'=>$data])){
            throw new \Exception('failed');
        }
        return response()->json([
            'error'=>0,
            'msg'=>'deleted successfully'
        ]);
    }

}


