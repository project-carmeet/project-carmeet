<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Event;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use UnexpectedValueException;

final class EventFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $events = [
            new Event(
                'Large carmeet',
                'This is a large carmeet.',
                new DateTimeImmutable('today 13:00'),
                new DateTimeImmutable('today 18:00'),
                $this->getUser('admin')
            ),
            new Event(
                'Small carmeet',
                'This is a small carmeet.',
                new DateTimeImmutable('today 13:00'),
                new DateTimeImmutable('today 18:00'),
                $this->getUser('admin')
            ),
            new Event(
                'Short carmeet',
                'This is a large carmeet.',
                new DateTimeImmutable('today 13:00'),
                new DateTimeImmutable('today 14:00'),
                $this->getUser('existing_user')
            ),
            new Event(
                'Long carmeet',
                'This is a large carmeet.',
                new DateTimeImmutable('today 13:00'),
                new DateTimeImmutable('tomorrow'),
                $this->getUser('existing_user')
            ),
        ];

        foreach ($events as $event) {
            $manager->persist($event);
        }

        $manager->flush();
    }

    private function getUser(string $username): User
    {
        $referenceKey = UserFixtures::createReferenceKey($username);
        $user = $this->getReference($referenceKey);
        if (!$user instanceof User) {
            throw new UnexpectedValueException(sprintf(
                'Expected reference for key "%s" to be instance of "%s".',
                $referenceKey,
                User::class
            ));
        }

        return $user;
    }

    /**
     * @inheritDoc
     */
    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }
}
