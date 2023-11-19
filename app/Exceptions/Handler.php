<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

use Illuminate\Validation\ValidationException;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
    protected function unauthenticated($request, AuthenticationException $exception)
    {
//        if ($request->acceptsHtml()) {
//            return redirect()->route('login');
//        }
        return response()->json(['error' => 1, 'msg' => 'Please Login to access this route'], 401);
    }

//    public function render($request, Throwable $e)
//    {
//        if ($e instanceof \Illuminate\Auth\Access\AuthorizationException || $e instanceof UnauthorizedException) {
//            return response()->json([
//                'error' => 1,
//                'msg' => "Access Denied!!"
//            ],403);
//        }
//        if ($e instanceof AuthenticationException) {
//            return parent::render($request, $e);
//        }
//        if ($e instanceof ValidationException) {
//            return response()->json([
//                "error" => 1,
//                "msg" => $e->errors(),
//            ]);
//        }
//        return response()->json([
//            'error' => 1,
//            'msg' => $e->getMessage()
//        ],$e->getCode() != 0 ? $e->getCode() : 402);
//    }
}
