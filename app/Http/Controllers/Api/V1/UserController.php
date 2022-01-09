<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class UserController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/users/profile",
     *     summary="User profile",
     *     operationId="getProfile",
     *     tags={"User"},
     *     security={{ "passport": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *     ),
     *     @OA\Response(
     *         response=203,
     *         description="Error",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Not authorized",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *     )
     * )
     */

    /***
     * Get authenticated user profile
     */
    public function profile(Request $request)
    {
        return response()->json(['status' => 200, 'data' => auth()->user() ], 200);
    }

}
