<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Advertisement;

class LoadingAPI extends Controller
{

    public function getLoadingImage()
    {
        $imageLoading = asset('uploads/image/loading.png');
        $time = Advertisement::get('time_show');
        $time_show = $time[0]['time_show'];
        try {
            return response()->json([
               "link-image" => $imageLoading,
               "time" => $time_show
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                "error" => "An error occurred while processing your request",
                "message" => $e->getMessage()
            ], 500); 
        }
    }
}
