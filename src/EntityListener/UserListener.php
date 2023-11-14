<?php

namespace App\EntityListener;

use App\Entity\Participant;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserListener
{
  private UserPasswordHasherInterface $hasher;

  public function __construct(UserPasswordHasherInterface $hasher)
  {
    $this->hasher = $hasher;
  }
  public function prePersist(Participant $participant)
  {
    $this->encodePassword($participant);
  }
  public function preUpdate(Participant $participant)
  {
    $this->encodePassword($participant);
  }

  public function encodePassword(Participant $participant)
  {
    if ($participant->getPlainPassword() === null) {
      return;
    }
    $participant->setMotPasse(
      $this->hasher->hashPassword(
        $participant,
        $participant->getPlainPassword()
      )
    );
  }
}
