<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\CollegeService;
use Illuminate\Http\Request;

class CollegeController extends Controller
{
    public function add(Request $request)
    {
        $data = $request->only(['name']);
        $request->validate([
            'name' => ['required', 'string', 'max:255'],

        ], $data);
        $college = (new CollegeService())->getFirst(['name' => $data['name']]);
        if ($college != null || $college != '') {

            throw new \Exception("the $college->name is already exist");
        }
        if (!(new CollegeService())->save($data)) {
            throw new \Exception('Failed');
        }
        return response()->json([
            'error' => 0,
            'msg' => 'successfully added'
        ]);


    }

    public function edit(Request $request)
    {
        $data = $request->only(['id', 'name']);
        $request->validate([
            'id' => ['required', 'numeric'],
            'name' => ['string', 'max:255'],
        ], $data);
        if (!(new CollegeService())->update($data, ['id' => $data['id']])) {
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
                'college' => (new CollegeService())->getOne($id)
            ]);
        }
        return response()->json([
            'error' => 0,
            'colleges' => (new CollegeService())->getList()
        ]);


    }

    public function delete(Request $request)
    {
        $data = $request->only(['id']);
        $request->validate([
            'id' => ['numeric']
        ], $data);

        if (!(new CollegeService())->delete(['id' => $data['id']])) {
            throw new \Exception('Failed');
        }
        return response()->json([
            'error' => 0,
            'msg' => 'Successfully Deleted'
        ]);
    }
}
