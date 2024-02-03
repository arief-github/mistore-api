<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function index(Request $request)
    {
        // set validasi
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // response error validasi
        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // get "email" and "password" from input
        $credentials = $request->only('email', 'password');

        // check email and password if not match
        if(!$token = auth()->guard('api_admin')->attempt($credentials)) {
            // give response login failed
            return response()->json([
                'success' => false,
                'message' => 'Email or Password is incorrect'
            ], 401);
        }

        // give success response and generate Token
        return response()->json([
            'success' => true,
            'user' => auth()->guard('api_admin')->user(),
            'token' => $token
        ], 200);
    }

    // getUser
    public function getUser()
    {
        //response data "user" that currently exists
        return response()->json([
            'success' => true,
            'user' => auth()->guard('api_admin')->user()
        ], 200);
    }

    // refreshToken
    public function refreshToken(Request $request) {
        // refresh token
        $refreshToken = JWTAuth::refresh(JWTAuth::getToken());

        // set user with new token
        $user = JWTAuth::setToken($refreshToken)->toUser();

        // also set header Authorization with type Bearer
        $request->headers->set('Authorization', 'Bearer'. $refreshToken);

        //response data "user" with new token
        return response()->json([
            'success' => true,
            'user' => $user,
            'token' => $refreshToken
        ], 200);
    }

    // logout
    public function logout()
    {
        // remove jwt token
        $removeToken = JWTAuth::invalidate(JWTAuth::getToken());

        // give success response for logout
        return response()->json([
            'success' => true,
        ], 200);
    }
}
