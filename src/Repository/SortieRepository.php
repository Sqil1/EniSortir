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

    public function findByNomContaining($nom)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.nom LIKE :nom')
            ->setParameter('nom', '%'.$nom.'%')
            ->getQuery()
            ->getResult();
    }

    public function findParticipations(Participant $participant)
    {
        return $this->createQueryBuilder('s')
            ->join('s.participants', 'p')
            ->andWhere('p.id = :participantId')
            ->setParameter('participantId', $participant->getId())
            ->getQuery()
            ->getResult();
    }
}
