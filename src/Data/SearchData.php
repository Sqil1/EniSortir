<?php

namespace App\Data;

use App\Entity\Campus;

class SearchData
{
    public ?string $s = '';

    //objet campus
    public ?Campus $campus = null;

    public ?\DateTime $dateDebut;

    public ?\DateTime $dateFin;

    public ?bool $isInscrit;

    public ?bool $isNotInscrit;

    public ?bool $isOrganisateur;

    public ?bool $isTermine;

    public ?int $utilisateurInscrit = null;


}