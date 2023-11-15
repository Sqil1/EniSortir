<?php

namespace App\Repository;

use App\Entity\Participant;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    /**
     * Récupère toutes les sorties en fonction de la recherche
     * @return Sortie[]
     */

// Dans SortieRepository.php
    public function findByCriteria(array $criteria): array
    {
        $queryBuilder = $this->createQueryBuilder('s');

        if (!empty($criteria['nom'])) {
            $queryBuilder->andWhere('s.nom LIKE :nom')
                ->setParameter('nom', '%' . $criteria['nom'] . '%');
        }
        if (!empty($criteria['campus'])) {
            $queryBuilder->andWhere('s.campus = :campus')
                ->setParameter('campus', $criteria['campus']);
        }
        // les autres filtres ici
        $query = $queryBuilder->getQuery();
        return $query->getResult();

    }

}
