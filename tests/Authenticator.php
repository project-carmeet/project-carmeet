<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\HttpFoundation\Request;

final class Authenticator
{
    public static function authenticate(AbstractBrowser $client, string $email, string $password): void
    {
        $crawler = $client->request(Request::METHOD_GET, '/login');

        $form = $crawler->selectButton('Sign in')->form([
            'email' => $email,
            'password' => $password,
        ]);

        $client->submit($form);
        $client->followRedirect();
    }

    public static function admin(AbstractBrowser $client): void
    {
        self::authenticate($client, 'admin@carmeet.internal', 'admin');
    }

    public static function user(AbstractBrowser $client): void
    {
        self::authenticate($client, 'existing_user@carmeet.internal', 'existing_user');
    }
}
