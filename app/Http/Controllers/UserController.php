<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditUserRequest;
use App\Http\Resources\EditUserResource;
use App\Services\UserService;

class UserController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @OA\Get(
     *     path="/api/user",
     *     tags={"User"},
     *     summary="Получить пользователя",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="User info")
     * )
     */

    public function index()
    {
        $user = $this->userService->getUser();
        return response()->json([
            'status' => 'success',
            'message' => 'Пользователь',
            'data' => new EditUserResource($user)
        ]);
    }
    /**
     * @OA\Patch(
     *     path="/api/user",
     *     tags={"User"},
     *     summary="Обновить пользователя",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="phone", type="string"),
     *             @OA\Property(property="role", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Updated user")
     * )
     */
    public function update(EditUserRequest $request)
    {
        $user = $this->userService->editUser($request->only(['name', 'email', 'phone']));

        return response()->json([
            'status' => 'updated',
            'message' => 'Пользователь обновлен',
            'data' => new EditUserResource($user)
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/user",
     *     tags={"User"},
     *     summary="Удалить пользователя",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=204, description="No content")
     * )
     */
    public function destroy()
    {
        $user = auth()->user();
        $this->userService->deleteUser($user);

        return response()->json([
            'status' => 'deleted',
            'message' => 'Пользователь удален',
            'data' => null
        ]);
    }
}
