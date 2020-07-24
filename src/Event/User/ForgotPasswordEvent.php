<?php

declare(strict_types=1);

namespace App\Event\User;

use App\Model\ResetPasswordTokenAware;
use Symfony\Contracts\EventDispatcher\Event;

final class ForgotPasswordEvent extends Event
{
    /**
     * @var ResetPasswordTokenAware
     */
    protected $user;

    public function __construct(ResetPasswordTokenAware $user)
    {
        $this->user = $user;
    }

    public function getUser(): ResetPasswordTokenAware
    {
        return $this->user;
    }

    public function setUser(ResetPasswordTokenAware $user): void
    {
        $this->user = $user;
    }
}
