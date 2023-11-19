<?php

namespace App\Http\Middleware;

use App\Http\Controllers\HelperController;
use Closure;
use Illuminate\Http\Request;

class AppendPagination
{
    /*
     * APPENDPAGINATION MIDDLEWARE
     *
     * CREATED BY ENG. HAYYAN JARBOUE
     *
     * ALL RIGHTS RESERVED - XCORE 2021
     */

    public function handle(Request $request, Closure $next, $perPages=10)
    {
        $result = $next($request);
        if ($result->exception == null) {
            $result = response()->json([
                "error" => 0,
                "result" => HelperController::paginate($result->getOriginalContent(), $request, $perPages)
            ]);
        }
        return $result;
    }
}
