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

    public function getParticipantsInscritsCounts(): array
    {
        $allSorties = $this->sortieRepository->findAll();
        $participantsInscrits = [];

        foreach ($allSorties as $sortie) {
            $inscritsCount = $this->sortieRepository->findInscritCount($sortie->getId());
            $participantsInscrits[$sortie->getId()] = $inscritsCount;
        }
        return $participantsInscrits;
    }

    public function organisateurFilter(SearchData $data, Security $security): void
    {
        if (isset($data->isOrganisateur) && $data->isOrganisateur) {
            $currentUser = $security->getUser();
            $organisateurId = $currentUser ? $currentUser->getId() : null;
            $data->organisateur = $organisateurId;
        }
    }

}