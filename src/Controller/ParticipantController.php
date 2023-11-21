<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ParticipantController extends AbstractController
{
    #[Route('/participant/edit/{id}', name: 'participant.edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Participant $participant, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->getUser() !== $participant) {
            return $this->redirectToRoute('home');
        }

        $form = $this->createForm(ParticipantType::class, $participant);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $nouveauMotPasse = $form->get('MotPasse')->getData();
            if ($nouveauMotPasse !== null) {
                $hashedPassword = $hasher->hashPassword($participant, $nouveauMotPasse);
                $participant->setMotPasse($hashedPassword);
            }


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


    #[Route('/participant/show/{id}', name: 'participant.show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(Participant $participant): Response
    {
        return $this->render('participant/show.html.twig', [
            'participant' => $participant,
        ]);
    }
}
