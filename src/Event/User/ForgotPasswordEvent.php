<?php

declare(strict_types=1);

namespace App\Event\User;

use App\Model\ForgotPasswordAware;
use Symfony\Contracts\EventDispatcher\Event;

final class ForgotPasswordEvent extends Event
{
    /**
     * @var ForgotPasswordAware
     */
    protected $user;

    public function __construct(ForgotPasswordAware $user)
    {
        $this->user = $user;
    }

    public function getUser(): ForgotPasswordAware
    {
        return $this->user;
    }

    public function setUser(ForgotPasswordAware $user): void
    {
        $this->user = $user;
    }
}
