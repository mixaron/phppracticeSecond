<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\UserRequestResource;
use App\Services\UserRequestService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class UserRequestController extends Controller
{

    private UserRequestService $userRequestService;

    public function __construct(UserRequestService $userRequestService)
    {

        $this->userRequestService = $userRequestService;
    }

    /**
     * @OA\Get(
     *     path="/api/request",
     *     tags={"Requests"},
     *     summary="Список всех заявок пользователя",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="List of user requests")
     * )
     */
    public function index()
    {
        $user = auth()->user();
        $userRequests = $this->userRequestService->findAllRequestsByUser($user);

        return response()->json([
            'status' => 'success',
            'message' => 'Список заявок пользователя',
            'data' => UserRequestResource::collection($userRequests)
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/request",
     *     tags={"Requests"},
     *     summary="Создасть заявку",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"title", "description"},
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="description", type="string")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Created")
     * )
     */
    public function store(UserRequest $request)
    {
        $this->userRequestService->addRequest([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'service_id' => $request->input('service_id'),
            'status' => 'new',
            'user_id' => auth()->id()
        ]);

        return response()->json([
            'status' => 'created',
            'message' => 'Заявка создана',
            'data' => null
        ]);
    }


    /**
     * @OA\Get(
     *     path="/api/request/{id}",
     *     tags={"Requests"},
     *     summary="Получить заявку пользователя по id заявки",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Request data")
     * )
     */
    public function show(string $id)
    {
        $user = auth()->user();

        try {
            $userRequest = $this->userRequestService->showRequestById($id, $user);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Заявка не найдена',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Заявка получена',
            'data' => new UserRequestResource($userRequest)
            ]);
    }



    /**
     * @OA\Patch(
     *     path="/api/request/{id}",
     *     tags={"Requests"},
     *     summary="Обновить заявку пользователя по id заявки",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="description", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Updated request")
     * )
     */
    public function update(UserRequest $request, string $id)
    {
        $user = auth()->user();

        try {
            $userRequest = $this->userRequestService
                ->updateRequest($request->only(['title', 'description', 'service_id']), $id, $user);
        } catch (BadRequestException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Заявка уже в процессе решения',
                'data' => null
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Заявка не найдена',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => 'updated',
            'message' => 'Заявка обновлена',
            'data' => new UserRequestResource($userRequest)
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/request/{id}",
     *     tags={"Requests"},
     *     summary="Удалить заявку пользователя по id заявки",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=204, description="Deleted")
     * )
     */
    public function destroy(string $id)
    {
        $user = auth()->user();

        try {
            $this->userRequestService->deleteById($id, $user);
        } catch (BadRequestException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Заявка уже в процессе решения',
                'data' => null
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Заявка не найдена',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => 'deleted',
            'message' => 'Заявка удалена',
            'data' => null
        ]);
    }
}

