<?php

declare(strict_types=1);

namespace App\Model\Form;

final class ForgotPasswordModel
{
    /**
     * @var string|null
     */
    private $email;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }
}
