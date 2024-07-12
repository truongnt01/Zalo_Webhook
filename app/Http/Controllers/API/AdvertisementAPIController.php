<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Advertisement;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Env;

class AdvertisementAPIController extends Controller
{
    public function AllAdvertisements()
    {
        $today = Carbon::today();
        $data = Advertisement::select('image', 'release_date')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('release_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->get();
      
        if ($data->isEmpty()) {
            return response()->json([
                "error" => "No Data Was Found ?!"
            ], 404);
        } else {
            return response()->json([
                "result" => [
                    "content" => $data,
                    "message" => "Successfully retrieved data",
                    "errors" => [],
                    "response_code" => "default_create_200"
                ],
                "id" => null,
                "jsonrpc" => "2.0"
            ]);
        }
    }

    public function Advertisement($id)
    {
        $today = Carbon::today();
        try {
            $data = Advertisement::where('id', $id)
                ->whereDate('start_date', '<=', $today)
                ->whereDate('release_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->get(['image', 'release_date']);

            // Kiểm tra xem có dữ liệu hay không
            if ($data->isEmpty()) {
                return response()->json([
                    "error" => "No advertisement found for the given ID"
                ], 404); // Not Found
            }

            // Trích xuất trường 'image' từ kết quả
            $images = $data->pluck('image');

            // Trả về JSON response
            return response()->json([
                "result" => [
                    "content" => [$data],
                    "message" => "Successfully update",
                    "errors" => [],
                    "response_code" => "default_create_200"
                ],
                "id" => null,
                "jsonrpc" => "2.0"
            ]);
        } catch (\Exception $e) {
            // Xử lý lỗi bất ngờ
            return response()->json([
                "error" => "An error occurred while processing your request",
                "message" => $e->getMessage()
            ], 500); // Internal Server Error
        }
    }
}
