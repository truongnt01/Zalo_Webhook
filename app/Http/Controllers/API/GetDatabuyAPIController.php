<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\dataBuy;
use App\Models\User;
use App\Models\WebHook;
use Laravel\Sanctum\PersonalAccessToken;
use Carbon\Carbon;

class GetDatabuyAPIController extends Controller
{


    public function getDataBuy(Request $request, $privateKey = false)
    {

        $data = $request->all();

        ksort($data);

        $content = "";
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }
            $content .= $value;
        }

        // Get the private key
        // if (!$privateKey) {
        //     $privateKeyPath = storage_path('app/public.pem');
        //     $privateKey = file_get_contents($privateKeyPath);
        // }
        if (!$privateKey) {
            $publicKeyPath = storage_path('app/public.pem');
            $publicKey = file_get_contents($publicKeyPath);
        }
        $apiKey = 'Sry6DZ9XoNnnG0CsC6V9PNP01Y4vOCnhVaK48ZDt4FvzCdVDSm';
        // $privateKey = "";
        // Create the signature
        $signature = hash('sha256', $content . $apiKey);

        // Save the data and signature in the WebHook model
        $webHook = new WebHook();
        $webHook->data = json_encode($data);  // Ensure this is stored as an array
        $webHook->signature = $signature;
        $webHook->save();

        // Return the signature in the response
        return response()->json([
            'signature' => $signature,
            'public_key' => $publicKey

        ]);
    }

    public function checkSignature(Request $request)
    {
        $signature = $request->header('Signature');

        if (!$signature) {
            return response()->json(['error' => 'Missing Signature'], 400);
        }

        $webHook = WebHook::where('signature', $signature)->first();
        if ($webHook !== null) {
            $userId = $request->input('userId');

            $user = User::whereRaw("JSON_UNQUOTE(JSON_EXTRACT(contents, '$.user_id')) = ?", [$userId])->first();

            if ($user) {
                $newContent = [
                    'name' => '',
                    'avatar' => '',
                    'idByOA' => false,
                    'user_id' => '',
                    'followedOA' => false,
                    'isSensitive' => false,
                    'numberPhone' => ''
                ];
                $user->contents = json_encode($newContent);

                $user->save();

                return response()->json(['message' => 'User ID trùng khớp và đã cập nhật lại content']);
            } else {
                return response()->json(['message' => 'Không tìm thấy User ID khớp']);
            }
            return response()->json(['Signature']);
        }
    }

    public function generateSignature(Request $request, $privateKey = false)
    {
        $responseData = $request->all();
        
        ksort($responseData);
        $stringParams = json_encode($responseData);
        $content = "";
        foreach ($responseData as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }
            $content .= $value;
        }
    
        // Đọc khóa riêng tư từ file nếu chưa được cung cấp
        if (!$privateKey) {
            $privateKeyPath = storage_path('app/private.pem');
            $privateKey = file_get_contents($privateKeyPath);
        }
        $signature = hash('sha256', $content . $privateKey);
        openssl_sign($stringParams, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        return response()->json([
            'signature' => base64_encode($signature),
            'privateKey' => $privateKey
        ]) ;
        // return $signature;
    }
}
