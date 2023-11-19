<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Option;
use App\Models\Question;
use App\Models\TeacherCourse;
use App\Models\Unit;
use App\Services\CourseServices;
use App\Services\TeacherCourseServices;
use Carbon\Carbon;
use http\Env\Response;
use Illuminate\Auth\Events\Validated;
use \Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class TeacherCourseController extends Controller
{
    public function add(Request $request)
    {
        $data = $request->only(['name', 'summary', 'teacherId', 'courseId', 'photo']);
        $request->validate([
            'name' => ['required', 'string', 'max:255'],

            'courseId' => ['required', 'string', 'max:255'],
            'summary' => ['string', 'max:255'],
            'photo' => ['file', 'mimes:png,jpg,jpeg'],
        ], $data);
        $data['teacherId'] = auth()->user()->uniqueId;
        if ($request->hasFile("photo")) {
            $data["photo"] = (new TeacherCourseServices())->saveFile("photo", "/TeacherCourse", $request->allFiles());
        }
        if (!(new TeacherCourseServices())->save($data)) {
            throw new \Exception('Failed');
        }
        return response()->json([
            'error' => 0,
            'msg' => 'successfully added'
        ]);

    }

    public function edit(Request $request)
    {
        $data = $request->only(['id', 'name', 'summary', 'photo']);
        $request->validate([
            'id' => ['required', 'numeric'],
            'name' => ['string', 'max:255'],
            'summary' => ['string', 'max:255'],
            'photo' => ['file', 'mimes:jpg,png,jpeg'],
        ], $data);
        if ($request->has('photo')) {
            $photo = (new TeacherCourseServices())->getFirst(['id' => $data['id']])->rowPhoto;
            if (Storage::exists($photo)) {
                Storage::disk('public')->delete($photo);
            }
            $data['photo'] = (new TeacherCourseServices())->saveFile('photo', '/TeacherCourse', $request->allFiles());
        }
        if (!(new TeacherCourseServices())->update($data, ['id' => $data['id']])) {
            throw new \Exception('Failed');
        }
        return response()->json([
            'error' => 0,
            'msg' => 'successfully updated'
        ]);
    }

    public function view(Request $request)
    {
        $data = $request->only(['id', 'name', 'teacherId', 'courseId']);
        $request->validate([
            'id' => ['numeric'],
            'name' => ['string', 'max:255'],
            'teacherId' => ['string', 'max:255'],
            'courseId' => ['string', 'max:255'],
        ], $data);
        $teacherId = $request->get("teacherId") ?? "";
        if (auth()->check() && !$request->has("teacherId")) {
            $teacherId =  auth()->user()->uniqueId;
        }

        $teacherCourse = (new TeacherCourseServices())->getListQuery();
        if ($request->has('id')) {
            $teacherCourse = $teacherCourse->where('id', $data['id']);
        }
        if ($request->has('courseId')) {
            $teacherCourse = $teacherCourse->where('courseId', $data['courseId']);
        }
        if ($request->has('teacherId')) {
            $teacherCourse = $teacherCourse->where('teacherId', $teacherId);
        }
        if ($request->has('name')) {
            $teacherCourse = $teacherCourse->where('name', $data['name']);
        }
        return response()->json([
            'error' => 0,
            'result' => $teacherCourse->get()
        ]);
    }

    public function delete(Request $request)
    {
        $data = $request->only(['id']);
        $request->validate([
            'id' => ['required', 'numeric']
        ], $data);
        if (!(new TeacherCourseServices())->delete(['id' => $data['id']])) {
            throw new \Exception('Failed');
        }
        return response()->json([
            'error' => 0,
            'msg' => 'Successfully Deleted'
        ]);
    }

    public function addCourse(Request $request)
    {
        $data = json_decode($request->get('addCourse'), true);
        if (array_key_exists('course', $data)) {
            $course = $data['course'];
            $teacherCourse = new TeacherCourse();
            $teacherCourse->courseId = $course['courseId']; //TODO كانت id
            $teacherCourse->name = $course['name'];
            $teacherCourse->summary = $course['summary'];
            $teacherCourse->teacherId = auth()->user()->uniqueId;
            if (array_key_exists('photo', $course)) {
                $teacherCourse->photo = $this->fileSave('teacherCourse', $course['photo']);
            }
            $teacherCourse->save();
            if (array_key_exists('units', $data)) {
                $units = $data['units'];
                foreach ($units as $unit) {
                    $u = new Unit();
                    $u->teacherCourseId = $teacherCourse->id;
                    $u->name = $unit['name'];
                    $u->level = $unit['level'];
                    $u->goalId = $unit['goalId'];
                    $u->save();
                    if (array_key_exists('lessons', $unit)) {
                        foreach ($unit['lessons'] as $lesson) {
                            $l = new Lesson();
                            $l->name = $lesson['name'];
                            $l->unitId = $u->id;
                            $l->description = $lesson['description'];
                            $l->goalId = $u->goalId;
                            $l->courseId = $teacherCourse->courseId;
                            if (array_key_exists('photo', $lesson)) {
                                $l->photo = $this->fileSave('lesson', $lesson['photo']);
                            }
                            if (array_key_exists('video', $lesson)) {
                                $l->video = $lesson['video'];
                            }
                            if (array_key_exists('objectiveId', $lesson)) {
                                $l->objectiveId = $lesson['objectiveId'];
                            }
                            $l->save();
                            if (array_key_exists('questions', $lesson)) {
                                foreach ($lesson['questions'] as $question) {
                                    $q = new Question();
                                    $q->uniqueId = md5(Carbon::now());
                                    if (array_key_exists('question', $question)) {
                                        $q->question = $question['question'];
                                    }
                                    if (array_key_exists('photo', $question)) {
                                        $q->questionPhoto = $this->fileSave('questions', $question['photo']);
                                    }
                                    $q->createdBy = auth()->user()->uniqueId;
                                    $q->goalId = $u->goalId;
                                    $q->level = $question['level'];
                                    $q->courseId = $teacherCourse->courseId;
                                    $q->isEssential = $question['isEssential'];
                                    $q->duration = $question['duration'];
                                    if (array_key_exists('objectiveId', $question)) {
                                        $q->objectiveId = $question['objectiveId'];
                                    }
                                    $q->save();

                                    $answer = new Option();
                                    $answer->questionId = $q->uniqueId;
                                    $answer->content = $question['answer'];
                                    $answer->isCorrect = true;
                                    $answer->save();
                                    foreach ($question['options'] as $option) {
                                        $op = new Option();
                                        $op->questionId = $q->uniqueId;
                                        $op->content = $option;
                                        $op->save();
                                    }

                                }

                            }
                        }
                    }
                }
            }
            return \response()->json([
                'error' => 0,
                'msg' => 'saved Successfully'
            ], 200);
        }


    }

    private function fileSave($path, $file)
    {
        $path = '/' . $path . '/' . Str::random(35) . '.png';
        $store = Storage::disk('public')->put($path, base64_decode($file));
        if ($store) {
            return $path;
        }
        return '';
    }
}
