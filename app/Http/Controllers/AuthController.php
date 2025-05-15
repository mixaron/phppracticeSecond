<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Services\UserService;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class AuthController extends Controller
{
    private UserService $userService;
    private AuthService $authService;

    public function __construct(UserService $userService, AuthService $authService)
    {
        $this->userService = $userService;
        $this->authService = $authService;
    }

    /**
     * @OA\Post(
     *     path="/api/register",
     *     tags={"Auth"},
     *     summary="Зарегестрировать пользователя",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","phone","role","password"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="phone", type="string"),
     *             @OA\Property(property="password", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(response=200, description="JWT token")
     * )
     */
    public function register(RegisterRequest $request)
    {
        $user = $this->userService->createUser($request->only([
            'name', 'email', 'phone', 'password'
        ]));

        $token = $this->authService->generateToken($user);

        return response()->json([
            'status' => 'success',
            'message' => 'Пользователь успешно зарегистрирован',
            'data' => ['token' => $token]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Auth"},
     *     summary="Аутентифицировать пользователя",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","password"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="password", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(response=200, description="JWT token"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function login(Request $request)
    {
        $token = $this->authService->attemptLogin(
            $request->only('name', 'password')
        );

        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
                'data' => null
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Пользователь успешно авторизирован',
            'data' => ['token' => $token]
        ]);
    }
}
