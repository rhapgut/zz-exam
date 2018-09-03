<?php

declare (strict_types = 1);

namespace App\Eloquent\Repositories;

use App\Contracts\Repositories\UserPhoneNumberRepositoryInterface;
use App\Eloquent\Models\UserPhoneNumber;

class UserPhoneNumberRepository extends BaseRepository implements UserPhoneNumberRepositoryInterface
{
    public function __construct()
    {
        $this->setModel(new UserPhoneNumber());
    }

    /**
     * @param int $userId
     * @param string $number
     * @param string $exceptNumber
     * @return boolean
     */
    public function isExist(int $userId, string $number, string $exceptNumber = null)
    {
        $phoneNumber = UserPhoneNumber::where('user_id', $userId)
            ->where('number', $number);

        if (!is_null($exceptNumber)) {
            $phoneNumber->where('number', '!=', $exceptNumber);
        }

        return (boolean) $phoneNumber->count();
    }

     /**
     * @param int $userId
     * @param int $limit
     * @param int $page
     * @return boolean
     */
    public function getAllPhoneNumbers(int $userId, int $limit = self::DEFAULT_LIMIT, int $page = 1)
    {
        $query = UserPhoneNumber::where('user_id', $userId);
        return $query->paginate($limit);
    }
}
