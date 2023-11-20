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
            ->select('c', 's', 'p', 'e')
            ->leftjoin('s.campus', 'c')
            ->leftjoin('s.participants', 'p')
            ->leftjoin('s.organisateur', 'o')
            ->leftjoin('s.etat', 'e');

        if (!empty($search->s)) {
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
                ->andWhere('s.organisateur = :isOrganisateur OR s.organisateur IS NULL')
                ->setParameter('isOrganisateur', $search->organisateur);
        }
        if (!empty($search->isInscrit) || !empty($search->isNotInscrit)) {
            $participantsConditions = [];

            if (!empty($search->isInscrit)) {
                $participantsConditions[] = ':isInscrit MEMBER OF s.participants';
                $query = $query->setParameter('isInscrit', $search->inscrit);
            }

            if (!empty($search->isNotInscrit)) {
                $participantsConditions[] = ':isNotInscrit NOT MEMBER OF s.participants';
                $query = $query->setParameter('isNotInscrit', $search->notInscrit);
            }
            $query = $query->andWhere(implode(' OR ', $participantsConditions));
        }
        if (!empty($search->isTermine)) {
            $query = $query
                ->andWhere('s.etat = :etatCloture')
                ->setParameter('etatCloture', 10);
        }
        return $query->getQuery()->getResult();
    }
    public function participantsInscritsCounts(): array
    {
        $qb = $this->createQueryBuilder('s')
            ->select('s.id AS sortie_id', 'COUNT(p) AS nombreParticipantsInscrits')
            ->leftJoin('s.participants', 'p')
            ->groupBy('s.id');

        $results = $qb->getQuery()->getResult();

        $nombreParticipantsInscrits = [];
        foreach ($results as $result) {
            $nombreParticipantsInscrits[$result['sortie_id']] = $result['nombreParticipantsInscrits'];
        }
        return $nombreParticipantsInscrits;
    }
}
