<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RefreshToken;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{


public function register(Request $request)
{
    $validated = $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|string|email|unique:users',
        'password' => 'required|string|min:6|confirmed'
    ]);

    $user = User::create([
        'name'     => $request->name,
        'email'    => $request->email,
        'password' => bcrypt($request->password)
    ]);

    $accessToken = JWTAuth::fromUser($user);
    $rawRefreshToken = Str::random(64);

    RefreshToken::create([
        'user_id'    => $user->id,
        'token'      => hash('sha256', $rawRefreshToken),
        'expires_at' => now()->addDays(7),
    ]);

    return response()->json([
        'access_token'  => $accessToken,
        'refresh_token' => $rawRefreshToken,
        'token_type'    => 'bearer',
        'expires_in' => auth('api')->factory()->getTTL() * 60

    ]);
}

    public function login(Request $request)
    {
        
        $credentials = $request->only('email', 'password');

        if (! $accessToken = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $user = auth()->user();

        $rawRefreshToken = Str::random(64);

        RefreshToken::create([
            'user_id'    => $user->id,
            'token'      => hash('sha256', $rawRefreshToken),
            'expires_at' => now()->addDays(7),
        ]);

        return response()->json([
            'access_token'  => $accessToken,
            'refresh_token' => $rawRefreshToken,
            'token_type'    => 'bearer',
            'expires_in'    => auth()->factory()->getTTL() * 60
        ]);
    }

    public function me()
    {
        
        return response()->json(auth()->user());
    }

    public function logout(Request $request)
    {
        auth()->logout();

        $request->validate([
            'refresh_token' => 'required|string',
        ]);

        RefreshToken::where('token', hash('sha256', $request->refresh_token))->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function refresh(Request $request)
    {
        $request->validate([
            'refresh_token' => 'required|string',
        ]);

        $hashedToken = hash('sha256', $request->refresh_token);

        $tokenRecord = RefreshToken::where('token', $hashedToken)
            ->where('expires_at', '>', now())
            ->first();

        if (! $tokenRecord) {
            return response()->json(['error' => 'Invalid or expired refresh token'], 401);
        }

        $user = $tokenRecord->user;

        $accessToken = JWTAuth::fromUser($user);

        return response()->json([
            'access_token' => $accessToken,
            'token_type'   => 'bearer',
            'expires_in'   => auth()->factory()->getTTL() * 60
        ]);
    }
}
