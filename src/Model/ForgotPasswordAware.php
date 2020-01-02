<?php

declare(strict_types=1);

namespace App\Model;

use DateTimeInterface;

interface ForgotPasswordAware
{
    public function getEmail(): string;

    /**
     * The unique token for the forgot password functionality.
     */
    public function getForgotPasswordToken(): ?string;

    /**
     * Set the unique forgot password token.
     */
    public function setForgotPasswordToken(?string $forgotPasswordToken): void;

    /**
     * Get the timestamp that the forgot password token was set. This can be used to make sure the token is not expired.
     */
    public function getForgotPasswordTimestamp(): ?DateTimeInterface;

    /**
     * Set the timestamp that the forget password token was generated at.
     */
    public function setForgotPasswordTimestamp(?DateTimeInterface $forgotPasswordTimestamp): void;
}
