<?php

declare(strict_types=1);

namespace App\Event\User;

use App\Entity\User;
use App\Model\Form\UserModel;
use LogicException;

final class RegisterEvent
{
    /**
     * @var UserModel
     */
    private $userModel;

    /**
     * @var User|null
     */
    private $user;

    public function __construct(UserModel $userModel)
    {
        $this->userModel = $userModel;
    }

    public function getUserModel(): UserModel
    {
        return $this->userModel;
    }

    public function getUser(): User
    {
        if (null === $this->user) {
            throw new LogicException('No user set.');
        }

        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }
}
