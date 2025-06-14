<?php

namespace App\Http\Controllers;

use App\Models\Responder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Mockery\Expectation;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWTAuthController extends Controller
{

    /**
     * Register a responder into the database.
     *
     * @var Request: The caller HttpRequest data
     */
    public function register(Request $request)
    {
        // Validation Rules
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:responders',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $responder = Responder::create([
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);

        $token = JWTAuth::fromUser($responder);

        return response()->json(compact('responder', 'token'), 201);
    }

    /**
     * Login a responder and return jwt token.
     *
     * @var Request: The caller HttpRequest data
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            // Invalid
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Unauthorized user'], 401);
            }

            // Get the authenticated user.
            $user = auth('api')->user();

            $token = JWTAuth::fromUser($user);

            return response()->json(compact('token'));
        } catch (Expectation) {
            return response()->json(['error' => 'Could not login'], 500);
        }
    }

    /**
     * Logout a responder.
     */
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json([], 204);
    }
}
