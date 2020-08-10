<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use UnexpectedValueException;

/**
 * @ORM\Entity()
 */
class Event
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
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @var DateTimeInterface
     *
     * @ORM\Column(type="datetime_immutable", nullable=false)
     */
    private $dateFrom;

    /**
     * @var DateTimeInterface
     *
     * @ORM\Column(type="datetime_immutable", nullable=false)
     */
    private $dateUntil;

    /**
     * @var DateTimeInterface|null
     *
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $cancellationDate;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $user;

    public function __construct(string $name, ?string $description, DateTimeInterface $dateFrom, DateTimeInterface $dateUntil, User $user)
    {
        $this->name = $name;
        $this->description = $description;
        $this->dateFrom = $dateFrom;
        $this->dateUntil = $dateUntil;
        $this->user = $user;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getDateFrom(): DateTimeInterface
    {
        return $this->dateFrom;
    }

    public function setDateFrom(DateTimeInterface $dateFrom): void
    {
        $this->dateFrom = $dateFrom;
    }

    public function getDateUntil(): DateTimeInterface
    {
        return $this->dateUntil;
    }

    public function setDateUntil(DateTimeInterface $dateUntil): void
    {
        $this->dateUntil = $dateUntil;
    }

    public function isCancelled(): bool
    {
        return null !== $this->cancellationDate;
    }

    public function getCancellationDate(): ?DateTimeInterface
    {
        if (null === $this->cancellationDate) {
            throw new UnexpectedValueException('Cancellation date not found. Use isCancelled() to check if the event is cancelled.');
        }

        return $this->cancellationDate;
    }

    public function setCancellationDate(?DateTimeInterface $cancellationDate): void
    {
        $this->cancellationDate = $cancellationDate;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }
}
