<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Etat;
use Faker\Generator;
use App\Entity\Ville;
use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Participant;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        // Campus
        $campusNames = ['SAINT-HERBLAIN', 'CHARTRES DE BRETAGNE', 'LA ROCHE SUR YON'];

        foreach ($campusNames as $i => $campusName) {
            $campus = new Campus();
            $campus->setNom($campusName);

            $manager->persist($campus);
            // référence pour chaque entité Campus
            $this->addReference('campus_' . $i, $campus);
        }

        // Etat
        $etatNames = ['Créée', 'Ouverte', 'Clôturée', 'Activité en cours', 'passée', 'Annulé'];

        foreach ($etatNames as $i => $etatName) {
            $etat = new Etat();
            $etat->setLibelle($etatName);

            $manager->persist($etat);
            $this->addReference('etat_' . $i, $etat);
        }

        // Villes
        for ($j = 0; $j < 12; $j++) {
            $ville = new Ville();
            $ville->setNom($this->faker->city());
            $ville->setCodePostal($this->faker->postcode());

            $manager->persist($ville);
            $this->addReference('ville_' . $j, $ville);
        }

        // Lieux
        for ($k = 0; $k < 12; $k++) {
            $lieu = new Lieu();
            $lieu->setNom($this->faker->company);
            $lieu->setRue($this->faker->streetAddress);
            $lieu->setLatitude($this->faker->latitude);
            $lieu->setLongitude($this->faker->longitude);
            $lieu->setVille($this->getReference('ville_' . $k));

            $manager->persist($lieu);
            $this->addReference('lieu_' . $k, $lieu);
        }

        // Participants
        $participants = [];
        for ($i = 0; $i < 12; $i++) {
            $participant = new Participant();
            $participant
                ->setNom($this->faker->lastName())
                ->setPrenom($this->faker->firstName())
                ->setPseudo($this->faker->userName())
                ->setTelephone($this->faker->phoneNumber())
                ->setEmail($this->faker->email())
                ->setActif($this->faker->boolean())
                ->setAdministrateur($this->faker->boolean())
                ->setPlainPassword('password');

            $campus = $this->getReference('campus_' . $this->faker->numberBetween(0, 2));
            $participant->setCampus($campus);

            $manager->persist($participant);
            $participants[] = $participant;
        }

        // Sorties
        for ($i = 0; $i < 12; $i++) {
            $sortie = new Sortie();
            $sortie
                ->setNom($this->faker->word())
                ->setDateHeureDebut($this->faker->dateTimeThisYear())
                ->setDuree($this->faker->numberBetween(30, 240))
                ->setDateLimiteInscription($this->faker->dateTimeThisMonth('+2 weeks'))
                ->setNbInscriptionsMax($this->faker->randomDigit())
                ->setInfosSortie($this->faker->sentence())
                ->setLieu($this->getReference('lieu_' . $i))
                ->setCampus($this->getReference('campus_' . $this->faker->numberBetween(0, 2)))
                ->setEtat($this->getReference('etat_' . $this->faker->numberBetween(0, 5)));

            $organisateur = $this->faker->randomElement($participants);
            $sortie->setOrganisateur($organisateur);

            $manager->persist($sortie);
        }

        $manager->flush();
    }
}