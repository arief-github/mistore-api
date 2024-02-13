<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    public function index(Request $request)
    {
        // set validasi
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
           return response()->json($validator->errors(), 422);
        }

        // get email and password from input
        $credentials = $request->only('email', 'password');

        // checking if email and password doesn't match
        if (!$token = auth()->guard('api_customer')->attempt($credentials)) {
           // response login failed
            return response()->json([
                'success' => false,
                'message' => 'Email or Password is Inccorrect'
            ], 401);
        }

        // response login success
        return response()->json([
            'success'=> true,
            'user' => auth()->guard('api_customer')->user(),
            'token' => $token
        ], 200);
    }

    // getUser
    public function getUser()
    {
        // response data user currently login
        return response()->json([
            'success' => true,
            'user' => auth()->guard('api_customer')->user()
        ], 200);
    }

    // refresh token
    public function refreshToken(Request $request)
    {
        $refreshToken = JWTAuth::refresh(JWTAuth::getToken());

        // set user with new token
        $user = JWTAuth::setToken($refreshToken)->toUser();

        // set header "Authorizatio" with type Bearer and new token
        $request->headers->set('Authorization', 'Bearer'.$refreshToken);

        // response data user with new token
        return response()->json([
            'success' => true,
            'user' => $user,
            'token' => $refreshToken
        ], 200);
    }

    public function logout()
    {
        // remove jwt token
        $removeToken = JWTAuth::invalidate(JWTAuth::getToken());

        // return response success logout
        return response()->json([
            'success' => true
        ], 200);
    }

}
