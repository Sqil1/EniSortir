<?php

namespace App\DataFixtures;

use App\Entity\Participant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class AppFixtures extends Fixture
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 12; $i++) {
            $participant = new Participant();
            $participant->setNom($this->faker->lastName());
            $participant->setPrenom($this->faker->firstName());
            $participant->setPseudo($this->faker->userName());
            $participant->setTelephone($this->faker->phoneNumber());
            $participant->setEmail($this->faker->email());
            $participant->setPassword('password');
            $participant->setRoles(['ROLE_USER']);
            $participant->setIsAdmin($this->faker->boolean());
            $participant->setIsActive($this->faker->boolean());

            $manager->persist($participant);
        }

        $manager->flush();
    }
}
