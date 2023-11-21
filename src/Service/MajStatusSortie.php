<?php

namespace App\Service;

use App\Entity\Etat;
use App\Entity\Sortie;
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
        $this->updateOuvertesToCloturees();
        $this->updateClotureToEnCours();
        $this->updateEnCoursToPassee();
    }
    private function updateOuvertesToCloturees(): void
    {
        $ouverte = $this->entityManager->getRepository(Sortie::class)->findOuvertes();

        foreach ($ouverte as $sortie) {
            if ($sortie->getDateHeureDebut() < new \DateTime()) {

                // Récupérer l'id de l'état 'Clôturée'
                $etatClotureId = $this->entityManager->getRepository(Etat::class)
                    ->findOneBy(['libelle' => 'Clôturée'])->getId();

                // Récupérer la référence de l'état 'Clôturée' avec l'id
                $etatCloture = $this->entityManager->getReference(Etat::class, $etatClotureId);

                // Mettre à jour l'état de la sortie
                $sortie->setEtat($etatCloture);
            }
        }
        $this->entityManager->flush();
    }
    private function updateClotureToEnCours(): void
    {
        $cloture = $this->entityManager->getRepository(Sortie::class)->findCloture();

        foreach ($cloture as $sortie) {
            if ($sortie->getDateHeureDebut() <= new \DateTime()) {

                // Ajouter la durée de la sortie à la date de début
                $dateFin = clone $sortie->getDateHeureDebut();
                $dateFin->modify('+' . $sortie->getDuree() . ' minutes');

                // Vérifier si la date de fin est atteinte ou dépassée
                if ($dateFin <= new \DateTime()) {

                    // Récupérer l'id de l'état 'En cours'
                    $etatEnCoursId = $this->entityManager->getRepository(Etat::class)
                        ->findOneBy(['libelle' => 'Activité en cours'])->getId();

                    // Récupérer la référence de l'état 'En cours' avec l'id
                    $etatEnCours = $this->entityManager->getReference(Etat::class, $etatEnCoursId);

                    // Mettre à jour l'état de la sortie
                    $sortie->setEtat($etatEnCours);
                }
            }
        }
        $this->entityManager->flush();
    }
    private function updateEnCoursToPassee(): void
    {
        $enCours = $this->entityManager->getRepository(Sortie::class)->findEnCours();

        foreach ($enCours as $sortie) {
            // Ajouter la durée de la sortie à la date de début
            $dateFin = clone $sortie->getDateHeureDebut();
            $dateFin->modify('+' . $sortie->getDuree() . ' minutes');

            // Vérifier si la date de fin est atteinte ou dépassée
            if ($dateFin <= new \DateTime()) {
                // Récupérer l'id de l'état 'Passée'
                $etatPasseeId = $this->entityManager->getRepository(Etat::class)
                    ->findOneBy(['libelle' => 'Passée'])->getId();

                // Récupérer la référence de l'état 'Passée' avec l'id
                $etatPassee = $this->entityManager->getReference(Etat::class, $etatPasseeId);

                // Mettre à jour l'état de la sortie
                $sortie->setEtat($etatPassee);
            }
        }
        $this->entityManager->flush();
    }

}