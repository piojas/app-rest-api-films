<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private static $identToken = 'CorpusDelictiPasswordApi';

    public function register(RegisterUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = bcrypt($request->password);

        $user = User::create($data);

        $token = $user->createToken(self::$identToken)->accessToken;
 
        return response()->json(['token' => $token], 200);
    }
 
    public function login(Request $request)
    {
        $data = [
            'email' => $request->email,
            'password' => $request->password
        ];
 
        if (auth()->attempt($data)) {
            $token = auth()->user()->createToken(self::$identToken)->accessToken;
            
            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            'message' => 'Succesfully Logged out'
        ], 200);
    }

    public function userInfo() 
    {
        $user = auth()->user();
        
        return response()->json(['user' => $user], 200);
    }
}
