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
        $this->updateOuverteToEnCours();
        $this->updateEnCoursToPassee();
        $this->updateOuvertesToCloturees();
        $this->updatePasseToHisto();
    }

    private function updateOuverteToEnCours(): void
    {
        $ouverte = $this->entityManager->getRepository(Sortie::class)->findByEtat('Ouverte');

        $dateActuelle = new \DateTime();

        foreach ($ouverte as $sortie) {
            if ($sortie->getDateHeureDebut() <= $dateActuelle) {
                $dateFin = clone $sortie->getDateHeureDebut();
                $dateFin->modify('+' . $sortie->getDuree() . ' minutes');
                if ($dateFin >= $dateActuelle) {
                    $etatEnCoursId = $this->entityManager->getRepository(Etat::class)
                        ->findOneBy(['libelle' => 'Activité en cours'])->getId();
                    $etatEnCours = $this->entityManager->getReference(Etat::class, $etatEnCoursId);
                    $sortie->setEtat($etatEnCours);
                }
            }
        }
        $this->entityManager->flush();
    }
    private function updateEnCoursToPassee(): void
    {
        $enCours = $this->entityManager->getRepository(Sortie::class)->findByEtat('Activité en cours');

        $dateActuelle = new \DateTime();

        foreach ($enCours as $sortie) {
            $dateFin = clone $sortie->getDateHeureDebut();
            $dateFin->modify('+' . $sortie->getDuree() . ' minutes');
            if ($dateFin <= new $dateActuelle) {
                $etatPasseeId = $this->entityManager->getRepository(Etat::class)
                    ->findOneBy(['libelle' => 'Passée'])->getId();
                $etatPassee = $this->entityManager->getReference(Etat::class, $etatPasseeId);
                $sortie->setEtat($etatPassee);
            }
        }
        $this->entityManager->flush();
    }

    private function updateOuvertesToCloturees(): void
    {
        $ouvertes = $this->entityManager->getRepository(Sortie::class)->findByEtat('Ouverte');
        $clotures = $this->entityManager->getRepository(Sortie::class)->findByEtat('Clôturée');

        $dateActuelle = new \DateTime();

        foreach ($ouvertes as $sortie) {
            $nombreParticipantsInscrits = count($sortie->getParticipants());
            if ($nombreParticipantsInscrits < $sortie->getNbInscriptionsMax()) {
                if ($sortie->getDateLimiteInscription() > $dateActuelle) {
                $etatOuverteId = $this->entityManager->getRepository(Etat::class)
                    ->findOneBy(['libelle' => 'Ouverte'])->getId();
                $etatOuverte = $this->entityManager->getReference(Etat::class, $etatOuverteId);
                $sortie->setEtat($etatOuverte);
                }
            } else {
                    $etatClotureId = $this->entityManager->getRepository(Etat::class)
                        ->findOneBy(['libelle' => 'Cloturée'])->getId();
                    $etatCloture = $this->entityManager->getReference(Etat::class, $etatClotureId);
                    $sortie->setEtat($etatCloture);
            }
        }
        foreach ($clotures as $sortieCloture) {
            $nombreParticipantsInscritsCloture = count($sortieCloture->getParticipants());
            if ($sortieCloture->getDateHeureDebut() > $dateActuelle && $nombreParticipantsInscritsCloture < $sortieCloture->getNbInscriptionsMax()) {
                $etatOuverteId = $this->entityManager->getRepository(Etat::class)
                    ->findOneBy(['libelle' => 'Ouverte'])->getId();
                $etatOuverte = $this->entityManager->getReference(Etat::class, $etatOuverteId);
                $sortieCloture->setEtat($etatOuverte);
            }
        }
        $this->entityManager->flush();
    }

    private function updatePasseToHisto(): void
    {
        $passee = $this->entityManager->getRepository(Sortie::class)->findByEtat('Passée');
        $annulee = $this->entityManager->getRepository(Sortie::class)->findByEtat('Annulée');

        $dateActuelle = new \DateTime();

        $dateMoinsUnMois = $dateActuelle->sub(new \DateInterval('P1M'));

        foreach ($passee as $sortie) {
            if ($sortie->getDateHeureDebut() <= $dateActuelle) {
                $dateFin = clone $sortie->getDateHeureDebut();
                $dateFin->modify('+' . $sortie->getDuree() . ' minutes');

                if ($dateFin < $dateMoinsUnMois) {
                    $etatHistoriseeId = $this->entityManager->getRepository(Etat::class)
                        ->findOneBy(['libelle' => 'Historisée'])->getId();
                    $etatHistorisee = $this->entityManager->getReference(Etat::class, $etatHistoriseeId);
                    $sortie->setEtat($etatHistorisee);
                }
            }
        }
        foreach ($annulee as $sortie) {
            if ($sortie->getDateHeureDebut() <= $dateActuelle) {
                $dateFin = clone $sortie->getDateHeureDebut();
                $dateFin->modify('+' . $sortie->getDuree() . ' minutes');

                if ($dateFin < $dateMoinsUnMois) {
                    $etatHistoriseeId = $this->entityManager->getRepository(Etat::class)
                        ->findOneBy(['libelle' => 'Historisée'])->getId();
                    $etatHistorisee = $this->entityManager->getReference(Etat::class, $etatHistoriseeId);
                    $sortie->setEtat($etatHistorisee);
                }
            }
        }
        $this->entityManager->flush();
    }
}