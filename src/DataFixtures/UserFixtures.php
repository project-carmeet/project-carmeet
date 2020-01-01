<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class UserFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    protected $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager): void
    {
        $admin = $this->createUser('admin');
        $manager->persist($admin);

        $newUser = $this->createUser('new_user');
        $manager->persist($newUser);

        $existingUser = $this->createUser('existing_user');
        $manager->persist($existingUser);

        $manager->flush();
    }

    private function createUser(string $username): User
    {
        $user = new User($username, sprintf('%s@carmeet.internal', $username));

        $password = $this->encoder->encodePassword($user, $username);
        $user->setPassword($password);

        return $user;
    }
}