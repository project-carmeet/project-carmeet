<?php

declare(strict_types=1);

namespace App\Service\Authentication;

use App\Model\ResetPasswordTokenAware;
use DateTimeImmutable;

final class ResetTokenValidator
{
    public function isExpired(ResetPasswordTokenAware $forgotPassword): bool
    {
        return $forgotPassword->getResetPasswordTimestamp() < new DateTimeImmutable('-1 hour');
    }
}
