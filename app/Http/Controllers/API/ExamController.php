<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\Answer;
use App\Models\Exam;
use App\Models\StudentExam;
use App\Models\User;
use App\Services\AnswerService;
use App\Services\CompetitionService;
use App\Services\CourseServices;
use App\Services\ExamService;
use App\Services\LessonServices;
use App\Services\ObjectiveService;
use App\Services\OptionService;
use App\Services\QuestionService;
use App\Services\StudentExamService;
use App\Services\TeacherCourseServices;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use function PHPUnit\Framework\returnArgument;

class ExamController extends Controller
{
    public function add(Request $request)
    {
        $data1 = $request->only(['goalIds', 'objectiveIds', 'courseId', 'unitId',
            'totalMark','isCompetition','succeedMark']);
        $data2 = $request->only(['startsOn', 'title']);
        $request->validate([
            'goalIds' => ['string'],
            'objectiveIds' => ['string'],
            'title' => ['string'],
            'courseId' => ['string'],
            'unitId' => ['string'],
            'succeedMark' => ['required', 'numeric'],
            'totalMark' => ['required', 'numeric'],
            'isCompetition' => ['boolean'],
            'startsOn' => ['date']
        ], [...$data1, ...$data2]);
        $data1['createdBy'] = auth()->user()->uniqueId;
        $data1['quantity'] = 0;
        if ($request->has("goalIds")) {
            foreach (explode(";",$data1["goalIds"]) as $g) {
                $data1["quantity"] += explode(":", explode("-", $g)[0])[1];
            }
        }
        if ($request->has("objectiveIds")) {
            foreach (explode(";",$data1["objectiveIds"]) as $g) {
                $data1["quantity"] += explode(":", explode("-", $g)[0])[1];
            }
        }
        if (!(new ExamService())->save($data1)) {
            throw new \Exception('failed');
        }
        if ( $request->has('isCompetition')&&$data1['isCompetition']) {

            $exam = (new ExamService())->getListQuery(['quantity' => $data1['quantity'],
                'succeedMark' => $data1['succeedMark'],
                'totalMark' => $data1['totalMark']]);
            if ($request->has('goalIds')) {
                $exam = $exam->where('goalIds', $data1['goalIds']);
            } elseif ($request->has('objectiveIds')) {
                $exam = $exam->where('objectiveIds', $data1['objectiveIds']);
            }
            $exam = $exam->get()->first();

            $questions = $exam->questions['questions'];
            $qs = [];
            foreach ($questions as $question) {
                $qs[] = $question['uniqueId'];
            }
            $qs = implode(';', $qs);
            $data2['questionIds'] = $qs;
            $data2['examId'] = $exam->id;
            $data2['secretKey'] = Str::random(5);

            if (!(new CompetitionService())->save($data2)) {
                throw new \Exception('failed save');
            }
        }
        return response()->json([
            'error' => 0,
            'msg' => 'added successfully'
        ]);
    }

    public function edit(Request $request)
    {
        $data = $request->only(['id', 'goalIds', 'objectiveIds', 'quantity', 'courseId', 'unitId', 'createdBy', 'totalMark', 'succeedMark']);
        $request->validate([
            'id' => ['required', 'numeric'],
            'goalIds' => ['string'],
            'objectiveIds' => ['string'],
            'quantity' => ['numeric'],
            'courseId' => ['string'],
            'unitId' => ['numeric'],
            'createdBy' => ['string'],
            'isCompetition' =>['boolean'],
            'succeedMark' => ['numeric'],
            'totalMark' => ['numeric']
        ], $data);
        if (!(new ExamService())->update($data, ['id' => $data['id']])) {
            throw new \Exception('failed');
        }
        return response()->json([
            'error' => 0,
            'msg' => 'edit successfully'
        ]);
    }

    public function getQuiz( Request $request){ //TODO Complete this
        $data = $request->only(['courseId', "goalId"]);
        $request->validate([
            'courseId' => ['required'],
            'goalId' => ['required'],
        ], $data);
        $courseId = (new TeacherCourseServices())->getOne($data["courseId"])->course->uniqueId;
        $questions = collect((new QuestionService())->getList(["courseId" => $courseId, "goalId" => $data["goalId"]]))->shuffle()->take(5);
        $result = [];

        return response()->json([
            'error' => 0,
            'result' => $result
        ], 200);
    }

    public function getExamId( Request $request){
        $data = $request->only(['courseId']);
        $request->validate([
            'courseId' => ['required'],
        ], $data);
        $courseId = (new TeacherCourseServices())->getOne($data["courseId"])->course->uniqueId;
        return response()->json([
            'error' => 0,
            'examId' => (new ExamService())->getFirst(["courseId" => $courseId])->id
        ], 200);
    }

