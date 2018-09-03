<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Repositories\ClientRepositoryInterface;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClientController extends ApiController
{
    /** @var ClientRepositoryInterface */
    private $clientRepository;

    public function __construct(
        ClientRepositoryInterface $clientRepository
    ) {
        $this->clientRepository = $clientRepository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function get(Request $request): JsonResponse
    {
        $request->user->client->makeVisible([
            'name',
            'address',
        ]);
        return response()->json([
            'client' => $request->user->client,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'address' => 'sometimes|string',
        ]);
        if ($validator->fails()) {
            return response()->json(self::formatErrorResponse(422, $validator->errors()->all()), 422);
        }
        $dataToUpdate = $request->only([
            'name',
            'address',
        ]);
        $this->clientRepository->update($request->user->client, $dataToUpdate);
        return response()->json(self::formatResponse(self::RESPONSE_STATUS_OK, 200));
    }
}
