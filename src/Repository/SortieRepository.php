<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Integer;

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
     * @param int $idUtilisateurConnecte
     * @return Sortie[]
     */

    public function findSearch(SearchData $search)
    {
        $query = $this
            ->createQueryBuilder('s')
            ->select('c', 's', 'p', 'e')
            ->join('s.campus', 'c')
            ->leftJoin('s.participants', 'p')
            ->leftJoin('s.organisateur', 'o')
            ->join('s.etat', 'e')
            ->where('e.libelle != :etatHistorisee')
            ->setParameter('etatHistorisee', 'Historisée')
            ->orderBy('s.dateHeureDebut', 'DESC');

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
                ->andWhere('s.organisateur = :idUtilisateurConnecte OR s.organisateur IS NULL')
                ->setParameter('idUtilisateurConnecte', $search->utilisateurInscrit);
        }
        if (!empty($search->isInscrit) || !empty($search->isNotInscrit)) {
            $participantsConditions = [];

            if (!empty($search->isInscrit)) {
                $participantsConditions[] = ':idUtilisateurConnecte MEMBER OF s.participants';
                $query = $query->setParameter('idUtilisateurConnecte', $search->utilisateurInscrit);
            }

            if (!empty($search->isNotInscrit)) {
                $participantsConditions[] = ':idUtilisateurConnecte NOT MEMBER OF s.participants';
                $query = $query->setParameter('idUtilisateurConnecte', $search->utilisateurInscrit);
            }
            $query = $query->andWhere(implode(' OR ', $participantsConditions));
        }
        if (!empty($search->isTermine)) {
            $query = $query
                ->andWhere('s.etat = :etatCloture')
                ->setParameter('etatCloture', 5);
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
    public function findByEtat(string $etatLibelle): array
    {
        $queryBuilder = $this->createQueryBuilder('s')
            ->join('s.etat', 'e')
            ->andWhere('e.libelle = :etatLibelle')
            ->setParameter('etatLibelle', $etatLibelle);

        return $queryBuilder->getQuery()->getResult();
    }
    public function findOuverteToFermee()
    {
        $queryBuilder = $this
            ->createQueryBuilder('s')
            ->select('s', 'e')
            ->join('s.etat', 'e')
            ->andWhere('s.etat = :etatOuvert')
            ->andWhere('s.dateLimiteInscription > :dateActuelle')
            ->setParameter('etatOuvert', 02)
            ->setParameter('dateActuelle', new \DateTime('midnight'));
        return $queryBuilder->getQuery()->getResult();
    }
    public function findFermeeToOuvert()
    {
        $queryBuilder = $this
            ->createQueryBuilder('s')
            ->select('s', 'e')
            ->join('s.etat', 'e')
            ->andWhere('s.etat = :etatOuvert')
            ->andWhere('s.dateLimiteInscription > :dateActuelle')
            ->setParameter('etatOuvert', 03)
            ->setParameter('dateActuelle', new \DateTime('midnight'));
        return $queryBuilder->getQuery()->getResult();
    }
}
