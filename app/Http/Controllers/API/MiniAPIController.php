<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Minigame;
use Laravel\Sanctum\PersonalAccessToken;
use Carbon\Carbon;

class MiniAPIController extends Controller
{
    public function getMinigame(Request $request)
    {
        $minigames = Minigame::select('data')->get();
        $token = $request->bearerToken();

        // Kiểm tra token có hợp lệ không
        $accessToken = PersonalAccessToken::findToken($token);
        if (!$accessToken || $accessToken->expires_at < Carbon::now()) {
            return response()->json(['error' => 'Token không hợp lệ hoặc đã hết hạn.'], 401);
        }
        $user = $accessToken->tokenable;
        
        return response()->json([
            'color' => $minigames,
            "numberSpin" => $user->numberSpin
        ]);
    }
}
