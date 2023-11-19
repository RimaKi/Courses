<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function changeUserType(Request $request)
    {
       $data= $request->only('uniqueId', "role");
       $request->validate([
           'uniqueId'=>["required",'string'],
           'role'=>["required",'numeric', "min:1" , "max:4"]
       ],$data);
       $role = $request->get("role");
       $role = $role == 1 ? "admin" : ($role == 2 ? "director" : ($role == 3 ? "teacher" : "student"));
       $user=(new UserService())->getFirst(['uniqueId'=>$data['uniqueId']])->syncRoles($role);
        return response()->json([
            'error'=>0,
            'msg'=>'successfully assigned to ' . $role
        ]);

    }
}
