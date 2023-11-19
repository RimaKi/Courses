<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class HelperController extends Controller
{
    static function downloadFile($fileName, $downloadName = null) {
        if ($fileName != null && $fileName != "" && Storage::disk("public")->exists($fileName)){
            $extension = explode('.',$fileName);
            $extension = $extension[count($extension) - 1];
            return response()->download(base_path("files") . '/' . $fileName, $downloadName ?? Carbon::now()->getTimestamp() . "." . $extension);
        }
        return response()->json([
            "error" => 1,
            "msg"=> "Access Denied!",
        ],403);
    }

    static function viewFile($fileName) {
        if ($fileName != null && $fileName != "" && Storage::disk("public")->exists($fileName))
            return response()->file(base_path("files") . '/' . $fileName);
        return response()->json([
            "error" => 1,
            "msg"=> "Access Denied!",
        ],403);
    }

    static function viewPhoto($photoName) {
        if ($photoName != null && $photoName != "" && Storage::disk("public")->exists( $photoName))
            return Storage::disk('public')->url($photoName);
        return "";
    }

    static function paginate($data, Request $request, $perPage=3) {
        if (!($data instanceof Collection)) {
            $data = collect($data);
        }
        $total = $data->count();
        $current_page = $request->get("page") ?? 1;
        return new Paginator(
            $data->forPage($current_page, $perPage)->values(),
            $total,
            $perPage,
            $current_page,
            ["path"=> $request->url()]
        );
    }

}
