<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager): void
    {
        $user = new User('admin', 'admin', null, []);

        $manager->persist($user);
        $manager->flush();
    }
}
