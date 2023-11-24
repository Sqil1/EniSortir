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
        $this->updateClotureeToOuverte();
//        $this->updateOuverteToEnCours();
//        $this->updateEnCoursToPassee();
//
//        $this->updatePasseToHisto();
    }

//    private function updateOuverteToEnCours(): void
//    {
//        $ouverte = $this->entityManager->getRepository(Sortie::class)->findByEtat('Ouverte');
//
//        $dateActuelle = new \DateTime();
//        $etatEnCours = $this->entityManager->getRepository(Etat::class)
//            ->findOneBy(['libelle' => 'Activité en cours'])->getId();
//
//        foreach ($ouverte as $sortie) {
//            if ($sortie->getDateHeureDebut() <= $dateActuelle) {
//                $dateFin = clone $sortie->getDateHeureDebut();
//                $dateFin->modify('+' . $sortie->getDuree() . ' minutes');
//                if ($dateFin >= $dateActuelle) {
//                    $sortie->setEtat($etatEnCours);
//                }
//            }
//        }
//        $this->entityManager->flush();
//    }

//    private function updateEnCoursToPassee(): void
//    {
//        $enCours = $this->entityManager->getRepository(Sortie::class)->findByEtat('Activité en cours');
//        $etatPassee = $this->entityManager->getRepository(Etat::class)
//            ->findOneBy(['libelle' => 'Passée'])->getId();
//        $dateActuelle = new \DateTime();
//
//        foreach ($enCours as $sortie) {
//            $dateFin = clone $sortie->getDateHeureDebut();
//            $dateFin->modify('+' . $sortie->getDuree() . ' minutes');
//            if ($dateFin <= new $dateActuelle) {
//                $sortie->setEtat($etatPassee);
//            }
//        }
//        $this->entityManager->flush();
//    }

    private function updateOuverteToCloturee(SortieRepository $sortieRepository): void
    {
        $sortiesOuvertes = $this->entityManager->getRepository(Sortie::class)->findOuverteToFermee();
        $etatCloture = $this->entityManager->getRepository(Etat::class)
            ->findOneBy(['libelle' => 'Cloturée']);
        $etatOuvert = $this->entityManager->getRepository(Etat::class)
            ->findOneBy(['libelle' => 'Ouverte']);

        foreach ($sortiesOuvertes as $sortie) {
            $nombreParticipantsInscrits = $sortieRepository->participantsInscritsCounts();
            dump($nombreParticipantsInscrits);
            if ($nombreParticipantsInscrits === $sortie->getNbInscriptionsMax()) {
                $sortie->setEtat($etatCloture);
            }
        }
    }
    private function updateClotureeToOuverte(): void
    {
        $sortiesCloturees = $this->entityManager->getRepository(Sortie::class)->findFermeeToOuvert();
        $etatCloture = $this->entityManager->getRepository(Etat::class)
            ->findOneBy(['libelle' => 'Cloturée']);
        $etatOuvert = $this->entityManager->getRepository(Etat::class)
            ->findOneBy(['libelle' => 'Ouverte']);

        foreach ($sortiesCloturees as $sortie) {
            $nombreParticipantsInscrits = count($sortie->getParticipants());
            if ($nombreParticipantsInscrits < $sortie->getNbInscriptionsMax()) {
                $sortie->setEtat($etatOuvert);
                dump($sortie);
            }

        }
    }


//        $ouvertes = $this->entityManager->getRepository(Sortie::class)->findByEtat('Ouverte');
//        $clotures = $this->entityManager->getRepository(Sortie::class)->findByEtat('Clôturée');
//        $etatOuverte = $this->entityManager->getRepository(Etat::class)
//            ->findOneBy(['libelle' => 'Ouverte']);
//        $etatCloture = $this->entityManager->getRepository(Etat::class)
//            ->findOneBy(['libelle' => 'Cloturée']);
//        $dateActuelle = new \DateTime();
//
//        foreach ($ouvertes as $sortie) {
//            $nombreParticipantsInscrits = count($sortie->getParticipants());
//            if ($nombreParticipantsInscrits < $sortie->getNbInscriptionsMax()) {
//                if ($sortie->getDateLimiteInscription() > $dateActuelle) {
//                $sortie->setEtat($etatOuverte);
//                }
//            } else {
//                    $sortie->setEtat($etatCloture);
//            }
//        }
//        foreach ($clotures as $sortieCloture) {
//            $nombreParticipantsInscritsCloture = count($sortieCloture->getParticipants());
//            if ($sortieCloture->getDateHeureDebut() > $dateActuelle && $nombreParticipantsInscritsCloture < $sortieCloture->getNbInscriptionsMax()) {
//
//                $sortieCloture->setEtat($etatOuverte);
//            }
//        }
//        $this->entityManager->flush();


//    private function updatePasseToHisto(): void
//    {
//        $passee = $this->entityManager->getRepository(Sortie::class)->findByEtat('Passée');
//        $annulee = $this->entityManager->getRepository(Sortie::class)->findByEtat('Annulée');
//        $etatHistorisee = $this->entityManager->getRepository(Etat::class)
//            ->findOneBy(['libelle' => 'Historisée']);
//        $dateActuelle = new \DateTime();
//
//        $dateMoinsUnMois = $dateActuelle->sub(new \DateInterval('P1M'));
//
//        foreach ($passee as $sortie) {
//            if ($sortie->getDateHeureDebut() <= $dateActuelle) {
//                $dateFin = clone $sortie->getDateHeureDebut();
//                $dateFin->modify('+' . $sortie->getDuree() . ' minutes');
//                if ($dateFin < $dateMoinsUnMois) {
//                    $sortie->setEtat($etatHistorisee);
//                }
//            }
//        }
//        foreach ($annulee as $sortie) {
//            if ($sortie->getDateHeureDebut() <= $dateActuelle) {
//                $dateFin = clone $sortie->getDateHeureDebut();
//                $dateFin->modify('+' . $sortie->getDuree() . ' minutes');
//
//                if ($dateFin < $dateMoinsUnMois) {
//                    $sortie->setEtat($etatHistorisee);
//                }
//            }
//        }
//        $this->entityManager->flush();
//    }
}