<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class UserAPIController extends Controller
{
    // Hàm đăng ký người dùng mới
    public function register(Request $request)
    {
        // Kiểm tra tính hợp lệ của dữ liệu đầu vào
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8|confirmed',
            'phone' => [
                'required',
                'string',
                'regex:/^(0|\+84)(86|96|97|98|32|33|34|35|36|37|38|39|89|90|93|70|79|77|76|78|88|91|94|83|84|85|81|82|92|56|58|99|59)[0-9]{7}$/'
            ]
        ]);

        // Kiểm tra xem email hoặc số điện thoại đã tồn tại
        $existingUserByEmail = User::where('email', $request->email)->first();
        $existingUserByPhone = User::where('phone', $request->phone)->first();

        if ($existingUserByEmail) {
            return response()->json(['message' => 'Email đã tồn tại'], 400);
        }

        if ($existingUserByPhone) {
            return response()->json(['message' => 'Số điện thoại đã tồn tại'], 400);
        }

        // Tạo người dùng mới
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
        ]);

        // Trả về phản hồi JSON xác nhận tạo người dùng thành công
        return response()->json(['message' => 'Người dùng đã được tạo thành công'], 201);
    }



    // Hàm đăng nhập người dùng
    public function login(Request $request)
    {
        // Kiểm tra tính hợp lệ của dữ liệu đầu vào
        $request->validate([
            'phone' => ['required', 'string', 'regex:/^(0|\+84)[3-9][0-9]{8}$/'],
            'password' => 'required|string',
        ]);

        // Tìm người dùng theo số điện thoại
        $user = User::where('phone', $request->phone)->first();

        // Kiểm tra thông tin đăng nhập
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'phone' => ['Thông tin đăng nhập không chính xác.'],
            ]);
        }

        // Tạo token xác thực
        // $token = $user->createToken('auth_token')->plainTextToken;
        $token = $user->createToken('auth_token', ['*'], now()->addMinutes(60))->plainTextToken;
        // Trả về phản hồi JSON chứa token và thông tin người dùng
        return response()->json([
            'access_token' => $token,
            'user' => $user
        ]);
    }

    public function getProfile(Request $request)
    {
        // Lấy token từ header
        $token = $request->bearerToken();

        // Kiểm tra token có hợp lệ không
        $accessToken = PersonalAccessToken::findToken($token);
        if (!$accessToken || $accessToken->expires_at < Carbon::now()) {
            return response()->json(['error' => 'Token không hợp lệ hoặc đã hết hạn.'], 401);
        }

        // Lấy người dùng tương ứng với token
        $user = $accessToken->tokenable;
        $profileData = [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'point' => $user->point,
            'numberSpin' => $user->numberSpin,
            'contents' => [
                "user_id" => "",
                "name" => "",
                "avatar" => "",
                "idByOA" => null,
                "followedOA" => false,
                "isSensitive" => false,
            ],
        ];
        // Trả về thông tin người dùng
        return response()->json(['user' => $profileData]);
    }

    public function updateNumberSpin(Request $request)
    {
        $token = $request->bearerToken();

        // Kiểm tra token có hợp lệ không
        $accessToken = PersonalAccessToken::findToken($token);
        if (!$accessToken || $accessToken->expires_at < Carbon::now()) {
            return response()->json(['error' => 'Token không hợp lệ hoặc đã hết hạn.'], 401);
        }
        $user = $accessToken->tokenable;


        $user->numberSpin -= 1;

        $user->save();
        return response()->json([
            'message' => 'Thêm vòng quay thành công',
            'spin left' => $user->numberSpin
        ], 201);
    }

    public function updateProfile(Request $request)
    {
        // Kiểm tra tính hợp lệ của token và các trường đầu vào
        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255',
            'phone' => ['nullable', 'string', 'regex:/^(0|\+84)[3-9][0-9]{8}$/'],
        ]);

        // Lấy token từ body
        $token = $request->bearerToken();

        // Kiểm tra token có hợp lệ không
        $accessToken = PersonalAccessToken::findToken($token);
        if (!$accessToken || $accessToken->expires_at < Carbon::now()) {
            return response()->json(['error' => 'Token không hợp lệ hoặc đã hết hạn.'], 401);
        }
        $user = $accessToken->tokenable;

        // Kiểm tra tính duy nhất của email nếu có trong request
        if ($request->has('email')) {
            $email = $request->input('email');
            $existingUser = User::where('email', $email)->where('id', '!=', $user->id)->first();
            if ($existingUser) {
                return response()->json(['error' => 'Email đã được sử dụng bởi người dùng khác.'], 422);
            }
            $user->email = $email;
        }

        // Kiểm tra tính duy nhất của phone nếu có trong request
        if ($request->has('phone')) {
            $phone = $request->input('phone');
            $existingUser = User::where('phone', $phone)->where('id', '!=', $user->id)->first();
            if ($existingUser) {
                return response()->json(['error' => 'Số điện thoại đã được sử dụng bởi người dùng khác.'], 422);
            }
            $user->phone = $phone;
        }

        // Cập nhật các trường khác nếu có trong request
        if ($request->has('name')) {
            $user->name = $request->input('name');
        }
        if ($request->has('numberSpin')) {
            $user->numberSpin = $request->input('numberSpin');
        }
        if ($request->has('point')) {
            $user->point += $request->input('point');
        }
        // Lưu thông tin người dùng
        $user->save();

        // Trả về thông tin người dùng đã cập nhật
        return response()->json(['success' => 'Cập nhật thành công']);
    }
}
