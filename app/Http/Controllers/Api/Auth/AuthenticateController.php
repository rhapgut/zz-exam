<?php

declare (strict_types = 1);

namespace App\Http\Controllers\Api\Auth;

use App\Contracts\Repositories\ClientRepositoryInterface;
use App\Eloquent\Models\User;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\JWTAuth;

class AuthenticateController extends ApiController
{
    use AuthenticatesUsers;

    protected $maxAttempts = 5;

    /** @var JWTAuth */
    private $auth;

    /** @var ClientRepositoryInterface */
    private $clientRepository;

    /** @var string */
    private $userIdentifier = 'email';

    public function __construct(
        JWTAuth $auth,
        ClientRepositoryInterface $clientRepository
    ) {
        $this->auth = $auth;
        $this->clientRepository = $clientRepository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');
        //$credentials['is_deleted'] = 0;

        if ($this->hasTooManyLoginAttempts($request)) {
            return response()->json(
                $this->formatResponse(
                    self::RESPONSE_STATUS_FAIL,
                    401,
                    [
                        self::RESPONSE_MESSAGE_KEY_ERRORS => ['Too many login attempts. Please try after a while.'],
                    ]
                ),
                401
            );
        }
        try {
            if (!$token = $this->auth->attempt($credentials)) {
                $this->incrementLoginAttempts($request);
                return response()->json(
                    $this->formatResponse(
                        self::RESPONSE_STATUS_FAIL,
                        401,
                        [
                            self::RESPONSE_MESSAGE_KEY_ERRORS => ['invalid_credentials'],
                        ]
                    ),
                    401
                );
            }
        } catch (JWTException $e) {
            return response()->json(
                $this->formatResponse(
                    self::RESPONSE_STATUS_FAIL,
                    500,
                    [
                        self::RESPONSE_MESSAGE_KEY_ERRORS => ['could_not_create_token'],
                    ]
                ),
                500
            );
        }

        /** @var User $user */
        $user = $this->auth->toUser($token);
        $user->makeVisible([
            'full_name',
        ]);
        $user->client->makeVisible([
            'name',
            'address',
        ]);

        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        try {
            $this->auth->invalidate($this->auth->getToken());
        } catch (TokenExpiredException $e) {
            return response()->json(
                $this->formatResponse(
                    self::RESPONSE_STATUS_FAIL,
                    $e->getStatusCode(),
                    [
                        self::RESPONSE_MESSAGE_KEY_ERRORS => ['token_expired'],
                    ]
                ),
                $e->getStatusCode()
            );
        } catch (TokenInvalidException $e) {
            return response()->json(
                $this->formatResponse(
                    self::RESPONSE_STATUS_FAIL,
                    $e->getStatusCode(),
                    [
                        self::RESPONSE_MESSAGE_KEY_ERRORS => ['token_invalid'],
                    ]
                ),
                $e->getStatusCode()
            );
        } catch (JWTException $e) {
            return response()->json(
                $this->formatResponse(
                    self::RESPONSE_STATUS_FAIL,
                    $e->getStatusCode(),
                    [
                        self::RESPONSE_MESSAGE_KEY_ERRORS => ['token_absent'],
                    ]
                ),
                $e->getStatusCode()
            );
        }
        return response()->json($this->formatResponse('ok', 200));
    }

    /**
     * @inheritdoc
     */
    public function username()
    {
        return $this->userIdentifier;
    }
}
