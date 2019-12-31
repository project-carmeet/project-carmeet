<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity()
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="string")
     *
     * @var string $id
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $username;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $password;

    /**
     * @var string|null
     */
    protected $salt;

    /**
     * @ORM\Column(type="array")
     *
     * @var array<int, string>
     */
    protected $roles;

    /**
     * @param array<int, string> $roles
     */
    public function __construct(string $username, string $password, ?string $salt, array $roles)
    {
        $this->username = $username;
        $this->password = $password;
        $this->salt = $salt;
        $this->roles = $roles;
    }

    /**
     * @inheritDoc
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @inheritDoc
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @inheritDoc
     */
    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function setSalt(?string $salt): void
    {
        $this->salt = $salt;
    }

    /**
     * @inheritDoc
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials(): void
    {
    }
}
