<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends BaseController
{
    /**
     * @OA\Post(
     *     path="/register",
     *     summary="Register",
     *     operationId="postRegister",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *        required=true,
     *        @OA\JsonContent(
     *           required={"email", "fullname", "phone", "password", "confirm_password"},
     *           @OA\Property(property="fullname", type="string", format="string", example="John Doe"),
     *           @OA\Property(property="email", type="string", format="string", example="johndoe@example.com"),
     *           @OA\Property(property="phone", type="string", format="string", example="2348052142102"),
     *           @OA\Property(property="password", type="string", format="password", example="******"),
     *           @OA\Property(property="confirm_password", type="string", format="password", example="******")
     *        ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessed entity",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *     )
     * )
     */

    /**
     * Register new user
     */
    public function register(Request $request)
    {
        // Vdalidate request
        $validator = Validator::make($request->all(), [
            'fullname'          => 'required|string|max:255',
            'email'             => 'required|string|email|max:255|unique:users',
            'phone'             => 'required|string|max:14|min:10|unique:users',
            'password'          => 'required|string|min:6',
            'confirm_password'  => 'required|string|min:6|same:password'
        ]);

        if ($validator->fails()) {
            // Return validation error if it fails
            return response(['status' => 422, 'errors' => $validator->errors()], 422);
        }

        // Request form data
        $fullname = ucwords(strtolower($request->fullname));
        $email = $request->email;
        $phone = $request->phone;
        $password = $request->password;

        $user = User::create([
            'name'      => $fullname,
            'email'     => $email,
            'phone'     => $phone,
            'password'  => Hash::make($password)
        ]);

        return response()->json(['status' => 201, 'data' => $user], 201);
    }

    /**
     * @OA\Post(
     *     path="/login",
     *     summary="Login",
     *     operationId="postLogin",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *        required=true,
     *        @OA\JsonContent(
     *           required={"email", "password"},
     *           @OA\Property(property="email", type="string", format="string", example="johndoe@example.com"),
     *           @OA\Property(property="password", type="string", format="password", example="******")
     *        ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *     ),
     *    @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *    ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessed entity",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *     )
     * )
     */

    /**
     * Login user
     */
    public function login(Request $request)
    {
        // Validate data
        $validation = Validator::make($request->all(), [
            'email'     => 'required|string|email|max:255',
            'password'  => 'required|string'
        ]);

        if ($validation->fails()) {
            // Return validation error
            return response()->json(['status' => 422, 'errors' => $validation->errors()], 422);
        }

        // Request data
        $email = $request->email;
        $password = $request->password;

        if (Auth::attempt(['email' => $email, 'password' => $password], true)) {
            $token = Auth::user()->createToken(env('APP_NAME') . ' Grant Client')->accessToken;
            return $this->respondWithToken($token);
        }

        return response()->json(['status' => 401, 'message' => 'Credentials supplied does not match any record.'], 401);
    }

    /**
     * @OA\Post(
     *     path="/users/logout",
     *     summary="Logout",
     *     operationId="postLogout",
     *     tags={"User"},
     *     security={{"passport": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *     )
     * )
     */
    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $token = $request->user()->token();
        $token->revoke();
        return response()->json(['status' => 200, 'message' => 'Logout successful'], 200);
    }

    private function respondWithToken($token)
    {
        return response()->json([
            'status'     => 200,
            'data'      => [
                'token'      => $token,
                'token_type' => 'bearer',
                'user'       => Auth::user(),
            ]
        ], 200);
    }
}
