<?php

declare (strict_types = 1);

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\ApiController;
use App\UseCase\UserUseCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\JWTAuth;

class RegisterController extends ApiController
{
    const CLIENT_REGISTER_VALIDATION = [
        'company_name' => 'required|string|unique:clients,name',
        'company_address' => 'string',
        'email' => 'required|string|email|max:255|unique:users,email',
        'password' => 'required|string|min:6',
        'full_name' => 'required|string',
    ];

    const ERR_MSG_ROLE_NOT_FOUND = 'No default user role';
    const ERR_MSG_INVALID_CREDENTIALS = 'Invalid credentials';
    const ERR_MSG_COULDNT_CREATE_TOKEN = 'Could not create token';

    /**
     * The authentication guard factory instance.
     *
     * @var JWTAuth
     */
    protected $auth;

    /**
     * @var UserUseCase
     */
    private $userUseCase;

    public function __construct(
        JWTAuth $auth,
        UserUseCase $userUseCase
    ) {
        $this->auth = $auth;
        $this->userUseCase = $userUseCase;
    }

    public function post(Request $request)
    {
        $validator = Validator::make($request->all(), self::CLIENT_REGISTER_VALIDATION);
        if ($validator->fails()) {
            return response()->json(
                $this->formatResponse(
                    self::RESPONSE_STATUS_FAIL,
                    422,
                    [self::RESPONSE_MESSAGE_KEY_ERRORS => $validator->errors()->all()]
                ),
                422
            );
        }

        $data = $request->all();

        $user = $this->userUseCase->createAndReturnClientUser($data);

        try {
            if (!$token = $this->auth->fromUser($user)) {
                return response()->json(
                    $this->formatResponse(
                        self::RESPONSE_STATUS_FAIL,
                        401,
                        [self::RESPONSE_MESSAGE_KEY_ERRORS => [self::ERR_MSG_INVALID_CREDENTIALS]]
                    ),
                    401
                );
            }
        } catch (JWTException $e) {
            return response()->json(
                $this->formatResponse(
                    self::RESPONSE_STATUS_FAIL,
                    500,
                    [self::RESPONSE_MESSAGE_KEY_ERRORS => [self::ERR_MSG_COULDNT_CREATE_TOKEN]]
                ),
                500
            );
        }

        return response()->json(
            $this->formatResponse(
                self::RESPONSE_STATUS_OK,
                200,
                [
                    'oAuth' => ['access_token' => $token],
                    'user' => $user,
                ]
            ),
            200
        );
    }
}
