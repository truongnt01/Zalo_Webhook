<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class GetBanner extends Controller
{

    public function getBanner(Request $request)
    {
        $banners = [
            asset('uploads/image/mgpsh_fullsize_anim2.png'),
            asset('uploads/image/imgpsh_fullsize_anim.png'),
        ];
        try {
            return response()->json([
                "link-image" => $banners
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                "error" => "An error occurred while processing your request",
                "message" => $e->getMessage()
            ], 500);
        }
    }
}
