<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ParticipantController extends AbstractController
{
    #[Route('/participant/edition/{id}', name: 'participant.edit', methods: ['GET', 'POST'])]
    public function edit(Participant $participant, Request $request, EntityManagerInterface $manager,  UserPasswordHasherInterface $hasher): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->getUser() !== $participant) {
            return $this->redirectToRoute('home');
        }

        $form = $this->createForm(ParticipantType::class,  $participant);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $nouveauMotPasse = $form->get('MotPasse')->getData();
            if ($nouveauMotPasse !== null) {
                $hashedPassword = $hasher->hashPassword($participant, $nouveauMotPasse);
                $participant->setMotPasse($hashedPassword);
            }
            $participant = $form->getData();
            $manager->persist($participant);
            $manager->flush();

            $this->addFlash(
                'success',
                'Vos modifications ont bien été enregistrées.'
            );
        }
        return $this->render('participant/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
