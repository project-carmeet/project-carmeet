<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Tests\Authenticator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

final class EventTest extends WebTestCase
{
    public function testCreate(): void
    {
        $client = self::createClient();
        Authenticator::user($client);

        $crawler = $client->request(Request::METHOD_GET, '/event/create');
        $form = $crawler->selectButton('Create')->form([
            'event[name]' => 'Test event',
            'event[description]' => 'Some test event',
            'event[date_from][time][hour]' => '13',
            'event[date_from][time][minute]' => '0',
            'event[date_until][time][hour]' => '14',
            'event[date_until][time][minute]' => '0',
        ]);

        $client->followRedirects();
        $crawler = $client->submit($form);

        $pageContents = $crawler->text();
        self::assertStringContainsString('Test event', $pageContents);
        self::assertStringContainsString(sprintf('%s 13:00 / 14:00', date('Y-m-d')), $pageContents);
        self::assertStringContainsString('By existing_user', $pageContents);
        self::assertStringContainsString('Some test event', $pageContents);
    }
}
