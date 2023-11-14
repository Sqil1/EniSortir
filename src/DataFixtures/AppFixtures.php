<?php

namespace App\DataFixtures;

use Faker\Factory;
use Faker\Generator;
use App\Entity\Participant;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private Generator $faker;


    public function __construct(UserPasswordHasherInterface $hasher)
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
            $participant->setActif($this->faker->boolean());
            $participant->setPlainPassword('password');
            $manager->persist($participant);
        }

        $manager->flush();
    }
}
