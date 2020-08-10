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

        $startDate = [
            'date' => [
                'year' => date('Y'),
                'month' => date('n'),
                'day' => date('j'),
            ],
            'time' => [
                'hour' => '13',
                'minute' => '0',
            ],
        ];

        $endDate = $startDate;
        $endDate['time'] = [
            'hour' => '14',
            'minute' => '0',
        ];

        $form = $crawler->selectButton('Create')->form([
            'event[name]' => 'Test event',
            'event[description]' => 'Some test event',
            'event[date_from]' => $startDate,
            'event[date_until]' => $endDate,
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
