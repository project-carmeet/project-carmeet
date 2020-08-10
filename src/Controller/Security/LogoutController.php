<?php

declare(strict_types=1);

namespace App\Controller\Security;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class LogoutController extends AbstractController
{
    public function __invoke()
    {
        throw new LogicException('This should be intercepted by the firewall.');
    }
}
