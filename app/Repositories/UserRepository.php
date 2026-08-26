<?php

declare(strict_types=1);

namespace SchoolERP\Repositories;

use SchoolERP\Models\User;

final class UserRepository extends Repository
{
    /**
     * Create a UserRepository.
     */
    public function __construct()
    {
        parent::__construct(
            new User()
        );
    }

    /**
     * Find an active user by email address.
     */
    public function findActiveByEmail(
        string $email
    ): ?User {
        $record = $this->model
            ->query()
            ->where(
                'email',
                '=',
                trim($email)
            )
            ->where(
                'status',
                '=',
                'active'
            )
            ->first();

        if ($record === null) {
            return null;
        }

        return (new User())->fill($record);
    }

    /**
     * Update the user's last login timestamp.
     */
    public function updateLastLogin(
        int $userId
    ): bool {
        $user = $this->find($userId);

        if ($user === null) {
            return false;
        }

        $affected = $user->update([
            'last_login' => date('Y-m-d H:i:s'),
        ]);

        return $affected > 0;
    }
}