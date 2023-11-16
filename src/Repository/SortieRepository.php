<?php

namespace App\Repository;

use App\Data\SearchData;
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

    public function findSearch(SearchData $search)
    {
        $query = $this
            ->createQueryBuilder('s')
            ->select('c', 's')
            ->join('s.campus', 'c')
            ->join('s.organisateur', 'p');

        if (!empty($search->s)){
            $query = $query
                ->andWhere('s.nom LIKE :q')
                ->setParameter('q', "%{$search->s}%");
        }
        if (!empty($search->campus)) {
            $query = $query
                ->andWhere('s.campus = :campus')
                ->setParameter('campus', $search->campus);
        }
        if (!empty($search->dateDebut)) {
            $query = $query
                ->andWhere('s.dateHeureDebut >= :dateDebut')
                ->setParameter('dateDebut', $search->dateDebut);
        }
        if (!empty($search->dateFin)) {
            $query = $query
                ->andWhere('s.dateHeureDebut <= :dateFin')
                ->setParameter('dateFin', $search->dateFin);
        }
        if (!empty($search->isOrganisateur)) {
            $query = $query
                ->andWhere('s.organisateur = :isOrganisateur')
                ->setParameter('isOrganisateur', $search->organisateur);
        }

        return $query->getQuery()->getResult();
    }



}
