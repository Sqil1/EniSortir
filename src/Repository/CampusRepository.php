<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Data\SearchDataCampus;
use App\Entity\Campus;
use App\Form\SearchFormCampus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Campus>
 *
 * @method Campus|null find($id, $lockMode = null, $lockVersion = null)
 * @method Campus|null findOneBy(array $criteria, array $orderBy = null)
 * @method Campus[]    findAll()
 * @method Campus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CampusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Campus::class);
    }

    public function findSearch(SearchDataCampus $searchCampus)
    {
        $query = $this
            ->createQueryBuilder('c')
            ->select('c')
            ->orderBy('c.nom', 'ASC');

        if (!empty($searchCampus->c)) {
            $query = $query
                ->andWhere('c.nom LIKE :q')
                ->setParameter('q', "%{$searchCampus->c}%");
        }
        return $query->getQuery()->getResult();
    }

}
