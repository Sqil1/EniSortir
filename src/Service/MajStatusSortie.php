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

        foreach ($ouverte as $sortie) {
            if ($sortie->getDateHeureDebut() <= new \DateTime()) {
                // Ajouter la durée de la sortie à la date de début
                $dateFin = clone $sortie->getDateHeureDebut();
                $dateFin->modify('+' . $sortie->getDuree() . ' minutes');
                // Vérifier si la date de fin est atteinte ou dépassée
                if ($dateFin >= new \DateTime()) {
                    $etatEnCoursId = $this->entityManager->getRepository(Etat::class)
                        ->findOneBy(['libelle' => 'Activité en cours'])->getId();
                    // Récupérer la référence de l'état 'En cours' avec l'id
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

        foreach ($enCours as $sortie) {
            $dateFin = clone $sortie->getDateHeureDebut();
            $dateFin->modify('+' . $sortie->getDuree() . ' minutes');
            if ($dateFin <= new \DateTime()) {
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

        foreach ($ouvertes as $sortie) {
            // Récupérer le nombre de participants inscrits à la sortie
            $nombreParticipantsInscrits = count($sortie->getParticipants());
            // Vérifier si le nombre d'inscriptions maximal est atteint
            if ($nombreParticipantsInscrits >= $sortie->getNbInscriptionsMax()) {
                $etatClotureId = $this->entityManager->getRepository(Etat::class)
                    ->findOneBy(['libelle' => 'Clôturée'])->getId();
                $etatCloture = $this->entityManager->getReference(Etat::class, $etatClotureId);
                $sortie->setEtat($etatCloture);
            } else {
                // Si le nombre d'inscriptions maximal n'est pas atteint,
                // vérifier si la date limite d'inscription est toujours valide
                if ($sortie->getDateLimiteInscription() > new \DateTime()) {
                    $etatOuverteId = $this->entityManager->getRepository(Etat::class)
                        ->findOneBy(['libelle' => 'Ouverte'])->getId();
                    $etatOuverte = $this->entityManager->getReference(Etat::class, $etatOuverteId);
                    $sortie->setEtat($etatOuverte);
                }
            }
        }
        foreach ($clotures as $sortieCloture) {
            // Vérifier si la date de début de la sortie est future
            if ($sortieCloture->getDateHeureDebut() > new \DateTime()) {
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