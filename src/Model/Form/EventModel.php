<?php

declare(strict_types=1);

namespace App\Model\Form;

use App\Entity\User;
use DateTimeInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class EventModel
{
    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var string|null
     */
    private $description;

    /**
     * @var DateTimeInterface|null
     */
    private $dateFrom;

    /**
     * @var DateTimeInterface|null
     */
    private $dateUntil;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
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

    public function getDateFrom(): ?DateTimeInterface
    {
        return $this->dateFrom;
    }

    public function setDateFrom(?DateTimeInterface $dateFrom): void
    {
        $this->dateFrom = $dateFrom;
    }

    public function getDateUntil(): ?DateTimeInterface
    {
        return $this->dateUntil;
    }

    public function setDateUntil(?DateTimeInterface $dateUntil): void
    {
        $this->dateUntil = $dateUntil;
    }
}
