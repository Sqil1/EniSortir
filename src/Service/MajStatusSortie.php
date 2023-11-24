<?php

namespace App\Service;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;

class MajStatusSortie

{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function updateSortieStates(): void
    {
        $this->updateOuverteToCloturee();
        $this->updateClotureToEnCours();
        $this->updateEnCoursToPassee();
        $this->updatePasseeToHistorisee();
        $this->updateAnnuleeToHistorisee();
    }

    private function updateOuverteToCloturee(): void
    {
        $sortiesOuvertesToCloturees = $this->entityManager->getRepository(Sortie::class)->findOuvertesToCloturees();
        $etatCloturee = $this->entityManager->getRepository(Etat::class)
            ->findOneBy(['libelle' => 'Cloturée']);
        foreach ($sortiesOuvertesToCloturees as $sortie) {
            $sortie->setEtat($etatCloturee);
        }
        $this->entityManager->flush();
    }

    private function updateClotureToEnCours(): void
    {
        $sortiesClotureToEnCours = $this->entityManager->getRepository(Sortie::class)->findClotureesToEnCours();
        $etatEnCours = $this->entityManager->getRepository(Etat::class)
            ->findOneBy(['libelle' => 'Activité en cours']);
        foreach ($sortiesClotureToEnCours as $sortie) {
            $sortie->setEtat($etatEnCours);
        }
        $this->entityManager->flush();
    }
    private function updateEnCoursToPassee(): void
    {
        $sortiesEnCoursToPassee = $this->entityManager->getRepository(Sortie::class)->findEnCoursToPassee();
        $etatPassee = $this->entityManager->getRepository(Etat::class)
            ->findOneBy(['libelle' => 'Passée']);
        foreach ($sortiesEnCoursToPassee as $sortie){
            $sortie->setEtat($etatPassee);
        }
        $this->entityManager->flush();
    }
    private function updatePasseeToHistorisee(): void
    {
        $sortiesPasseeToHistorisee = $this->entityManager->getRepository(Sortie::class)->updatePasseeToHistorisee();
        $etatHistorisee = $this->entityManager->getRepository(Etat::class)
            ->findOneBy(['libelle' => 'Historisée']);
        foreach ($sortiesPasseeToHistorisee as $sortie){
            $sortie->setEtat($etatHistorisee);
        }
        $this->entityManager->flush();
    }
    private function updateAnnuleeToHistorisee(): void
    {
        $sortiesAnnuleeToHistorisee = $this->entityManager->getRepository(Sortie::class)->updateAnnuleeToHistorisee();
        $etatHistorisee = $this->entityManager->getRepository(Etat::class)
            ->findOneBy(['libelle' => 'Historisée']);
        foreach ($sortiesAnnuleeToHistorisee as $sortie){
            $sortie->setEtat($etatHistorisee);
        }
        $this->entityManager->flush();
    }

}