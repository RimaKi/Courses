<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\DepartmentService;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{

    public function add(Request $request)
    {
        $data = $request->only(['name', 'collegeId']);
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'collegeId' => ['required', 'numeric'],
        ], $data);

        $department = (new DepartmentService())->getFirst(['name' => $data['name'], 'collegeId' => $data['collegeId']]);
        if ($department != null || $department != '') {

            throw new \Exception("the $department->name is already exist");
        }
        if (!(new DepartmentService())->save($data)) {
            throw new \Exception('Failed');
        }
        return response()->json([
            'error' => 0,
            'msg' => 'successfully added'
        ]);


    }

    public function edit(Request $request)
    {
        $data = $request->only(['id', 'name', 'collegeId']);
        $request->validate([
            'id' => ['required', 'numeric'],
            'name' => ['string', 'max:255'],
            'collegeId' => ['numeric'],
        ], $data);
        if (!(new DepartmentService())->update($data, ['id' => $data['id']])) {
            throw new \Exception('Failed');
        }
        return response()->json([
            'error' => 0,
            'msg' => 'successfully updated'
        ]);


    }

    public function view(Request $request, $id = null)
    {
        if ($id != null) {
            return response()->json([
                'error' => 0,
                'department' => (new DepartmentService())->getOne($id)
            ]);
        }
        $data = $request->only(['name']);
        $departments = (new DepartmentService())->getListQuery();
        $request->validate([
            'name' => ['string', 'max:255'],
        ], $data);
        if ($request->has('name')) {
            $departments = $departments->where('name', "LIKE", $data['name'] . "%");
        }
        return response()->json([
            'error' => 0,
            'departments' => $departments->get()
        ]);


    }

    public function delete(Request $request)
    {
        $data = $request->only(['id']);
        $request->validate([
            'id' => ['numeric']
        ], $data);
        if (!(new DepartmentService())->delete(['id' => $data['id']])) {
            throw new \Exception('Failed');
        }
        return response()->json([
            'error' => 0,
            'msg' => 'Successfully Deleted'
        ]);
    }
}
