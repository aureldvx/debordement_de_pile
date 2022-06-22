<?php

namespace App\Repository;

use App\Entity\LoginActivity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LoginActivity>
 *
 * @method LoginActivity|null find($id, $lockMode = null, $lockVersion = null)
 * @method LoginActivity|null findOneBy(array $criteria, array $orderBy = null)
 * @method LoginActivity[]    findAll()
 * @method LoginActivity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @extends ServiceEntityRepository<LoginActivity>
 */
class LoginActivityRepository extends ServiceEntityRepository
{
    public const PAGINATOR_ACTIVITIES_PER_PAGE = 25;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LoginActivity::class);
    }

    public function add(LoginActivity $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(LoginActivity $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /** @return Paginator<LoginActivity> */
    public function getActivitiesPaginator(int $offset): Paginator
    {
        $query = $this
            ->createQueryBuilder('l')
            ->orderBy('l.connectedAt', 'DESC')
            ->setMaxResults(self::PAGINATOR_ACTIVITIES_PER_PAGE)
            ->setFirstResult($offset)
            ->getQuery();

        return new Paginator($query);
    }
}
