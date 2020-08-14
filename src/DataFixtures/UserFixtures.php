<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use App\Security\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
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

    public static function createReferenceKey(string $username): string
    {
        return sprintf('user_%s', $username);
    }

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager): void
    {
        $admin = $this->createUser('admin', true, [Role::USER, Role::ADMIN]);
        $manager->persist($admin);

        $newUser = $this->createUser('new_user', true);
        $manager->persist($newUser);

        $existingUser = $this->createUser('existing_user', true);
        $manager->persist($existingUser);

        $existingUser = $this->createUser('inactive', false);
        $manager->persist($existingUser);

        $manager->flush();
    }

    /**
     * @param array<int, string>|null $roles
     */
    private function createUser(string $username, bool $active, ?array $roles = null): User
    {
        $user = new User($username, sprintf('%s@carmeet.internal', $username));

        $password = $this->encoder->encodePassword($user, $username);
        $user->setPassword($password);
        $user->setRoles($roles ?? [Role::USER]);
        $user->setActive($active);

        $this->setReference(static::createReferenceKey($username), $user);

        return $user;
    }
}
