<?php

namespace App\Repository;

use App\Entity\User;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    protected $paginator;

    public function __construct(RegistryInterface $registry, PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;

        parent::__construct($registry, User::class);
    }

    /**
     * Find all users paginated
     *
     * @param $page
     * @param int $items
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    public function findAllPaginated($page, $items = 20)
    {
        $dbQuery = $this->createQueryBuilder('u')
            ->getQuery();

        return $this->paginator->paginate($dbQuery, $page, $items);
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