    public function delete(Request $request)
    {
        $data = $request->only(['id']);
        $request->validate([
            'id' => ['numeric']
        ], $data);
        if (!(new ExamService())->delete(['id' => $data['id']])) {
            throw new \Exception('Failed');
        }
        return response()->json([
            'error' => 0,
            'msg' => 'Successfully Deleted'
        ], 200);
    }

    public function view($id=null)
    {
        if($id!=null){
            return response()->json([
                'error' => 0,
                'exams' => (new ExamService())->getOne($id)
            ], 200);
        }

        return response()->json([
            'error' => 0,
            'exams' => (new ExamService())->getList()
        ], 200);
    }

    public function getQuestion(Request $request)
    {
        $request->validate([
            'examId' => ["required", 'string'],
            'questionIds' => ['array'],
            'optionId' => ['numeric'],
            "page" => ["numeric", "min:1"]
        ], [$request->all()]);
        $exam = (new ExamService())->getOne($request->get("examId"));
        $isTrue = null;
        $trueOption = null;
        $suggestions = [];
        $question = [];
        $questionIds = $request->get("questionIds") ?? [];

        $page = ($request->get("page") ?? 1);
        $isContinue = ($page < count($questionIds)) || $page == 1;
        if ($request->has("questionIds") && $request->get("questionIds") != null && $request->has("optionId") && $page > 1) {
            $previousQuestion = (new QuestionService())->getOne($request->get("questionIds")[$page - 2]);
            $isTrue = $previousQuestion->isOptionCorrect($request->get("optionId"));
            if (!$isTrue && ($exam->unitId != null || $exam->courseId != null)) {
                if ($previousQuestion->isEssential == 1) {
                    $isContinue = false;
                    $filter = $exam->goalIds != null ? "goalId" : "objectiveId";
                    $suggestions = (new LessonServices())->getList([$filter => $previousQuestion->{$filter}]);
                } else $trueOption = $exam->unitId != null ? $previousQuestion->correctOptionId : null;
            }
        } else {
            $questions = $exam->questions['questions'];
            $question = $questions;
            foreach ($questions as $i => $qqq) {
                $questionIds[] = $qqq['uniqueId'];
            }
        }
        if ($page > 1) {
            foreach ($questionIds as $qid) {
                $question[] = (new QuestionService())->getOne($qid);
            }
        }
        return response()->json([
            "error" => 0,
            "question" => HelperController::paginate($question, $request, 1),
            'questionIds' => $questionIds,
            "isTrue" => $isTrue,
            "isContinue" => $isContinue,
            "correctOption" => $trueOption,
            "suggestions" => $suggestions

        ]);
    }

    public function saveMark(Request $request)
    {
        $data = $request->only(['examId', 'answers']);
        $request->validate([
            'examId' => ['required', 'numeric'],
            'answers' => ['required','string']
        ], $data);
        $exam = (new ExamService())->getOne($data['examId']);
        $goalMark = $exam->questions['goalMark'];

        $goals = $exam->goalsOrObjectives;
        $marks = 0;
        $score = 0;
        foreach (json_decode($request->get('answers')) as $answer) {
            $option = (new OptionService())->getOne($answer->optionId);
            $question = $option->question;
            if ($option->isCorrect == 1) {
                foreach ($goals as $index => $goal) {
                    if (($goal['Id'] == $question->goalId || $goal['Id'] == $question->objectiveId) &&
                        $goal['level'] == $question->level &&
                        $goal['isEssential'] == $question->isEssential) {
                        if ($question->duration / 2 >= $answer->duration) {
                            $user = auth()->user();
                            $user->score += 1;
                            $user->save();
                        }
                        $marks += $goalMark[$index];

                    }
                }
            }
        }

            $student = new StudentExam();
            $student->examId = $exam->id;
            $student->studentId = auth()->user()->uniqueId;
            $student->mark = $marks;
            $student->isPassed = $marks >= $exam->succesedMark;
            $student->save();
            foreach (json_decode($request->get('answers'))  as $option) {
                $op = (new OptionService())->getOne($option->optionId);
                $answer=new Answer();
                $answer->studentId=auth()->user()->uniqueId;
                $answer->studentExamId =$student->id;
                $answer->questionId =$op->questionId;
                $answer->optionId = $option->optionId;
                $answer->duration=$option->duration;
                $answer->save();
            }
            return response()->json([
                'msg' => 'successfully finished exam',
                'mark' => $marks,
                'isSucceed' => $student['isPassed'],
                'score' => $user->score,
            ]);
        }

}
