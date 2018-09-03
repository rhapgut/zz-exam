<?php

declare (strict_types = 1);

namespace App\Eloquent\Repositories;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Eloquent\Models\User;
use App\Eloquent\Models\UserPhoneNumber;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    const TRUE_VALUES = [true, 'true', 1, '1', 'yes'];

    public function __construct()
    {
        $this->setModel(new User());
    }

    /**
     * @param int $clientId
     * @return User
     */
    public function getByClientId(int $clientId)
    {
        return User::where('client_id', $clientId)
            ->where('is_deleted', 0)->get();
    }

    /**
     * @param array $data
     * @return User
     */
    public function createAndReturnUser(array $data): User
    {
        /** @var User $user */
        $user = $this->createAndReturn(
            [
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'role_id' => $data['role_id'],
                'full_name' => $data['full_name'],
                'is_authorized' => $data['is_authorized'] ?? 0,
                'client_id' => $data['client_id']
            ]
        );
        return $user;
    }

    /**
     * @inheritdoc
     */
    public function updateUser(User $user, array $data): void
    {
        $filledUser = $this->fillUser($user, $data);
        $filledUser->save();
    }

    /**
     * @param User $user
     * @param array $data
     * @return User
     */
    public function updateAndReturnUser(User $user, array $data): User
    {
        $filledUser = $this->fillUser($user, $data);
        $filledUser->save();
        return $filledUser;
    }

    /**
     * @param User $user
     * @return void
     */
    public function deleteUser(User $user): void
    {
        UserPhoneNumber::where('user_id', $user->id)->delete();
        $data = [
            'full_name' => '',
            'email' => sprintf('%s_deleted', $user->email),
            'is_deleted' => 1,
        ];
        $filledUser = $this->fillUser($user, $data);
        $filledUser->password = '';
        $filledUser->save();
    }

    /**
     * @param User $user
     * @param array $data
     * @return User
     */
    protected function fillUser(User $user, array $data): User
    {
        foreach ($user->getFillable() as $column) {
            if (array_key_exists($column, $data)) {
                $value = array_get($data, $column);
                if ($column == 'password') {
                    if (!empty($value)) {
                        $user->$column = bcrypt($value);
                    }
                } elseif ($column == 'role_id' || $column == 'client_id') {
                    $user->$column = intval($value);
                } elseif ($column == 'is_authorized' || $column == 'is_deleted') {
                    $user->$column = in_array($value, self::TRUE_VALUES);
                } else {
                    $user->$column = $value;
                }
            }
        }
        return $user;
    }
}
