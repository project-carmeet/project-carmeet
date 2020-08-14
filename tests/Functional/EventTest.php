<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Tests\Authenticator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

final class EventTest extends WebTestCase
{
    public function testCreate(): string
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

        return $this->getIdFromUrl($crawler->getUri());
    }

    /**
     * @depends testCreate
     */
    public function testEdit(string $id): string
    {
        $client = self::createClient();
        Authenticator::user($client);

        $crawler = $client->request(Request::METHOD_GET, sprintf('/event/edit/%s', $id));

        $form = $crawler->selectButton('Edit')->form([
            'event[name]' => 'Test event (renamed)',
            'event[description]' => 'Some other description',
        ]);

        $client->followRedirects();
        $crawler = $client->submit($form);

        $pageContents = $crawler->text();
        self::assertStringContainsString('Test event (renamed)', $pageContents);
        self::assertStringContainsString('Some other description', $pageContents);

        return $id;
    }

    /**
     * @depends testEdit
     */
    public function testCancel(string $id): string
    {
        $client = self::createClient();
        Authenticator::user($client);

        $client->followRedirects();
        $crawler = $client->request(Request::METHOD_GET, sprintf('/event/cancel/%s', $id));

        $pageContents = $crawler->text();
        self::assertStringContainsString(sprintf('Event has been cancelled on %s.', date('Y-m-d')), $pageContents);
        self::assertStringContainsString('If this is not correct, you can contact the administrators to reopen the event.', $pageContents);

        return $id;
    }

    /**
     * @depends testCancel
     */
    public function testReopenAsUser(string $id): string
    {
        $client = self::createClient();
        Authenticator::user($client);

        $client->request(Request::METHOD_GET, sprintf('/event/reopen/%s', $id));
        self::assertResponseStatusCodeSame(403);

        return $id;
    }

    /**
     * @depends testReopenAsUser
     */
    public function testReopenAsAdmin(string $id): void
    {
        $client = self::createClient();
        Authenticator::admin($client);

        $client->followRedirects();
        $crawler = $client->request(Request::METHOD_GET, sprintf('/event/reopen/%s', $id));

        $pageContents = $crawler->text();
        self::assertStringNotContainsString(sprintf('Event has been cancelled on %s.', date('Y-m-d')), $pageContents);
    }

    private function getIdFromUrl(string $url): string
    {
        preg_match('/\/event\/[a-z]+\/(?<id>[a-z0-9\-]+)/i', $url, $match);
        $id = $match['id'] ?? null;

        self::assertIsString($id);

        return $id;
    }
}
