<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ZaloUserProfile;
use Illuminate\Http\Request;

class ZaloAPIController extends Controller
{
    public function UpdateUserZalo(Request $request)
    {
        $key = env('APP_KEY_ZALO');
        $inputKey = $request->input('key');

        if (!$request->has('key')) {
            return response()->json([
                "error" => "key required",
            ], 404);
        }
        if ($inputKey == $key) {
            try {

                $data = $request->input('contents');

                if (!$data) {
                    return response()->json([
                        "error" => "Invalid data format"
                    ], 400);
                }

                $id = $data['id'] ?? null;
                $name = $data['name'] ?? null;
                $avatar = $data['avatar'] ?? null;
                $idByOA = $data['idByOA'] ?? null;
                $followedOA = $data['followedOA'] ?? false;
                $isSensitive = $data['isSensitive'] ?? false;
                $mobile = $data['mobile'] ?? null;

                if (!$id) {
                    return response()->json([
                        "error" => "ID is required"
                    ], 400);
                }

                $user = ZaloUserProfile::where('id', $id)->first();

                if (!$user) {
                    $user = new ZaloUserProfile();
                    $user->id = $id;
                } else {
                    return response()->json([
                        "error" => "ID is uniqued"
                    ], 400);
                }


                $user->name = $name;
                $user->avatar = $avatar;
                $user->idByOA = $idByOA;
                $user->followedOA = $followedOA;
                $user->isSensitive = $isSensitive;
                $user->mobile = $mobile;

                $user->save();

                return response()->json([
                    "success" => true,
                    "message" => "User profile updated successfully",
                    "data" => $user
                ]);
            } catch (\Exception $e) {
                // Xử lý lỗi bất ngờ
                return response()->json([
                    "error" => "An error occurred while processing your request",
                    "message" => $e->getMessage()
                ], 500); // Internal Server Error
            }
        } else {
            return response()->json([
                "error" => "key is not correct",
            ], 404);
        }
    }
}
