<?php

declare(strict_types=1);

namespace App\Entity;

use BadMethodCallException;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(indexes={@ORM\Index(columns={"username"}), @ORM\Index(columns={"email"})})
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="string", nullable=false)
     *
     * @var string $id
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=false)
     *
     * @var string
     */
    protected $username;

    /**
     * @ORM\Column(type="string", nullable=false)
     *
     * @var string
     */
    protected $email;

    /**
     * @ORM\Column(type="string", nullable=false)
     *
     * @var string|null
     */
    protected $password;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string|null
     */
    protected $salt;

    /**
     * @ORM\Column(type="array", nullable=false)
     *
     * @var array<int, string>
     */
    protected $roles;

    /**
     * @param array<int, string> $roles
     */
    public function __construct(string $username, string $email, ?string $password = null, ?string $salt = null, array $roles = [])
    {
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->salt = $salt;
        $this->roles = $roles;
    }

    public function getId(): string
    {
        return $this->id;
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

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @inheritDoc
     */
    public function getPassword(): string
    {
        if (null === $this->password) {
            throw new BadMethodCallException('No password set. Use setPassword to set a password first.');
        }

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
