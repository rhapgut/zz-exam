<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Eloquent\Models\Role;
use App\Http\Controllers\Api\ApiController;
use App\UseCase\UserUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends ApiController
{
    /** @var UserUseCase */
    private $userUseCase;

    /** @var UserRepositoryInterface */
    private $UserRepository;

    const ERR_MSG_USER_NOT_FOUND = 'The specified user not found.';
    const ERR_MSG_USER_FORBIDDEN = 'Your access to this user is forbidden.';

    public function __construct(
        UserUseCase $userUseCase,
        UserRepositoryInterface $userRepository
    ) {
        $this->userUseCase = $userUseCase;
        $this->userRepository = $userRepository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getMyself(Request $request): JsonResponse
    {
        $request->user->makeVisible([
            'full_name',
        ]);
        $request->user->client->makeVisible([
            'name',
            'address',
        ]);
        return response()->json($request->user);
    }

    /**
     * @param Request $request
     * @param int $userId
     * @return JsonResponse
     */
    public function getUser(Request $request, int $userId): JsonResponse
    {
        if ($request->user->role->name != Role::ADMIN_ROLE_NAME) { // not admin
            return response()->json(self::formatErrorResponse(403, [self::ERR_MSG_USER_FORBIDDEN]), 403);
        }
        $user = $this->userRepository->getById($userId);
        if (!$user || $user->is_deleted) {
            return response()->json(self::formatErrorResponse(422, [self::ERR_MSG_USER_NOT_FOUND]), 422);
        }
        $user->makeVisible([
            'full_name',
        ]);
        return response()->json($user);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        if (!($request->user->role->name == Role::ADMIN_ROLE_NAME ||
            ($request->user->role->name == Role::NON_ADMIN_ROLE_NAME && $request->user->is_authorized))) { //not authorize
            return response()->json(self::formatErrorResponse(403, [self::ERR_MSG_USER_FORBIDDEN]), 403);
        }
        $data = $request->all();
        $validator = Validator::make($data, [
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'full_name' => 'required|string',
            'is_authorized' => 'sometimes|boolean',
            'role_id' => 'required|exists:roles,id',
        ]);
        if ($validator->fails()) {
            return response()->json(self::formatErrorResponse(422, $validator->errors()->all()), 422);
        }
        $data['client_id'] = $request->user->client_id;
        $user = $this->userUseCase->createAndReturnUser($data);
        return response()->json(self::formatResponse(self::RESPONSE_STATUS_OK, 200));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateMyInfo(Request $request): JsonResponse
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'full_name' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(self::formatErrorResponse(422, $validator->errors()->all()), 422);
        }
        if ($request->has('email') || $request->has('email')) {
            $errMsgEmailNotAllowed = 'You can not change your email address.';
            return response()->json(self::formatErrorResponse(422, [$errMsgEmailNotAllowed]), 422);
        }
        $dataToUpdate = $request->only([
            'full_name',
        ]);
        $this->userRepository->updateUser($request->user, $dataToUpdate);
        return response()->json(self::formatResponse(self::RESPONSE_STATUS_OK, 200));
    }

    /**
     * @param Request $request
     * @param int $userId
     * @return JsonResponse
     */
    public function updateUserInfo(Request $request, int $userId): JsonResponse
    {
        if ($request->user->role->name != Role::ADMIN_ROLE_NAME) { // not admin
            return response()->json(self::formatErrorResponse(403, [self::ERR_MSG_USER_FORBIDDEN]), 403);
        }
        $user = $this->userRepository->getById($userId);
        if (!$user) {
            return response()->json(self::formatErrorResponse(422, [self::ERR_MSG_USER_NOT_FOUND]), 422);
        }
        if ($request->user->client_id != $user->client_id) {
            return response()->json(self::formatErrorResponse(403, [self::ERR_MSG_USER_FORBIDDEN]), 403);
        }
        $data = $request->all();
        $validator = Validator::make($data, [
            'full_name' => 'required|string',
            'is_authorized' => 'sometimes|boolean',
            'role_id' => 'required|exists:roles,id',
        ]);
        if ($validator->fails()) {
            return response()->json(self::formatErrorResponse(422, $validator->errors()->all()), 422);
        }
        $dataToUpdate = $request->only([
            'full_name',
            'is_authorized',
            'role_id',
        ]);
        $this->userRepository->updateUser($user, $dataToUpdate);
        return response()->json(self::formatResponse(self::RESPONSE_STATUS_OK, 200));
    }

    /**
     * @param Request $request
     * @param int $userId
     * @return JsonResponse
     */
    public function delete(Request $request, int $userId): JsonResponse
    {
        if ($request->user->role->name != Role::ADMIN_ROLE_NAME) { // not admin
            return response()->json(self::formatErrorResponse(403, [self::ERR_MSG_USER_FORBIDDEN]), 403);
        }
        $user = $this->userRepository->getById($userId);
        if (!$user) {
            return response()->json(self::formatErrorResponse(422, [self::ERR_MSG_USER_NOT_FOUND]), 422);
        }
        if ($request->user->id == $user->id) {
            $errMsgDeleteSelf = 'You cannot delete yourself.';
            return response()->json(self::formatErrorResponse(422, [$errMsgDeleteSelf]), 422);
        }
        if ($request->user->client_id != $user->client_id) {
            return response()->json(self::formatErrorResponse(403, [self::ERR_MSG_USER_FORBIDDEN]), 403);
        }
        $this->userRepository->deleteUser($user);
        return response()->json(self::formatResponse(self::RESPONSE_STATUS_OK, 200));
    }
}
