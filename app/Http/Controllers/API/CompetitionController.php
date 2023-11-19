<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\CompetitionService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CompetitionController extends Controller
{
    public function edit(Request $request)
    {
        $data = $request->only(['id', 'questionIds', 'examId', 'startsOn', 'title']);
        $request->validate([
            'id' => ['required', 'numeric'],
            'questionIds' => ['string'],
            'examId' => ['numeric'],
            'startsOn' => ['date'],
            'title' => ['required', 'string', 'max:255']
        ], $data);
        if (!(new CompetitionService())->update($data, ['id' => $data['id']])) {
            throw new \Exception('failed');
        }
        return response()->json([
            'error' => 0,
            'msg' => 'update successfully'
        ], 200);
    }

    public function view(Request $request)
    {
        $data = $request->only(['start', 'end', 'title','id']);
        $request->validate([
            'start' => ['date'],
            'end' => ['date'],
            'title' => ['string'],
            'id'=>['numeric']
        ], $data);
        $competitions = (new CompetitionService())->getListQuery();
        if ($request->has('id')) {
            $competitions = $competitions->whereDate('id', '>=', $data['id']);
        } if ($request->has('start')) {
            $competitions = $competitions->whereDate('startsOn', '>=', $data['start']);
        }
        if ($request->has('end')) {
            $competitions = $competitions->whereDate('startsOn', '<=', $data['end']);
        }
        if ($request->has('title')) {
            $competitions = $competitions->whereDate('title', '=', $data['title']);
        }
        return response()->json([
            'error' => 0,
            'Competitions' => $competitions->get()
        ], 200);

    }

    public function delete(Request $request)
    {
        $data = $request->only(['id']);
        $request->validate([
            'id' => ['numeric']
        ], $data);
        if (!(new CompetitionService())->delete(['id' => $data['id']])) {
            throw new \Exception('Failed');
        }
        return response()->json([
            'error' => 0,
            'msg' => 'Successfully Deleted'
        ], 200);
    }

    public function viewCompetitionQuestions(Request $request)
    {
        $data = $request->only(['id']);
        $request->validate([
            'id' => ['required', 'numeric'],
            'secretKey' => ['required', 'string']
        ], $data);
        $competition = (new CompetitionService())->getOne($data['id']);
        $isEarly = Carbon::make($competition->startsOn)->addMinutes(15) >= Carbon::now();
        if($competition->secretKey != $data['secretKey']){
            throw new \Exception('the key is not correct');
        }
        if ($competition->startsOn <= Carbon::now() && $isEarly ) {
            return response()->json([
                'error' => 0,
                'questions' => $competition->questions
            ]);
        }
        return response()->json([
            'error' => 0,
            'msg' => $isEarly ? "too early!!" : 'too Late!!'
        ]);
    }

    public function getKey(Request $request)
    {
        $data = $request->only('id');
        $request->validate([
            'id' => ['required', 'numeric']
        ]);
        return response()->json([
            'error' => 0,
            'secretKey' => (new CompetitionService())->getOne($data['id'])->secretKey
        ]);
    }

}
