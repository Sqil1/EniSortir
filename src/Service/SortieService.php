<?php

namespace App\Service;

namespace App\Service;

use App\Data\SearchData;
use App\Repository\SortieRepository;
use Symfony\Component\Security\Core\Security;

class SortieService
{
    private SortieRepository $sortieRepository;

    public function __construct(SortieRepository $sortieRepository)
    {
        $this->sortieRepository = $sortieRepository;
    }

    public function utilisateurConnecte(SearchData $data, Security $security): void
    {
        if (is_null($data->utilisateurInscrit)) {
            $currentUser = $security->getUser();
            $idUtilisateurConnecte = $currentUser ? $currentUser->getId() : null;
            $data->utilisateurInscrit = $idUtilisateurConnecte;
        }
    }
    public function organisateurFilter(SearchData $data, Security $security): void
    {
        if (isset($data->isOrganisateur) && $data->isOrganisateur) {
            $currentUser = $security->getUser();
            $organisateurId = $currentUser ? $currentUser->getId() : null;
            $data->organisateur = $organisateurId;
        }
    }
    public function participantFilter(SearchData $data, Security $security): void
    {
        if (isset($data->isInscrit) && $data->isInscrit) {
            $currentUser = $security->getUser();
            $isInscrit = $currentUser ? $currentUser->getId() : null;
            $data->inscrit = $isInscrit;
        }
    }
    public function notParticipantFilter(SearchData $data, Security $security): void
    {
        if (isset($data->isNotInscrit) && $data->isNotInscrit) {
            $currentUser = $security->getUser();
            $isNotInscrit = $currentUser ? $currentUser->getId() : null;
            $data->notInscrit = $isNotInscrit;
        }
    }
}