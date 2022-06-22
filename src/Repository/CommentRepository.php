<?php

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\Ticket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 *
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @extends ServiceEntityRepository<Comment>
 */
class CommentRepository extends ServiceEntityRepository
{
    public const PAGINATOR_COMMENTS_PER_PAGE = 20;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function add(Comment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Comment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Comment[]
     */
    public function getAll(): array
    {
        return $this
            ->createQueryBuilder('c')
            ->join('c.author', 'a')
            ->where('c.enabled = true')
            ->andWhere('a.blockedAt IS NULL')
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Comment[]
     */
    public function findByTicket(Ticket $ticket): array
    {
        return $this
            ->createQueryBuilder('c')
            ->join('c.author', 'a')
            ->where('c.enabled = true')
            ->andWhere('a.blockedAt IS NULL')
            ->andWhere('c.ticket = :ticket')
            ->orderBy('c.createdAt', 'DESC')
            ->setParameter('ticket', $ticket)
            ->getQuery()
            ->getResult();
    }

    /** @return Paginator<Comment> */
    public function getCommentsPaginator(int $offset, ?Ticket $ticket = null): Paginator
    {
        $builder = $this
            ->createQueryBuilder('c')
            ->join('c.author', 'a')
            ->where('c.enabled = true')
            ->andWhere('a.blockedAt IS NULL')
            ->andWhere('c.parent IS NULL')
            ->orderBy('c.createdAt', 'DESC')
            ->setMaxResults(self::PAGINATOR_COMMENTS_PER_PAGE)
            ->setFirstResult($offset);

        if ($ticket) {
            $builder->andWhere('c.ticket = :ticket');
            $builder->setParameter('ticket', $ticket);
        }

        $query = $builder->getQuery();

        return new Paginator($query);
    }
}
