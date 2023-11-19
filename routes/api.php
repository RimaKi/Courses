<?php

use App\Services\QuestionService;
use App\Services\UnitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\API\UserController;
use \App\Http\Controllers\API\UnitController;
use \App\Http\Controllers\API\QuestionController;
use \App\Http\Controllers\API\OptionController;
use \App\Http\Controllers\API\LessonController;
use \App\Http\Controllers\API\GoalController;
use \App\Http\Controllers\API\CourseController;
use \App\Http\Controllers\API\AnswerController;
use \App\Http\Controllers\API\ObjectiveController;
use \App\Http\Controllers\API\ExamController;
use \App\Http\Controllers\API\CompetitionController;
use \App\Http\Controllers\API\StudentExamsController;
use \App\Http\Controllers\API\DepartmentController;
use \App\Http\Controllers\API\CollegeController;
use \App\Http\Controllers\API\PermissionController;
use \App\Http\Controllers\API\TeacherCourseController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::post('/signup', [UserController::class, 'singUp']);
Route::post('/change-user-type', [PermissionController::class, 'changeUserType']);
Route::post('/login', [UserController::class, 'logIn']);
Route::get('/teacher-course/view', [TeacherCourseController::class, 'view']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile/{uniqueId?}', [UserController::class, 'profile']);
    Route::get('/view-users',[UserController::class,'view']); //TODO add filter with role
    Route::post('/delete', [UserController::class, 'delete']);
    Route::post('/edit', [UserController::class, 'edit']);
    Route::post('/change-password', [UserController::class, 'change_password']);
    Route::post('/logout', [UserController::class, 'logOut']);

    Route::prefix('/unit')->group(function () {
        Route::post('/add', [UnitController::class, 'add']);
        Route::post('/edit', [UnitController::class, 'edit']);
        Route::get('/view/{id?}', [UnitController::class, 'view']);
        Route::post('/delete', [UnitController::class, 'delete']);
    });

    Route::prefix('/department')->group(function () {
        Route::post('/add', [DepartmentController::class, 'add']);
        Route::post('/edit', [DepartmentController::class, 'edit']);
        Route::get('/view/{id?}', [DepartmentController::class, 'view']);
        Route::post('/delete', [DepartmentController::class, 'delete']);
    });
    Route::prefix('/college')->group(function () {
        Route::post('/add', [CollegeController::class, 'add']);
        Route::post('/edit', [CollegeController::class, 'edit']);
        Route::get('/view/{id?}', [CollegeController::class, 'view']);
        Route::post('/delete', [CollegeController::class, 'delete']);
    });

    Route::prefix('/question')->group(function () {
        Route::post('/add', [QuestionController::class, 'add']);
        Route::post('/edit', [QuestionController::class, 'edit']);
        Route::get('/view/{uniqueId?}', [QuestionController::class, 'view']);
        Route::get('/delete', [QuestionController::class, 'delete']);
    });

    Route::prefix('/option')->group(function () {
        Route::post('/add', [OptionController::class, 'add']);
        Route::post('/edit', [OptionController::class, 'edit']);
        Route::get('/view/{id?}', [OptionController::class, 'view']);
        Route::post('/delete', [OptionController::class, 'delete']);
    });

    Route::prefix('/lesson')->group(function () {
        Route::post('/add', [LessonController::class, 'add']);
        Route::post('/edit', [LessonController::class, 'edit']);
        Route::get('/view/{uniqueId?}', [LessonController::class, 'view']);
        Route::post('/delete', [LessonController::class, 'delete']);
    });

    Route::prefix('/goals')->group(function () {
        Route::post('/edit', [GoalController::class, 'edit']);
        Route::get('/view', [GoalController::class, 'view']);
        Route::post('/delete', [GoalController::class, 'delete']);
    });

    Route::prefix('/courses')->group(function () {
        Route::post('/add', [CourseController::class, 'add']);
        Route::post('/edit', [CourseController::class, 'edit']);
        Route::get('/view', [CourseController::class, 'view']);
        Route::post('/delete', [CourseController::class, 'delete']);
    });

    Route::prefix('/answer')->group(function () {
        Route::get('/view', [AnswerController::class, 'view']);//student
        Route::get('/views', [AnswerController::class, 'views']);
    });

    Route::prefix('/objective')->group(function () {
        Route::post('/add', [ObjectiveController::class, 'add']);
        Route::post('/edit', [ObjectiveController::class, 'edit']);
        Route::get('/view', [ObjectiveController::class, 'view']);
        Route::post('/delete', [ObjectiveController::class, 'delete']);

    });

    Route::prefix('/exam')->group(function () {
        Route::post('/add', [ExamController::class, 'add']);
        Route::post('/edit', [ExamController::class, 'edit']);
        Route::get('/view/{id?}', [ExamController::class, 'view']);
        Route::get('/get-quiz', [ExamController::class, 'getQuiz']);
        Route::get('/get-final-exam-id', [ExamController::class, 'getExamId']);
        Route::get('/get-questions', [ExamController::class, 'getQuestion']);
        Route::get('/finish', [ExamController::class, 'saveMark']);
        Route::post('/delete', [ExamController::class, 'delete']);

    });

    Route::prefix('/competitions')->group(function () {
        Route::get('/edit', [CompetitionController::class, 'edit']);
        Route::get('/view', [CompetitionController::class, 'view']);
        Route::get('/get-key', [CompetitionController::class, 'getKey']);
        Route::get('/get-questions', [CompetitionController::class, 'viewCompetitionQuestions']);
        Route::post('/delete', [CompetitionController::class, 'delete']);

    });

    Route::prefix('/teacher-course')->group(function () {
        Route::post('/add', [TeacherCourseController::class, 'add']);
        Route::post('/edit', [TeacherCourseController::class, 'edit']);
        Route::post('/delete', [TeacherCourseController::class, 'delete']);
        Route::post('/add-unit', [TeacherCourseController::class, 'addCourse']);
    });

    Route::prefix('/student-exam')->group(function () {
        Route::get('/view', [StudentExamsController::class, 'view']);
        Route::post('/delete', [StudentExamsController::class, 'delete']);

    });
});

