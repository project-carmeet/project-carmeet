<?php

declare(strict_types=1);

namespace App\Model;

use DateTimeInterface;

interface ResetPasswordTokenAware
{
    public function getEmail(): string;

    /**
     * Check if a token and date has been set.
     */
    public function hasResetPasswordToken(): bool;

    /**
     * The unique token for the forgot password functionality.
     */
    public function getResetPasswordToken(): string;

    /**
     * Set the unique forgot password token.
     */
    public function setResetPasswordToken(string $forgotPasswordToken): void;

    /**
     * Get the timestamp that the forgot password token was set. This can be used to make sure the token is not expired.
     */
    public function getResetPasswordTimestamp(): DateTimeInterface;

    /**
     * Set the timestamp that the forget password token was generated at.
     */
    public function setResetPasswordTimestamp(DateTimeInterface $forgotPasswordTimestamp): void;

    /**
     * Remove the set token.
     */
    public function clearResetPasswordToken(): void;
}
