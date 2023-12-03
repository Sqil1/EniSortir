<?php

namespace App\Repository;

use App\Data\SearchDataCampus;
use App\Data\SearchDataVille;
use App\Entity\Ville;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ville>
 *
 * @method Ville|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ville|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ville[]    findAll()
 * @method Ville[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VilleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ville::class);
    }

    public function findSearch(SearchDataVille $searchVille)
    {
        $query = $this
            ->createQueryBuilder('v')
            ->select('v')
            ->orderBy('v.nom', 'ASC');

        if (!empty($searchVille->v)) {
            $query = $query
                ->andWhere('v.nom LIKE :q')
                ->setParameter('q', "%{$searchVille->v}%");
        }
        if (!empty($searchVille->codePostal)) {
            $query = $query
                ->andWhere('v.codePostal LIKE :q')
                ->setParameter('q', "%{$searchVille->codePostal}%");
        }
        return $query->getQuery()->getResult();
    }
}
