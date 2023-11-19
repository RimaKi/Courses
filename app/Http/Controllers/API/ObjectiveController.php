<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\GoalService;
use App\Services\ObjectiveService;
use Illuminate\Http\Request;

class ObjectiveController extends Controller
{
    public function add(Request $request)
    {
        $data = $request->only(['name', 'goalId']);
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'goalId' => ['required', 'numeric'],
        ], $data);
        $objective = (new ObjectiveService())->getFirst(['name' => $data['name'], 'goalId' => $data['goalId']]);
        if ($objective != null || $objective != '') {
            throw new \Exception('already exit');
        }
        if (!(new ObjectiveService())->save($data)) {
            throw new \Exception('Failed');
        }
        return response()->json([
            'error' => 0,
            'msg' => 'successfully added'
        ]);


    }

    public function edit(Request $request)
    {
        $data = $request->only(['id', 'name', 'goalId']);
        $request->validate([
            'id' => ['required', 'numeric'],
            'name' => ['string', 'max:255'],
            'goalId' => ['numeric'],
        ], $data);
        if (!(new ObjectiveService())->update($data, ['id' => $data['id']])) {
            throw new \Exception('Failed');
        }
        return response()->json([
            'error' => 0,
            'msg' => 'successfully updated'
        ]);


    }

    public function view()
    {
        return response()->json([
            'error' => 0,
            'objective' => (new ObjectiveService())->getList()

        ]);
    }

    public function delete(Request $request)
    {
        $data = $request->only(['id']);
        $request->validate([
            'id' => ['required','numeric']
        ], $data);
        if (!(new ObjectiveService())->delete(['id' => $data['id']])) {
            throw new \Exception('Failed');
        }
        return response()->json([
            'error' => 0,
            'msg' => 'Successfully Deleted'
        ]);
    }
}
