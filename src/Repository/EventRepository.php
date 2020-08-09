<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @phpstan-extends ServiceEntityRepository<Event>
 *
 * @psalm-suppress  PropertyNotSetInConstructor
 */
final class EventRepository extends ServiceEntityRepository
{
    /**
     * @psalm-suppress ArgumentTypeCoercion
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }
}
