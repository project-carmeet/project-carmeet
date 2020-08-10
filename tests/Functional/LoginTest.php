<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Tests\Assert\PathEquals;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

final class LoginTest extends WebTestCase
{
    /**
     * @dataProvider loginProvider
     */
    public function testLogin(string $email, string $password): void
    {
        $client = self::createClient();
        $client->followRedirects();
        $client->request(Request::METHOD_GET, '/login');

        $form = $client->getCrawler()->selectButton('Sign in')->form([
            'email' => $email,
            'password' => $password,
        ]);

        $client->submit($form);
        self::assertThat($client, new PathEquals('/'));

        $client->request(Request::METHOD_GET, '/logout');
        self::assertThat($client, new PathEquals('/'));
    }

    /**
     * @return array<mixed>
     */
    public function loginProvider(): array
    {
        return [
            'admin' => [
                'admin@carmeet.internal',
                'admin',
            ],
            'existing_user' => [
                'existing_user@carmeet.internal',
                'existing_user',
            ],
            'new_user' => [
                'new_user@carmeet.internal',
                'new_user',
            ],
        ];
    }
}
