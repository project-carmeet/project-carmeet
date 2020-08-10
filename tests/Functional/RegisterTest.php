<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Tests\Assert\PathEquals;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

final class RegisterTest extends WebTestCase
{
    public function testRegister(): void
    {
        $id = 'test_' . random_int(0, mt_getrandmax());
        $email = $id . '@carmeet.internal';
        $password = 'some-password';

        $client = self::createClient();
        $client->followRedirects();
        $client->request(Request::METHOD_GET, '/register');

        $form = $client->getCrawler()->selectButton('Register')->form([
            'registration[username]' => $id,
            'registration[email]' => $email,
            'registration[password][first]' => $password,
            'registration[password][second]' => $password,
        ]);

        $client->submit($form);

        self::assertThat($client, new PathEquals('/login'));

        $form = $client->getCrawler()->selectButton('Sign in')->form([
            'email' => $email,
            'password' => $password,
        ]);

        $client->submit($form);
        self::assertThat($client, new PathEquals('/'));
    }
}
