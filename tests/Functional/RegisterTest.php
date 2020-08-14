<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Tests\Assert\PathEquals;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\SwiftmailerBundle\DataCollector\MessageDataCollector;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;

final class RegisterTest extends WebTestCase
{
    public function testRegister(): string
    {
        $id = 'test_' . random_int(0, mt_getrandmax());
        $email = $id . '@carmeet.internal';
        $password = 'some-password';

        $client = self::createClient();
        $client->request(Request::METHOD_GET, '/register');

        $form = $client->getCrawler()->selectButton('Register')->form([
            'registration[username]' => $id,
            'registration[email]' => $email,
            'registration[password][first]' => $password,
            'registration[password][second]' => $password,
        ]);

        $client->enableProfiler();
        $client->submit($form);

        /** @var MessageDataCollector $profile */
        $profile = $client->getProfile()->getCollector('swiftmailer');
        $messages = $profile->getMessages();
        self::assertCount(1, $messages);

        /** @var Swift_Message $message */
        $message = reset($messages);
        $body = new Crawler($message->getBody());
        $activationLink = $body->filter('a')->attr('href');

        $client->followRedirects();
        $crawler = $client->request(Request::METHOD_GET, $activationLink);
        self::assertThat($client, new PathEquals('/login'));

        $form = $crawler->selectButton('Sign in')->form([
            'email' => $email,
            'password' => $password,
        ]);

        $client->submit($form);
        self::assertThat($client, new PathEquals('/'));

        return $email;
    }

    /**
     * @depends testRegister
     */
    public function testForgotPassword(string $email): void
    {
        $client = self::createClient();

        $client->request(Request::METHOD_GET, '/forgot-password');

        $form = $client->getCrawler()->selectButton('Send')->form([
            'forgot_password[email]' => $email,
        ]);

        $client->enableProfiler();
        $client->submit($form);

        /** @var MessageDataCollector $profile */
        $profile = $client->getProfile()->getCollector('swiftmailer');
        $messages = $profile->getMessages();
        self::assertCount(1, $messages);

        /** @var Swift_Message $message */
        $message = reset($messages);
        $body = new Crawler($message->getBody());
        $resetLink = $body->filter('a')->attr('href');

        $crawler = $client->request(Request::METHOD_GET, $resetLink);
        $form = $crawler->selectButton('Change')->form([
            'repeated_password[first]' => 'other_password',
            'repeated_password[second]' => 'other_password',
        ]);

        $client->followRedirects();
        $crawler = $client->submit($form);

        $form = $crawler->selectButton('Sign in')->form([
            'email' => $email,
            'password' => 'other_password',
        ]);

        $client->submit($form);
        self::assertThat($client, new PathEquals('/'));
    }
}
