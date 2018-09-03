<?php

namespace App\Http\Controllers\Api;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

class ApiController
{
    const RESPONSE_STATUS_OK = 'ok';
    const RESPONSE_STATUS_FAIL = 'fail';

    const RESPONSE_MESSAGE_KEY_ERRORS = 'errors';

    const QUERY_TRUE_LIST_LOWER = ['1', 'true', 'yes'];

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param string $status
     * @param int $statusCode
     * @param array $message
     * @return array
     */
    public static function formatResponse(string $status, int $statusCode, array $message = []): array
    {
        return [
            'status' => $status,
            'code' => $statusCode,
            'message' => $message,
        ];
    }

    /**
     * @param int $statusCode
     * @param array $messages
     * @return array
     */
    public static function formatErrorResponse(int $statusCode, array $messages = []): array
    {
        return self::formatResponse(
            self::RESPONSE_STATUS_FAIL,
            $statusCode,
            [self::RESPONSE_MESSAGE_KEY_ERRORS => $messages]
        );
    }
}
