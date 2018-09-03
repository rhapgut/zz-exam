<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Repositories\UserPhoneNumberRepositoryInterface;
use App\Eloquent\Models\Role;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserPhoneNumberController extends ApiController
{
    const ERR_MSG_USER_FORBIDDEN = 'Your access to this phone number is forbidden.';
    const ERR_MSG_PHONE_NUMBER_NOT_FOUND = 'The specified phone number is not found.';

    /** @var UserPhoneNumberRepositoryInterface */
    private $phoneNumberRepository;

    public function __construct(
        UserPhoneNumberRepositoryInterface $phoneNumberRepository
    ) {
        $this->phoneNumberRepository = $phoneNumberRepository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getAll(Request $request): JsonResponse
    {
        $phoneNumbers = $this->phoneNumberRepository->getAllPhoneNumbers($request->user->id);
        return response()->json($phoneNumbers);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'number' => ['required', 'string', 'regex:/^(09|\+639)\d{9}$/'],
        ]);
        if ($validator->fails()) {
            return response()->json(self::formatErrorResponse(422, $validator->errors()->all()), 422);
        }
        $isExist = $this->phoneNumberRepository->isExist($request->user->id, $data['number']);
        if ($isExist) {
            $errMsgPhoneNumberExist = 'The phone number is already exist.';
            return response()->json(self::formatErrorResponse(422, [$errMsgPhoneNumberExist]), 422);
        }
        $data['user_id'] = $request->user->id;
        $user = $this->phoneNumberRepository->create($data);
        return response()->json(self::formatResponse(self::RESPONSE_STATUS_OK, 200));
    }

    /**
     * @param Request $request
     * @param int $phoneNumberId
     * @return JsonResponse
     */
    public function update(Request $request, int $phoneNumberId): JsonResponse
    {
        $phoneNumber = $this->phoneNumberRepository->getById($phoneNumberId);
        if (!$phoneNumber) {
            return response()->json(self::formatErrorResponse(422, [self::ERR_MSG_PHONE_NUMBER_NOT_FOUND]), 422);
        }
        $data = $request->all();
        $validator = Validator::make($data, [
            'number' => ['required', 'string', 'regex:/^(09|\+639)\d{9}$/'],
        ]);
        if ($validator->fails()) {
            return response()->json(self::formatErrorResponse(422, $validator->errors()->all()), 422);
        }
        $isExist = $this->phoneNumberRepository->isExist($request->user->id, $data['number'], $phoneNumber->number);
        if ($isExist) {
            $errMsgPhoneNumberExist = 'The phone number is already exist';
            return response()->json(self::formatErrorResponse(422, [$errMsgPhoneNumberExist]), 422);
        }
        $user = $this->phoneNumberRepository->update($phoneNumber, $data);
        return response()->json(self::formatResponse(self::RESPONSE_STATUS_OK, 200));
    }

    /**
     * @param Request $request
     * @param int $phoneNumberId
     * @return JsonResponse
     */
    public function delete(Request $request, int $phoneNumberId): JsonResponse
    {
        $phoneNumber = $this->phoneNumberRepository->getById($phoneNumberId);
        if (!$phoneNumber) {
            return response()->json(self::formatErrorResponse(422, [self::ERR_MSG_PHONE_NUMBER_NOT_FOUND]), 422);
        }
        if ($request->user->role != Role::ADMIN_ROLE_NAME) {
            if ($phoneNumber->user_id != $request->user->id) {
                return response()->json(self::formatErrorResponse(403, [self::ERR_MSG_USER_FORBIDDEN]), 403);
            }
        }

        $this->phoneNumberRepository->delete($phoneNumber);
        return response()->json(self::formatResponse(self::RESPONSE_STATUS_OK, 200));
    }
}
