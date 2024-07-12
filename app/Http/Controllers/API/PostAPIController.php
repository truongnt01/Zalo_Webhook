<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use Carbon\Carbon;

class PostAPIController extends Controller
{
    public function BlogALL(){
        $today = Carbon::today();
        try {
            $data = Post::whereDate('start_date', '<=', $today)
            ->whereDate('release_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->get();
        if ($data->isEmpty()) {
            return response()->json([
                "error" => "No Data Was Found ?!"
            ], 500);
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
        } catch (\Throwable $th) {
            
        }
    }
    public function Blog($id)
    {
        $today = Carbon::today();
        
        try {
            // Lấy dữ liệu từ bảng Post theo điều kiện id
            $data = Post::where('id', $id)
                ->whereDate('start_date', '<=', $today)
                ->whereDate('release_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->get();
    
            // Kiểm tra xem có dữ liệu hay không
            if ($data->isEmpty()) {
                return response()->json([
                    "error" => "No post found for the given ID"
                ], 404); // Not Found
            }
    
            // Trả về JSON response
            return response()->json([
                "result" => $data
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
