<?php

declare(strict_types=1);

namespace App\Event\User;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

final class ResetPasswordEvent extends Event
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var string
     */
    private $newPassword;

    public function __construct(User $user, string $newPassword)
    {
        $this->user = $user;
        $this->newPassword = $newPassword;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getNewPassword(): string
    {
        return $this->newPassword;
    }
}
