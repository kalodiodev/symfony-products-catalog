<?php

namespace App\Repository;

use App\Entity\Brand;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Brand|null find($id, $lockMode = null, $lockVersion = null)
 * @method Brand|null findOneBy(array $criteria, array $orderBy = null)
 * @method Brand[]    findAll()
 * @method Brand[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BrandRepository extends ServiceEntityRepository
{
    protected $paginator;

    public function __construct(RegistryInterface $registry, PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;

        parent::__construct($registry, Brand::class);
    }

    /**
     * Find all brands paginated
     *
     * @param $page
     * @param int $items
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    public function findAllPaginated($page, $items = 20)
    {
        $dbQuery = $this->createQueryBuilder('b')
            ->getQuery();

        return $this->paginator->paginate($dbQuery, $page, $items);
    }

    // /**
    //  * @return Brand[] Returns an array of Brand objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Brand
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
