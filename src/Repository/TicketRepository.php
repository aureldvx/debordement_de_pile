<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Ticket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ticket>
 *
 * @method Ticket|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ticket|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ticket[]    findAll()
 * @method Ticket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @extends ServiceEntityRepository<Ticket>
 */
class TicketRepository extends ServiceEntityRepository
{
    public const PAGINATOR_TICKETS_PER_PAGE = 20;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ticket::class);
    }

    public function add(Ticket $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Ticket $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Ticket[]
     */
    public function getAll(bool $withClosed = true): array
    {
        $builder = $this
            ->createQueryBuilder('t')
            ->join('t.author', 'a')
            ->where('t.enabled = true')
            ->andWhere('a.blockedAt IS NULL')
            ->orderBy('t.createdAt', 'DESC');

        if (!$withClosed) {
            $builder->andWhere('t.closed = false');
        }

        return $builder
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Ticket[]
     */
    public function findByCategory(Category $category): array
    {
        return $this
            ->createQueryBuilder('t')
            ->join('t.author', 'a')
            ->where('t.enabled = true')
            ->andWhere('t.category = :category')
            ->andWhere('a.blockedAt IS NULL')
            ->orderBy('t.createdAt', 'DESC')
            ->setParameter('category', $category)
            ->getQuery()
            ->getResult();
    }

    /** @return Paginator<Ticket> */
    public function getTicketsPaginator(int $offset, ?Category $category = null, bool $withClosed = true): Paginator
    {
        $builder = $this
            ->createQueryBuilder('t')
            ->join('t.author', 'a')
            ->where('t.enabled = true')
            ->andWhere('a.blockedAt IS NULL')
            ->orderBy('t.createdAt', 'DESC')
            ->setMaxResults(self::PAGINATOR_TICKETS_PER_PAGE)
            ->setFirstResult($offset);

        if ($category) {
            $builder->andWhere('t.category = :category');
            $builder->setParameter('category', $category);
        }

        if (!$withClosed) {
            $builder->andWhere('t.closed = false');
        }

        $query = $builder->getQuery();

        return new Paginator($query);
    }
}
