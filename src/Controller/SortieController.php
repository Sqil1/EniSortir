<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sortie', name: 'sortie_')]
class SortieController extends AbstractController
{
    #[Route('/', name: 'creer')]
    public function create( Request $request, EntityManagerInterface $entityManager,
                            ParticipantRepository $participantRepository,
                            EtatRepository $etatRepository): Response
    {
        /*récupération d'un participant et un campus - A RECUPERER AVEC LA SESSION*/
        $organisateur = $participantRepository->find(1);

        /*l'état est 'Créée' si validation avec 'Enregistrer', sinon il est 'Ouverte'*/
        //decaler la récupération de l'état après handlerequest ?
        $etat = $etatRepository->find(1);

        $sortie = new Sortie();
        $sortie->setEtat($etat);
        $sortie->setOrganisateur($organisateur);
        $sortie->setCampus($organisateur->getCampus());

        $sortieForm = $this->createForm( SortieType::class, $sortie );

        $sortieForm->get('campus')->setData($organisateur->getCampus()->getNom() );
        $sortieForm->get('dateLimiteInscription')->setData( new \DateTimeImmutable('+2 days') );
        /*MARCHE PAS :
        $sortieForm->setData([
            'campus' => $organisateur->getCampus()->getNom(),
            'dateLimiteInscription' => new \DateTimeImmutable('+2 days')
        ]);*/

        $sortieForm->handleRequest($request);

        if ( $sortieForm->isSubmitted() && $sortieForm->isValid() ) {
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash( 'success', 'La sortie a bien été ajoutée !' );
            return $this->redirectToRoute( 'home' );
        }

        return $this->render('sortie/create.html.twig', [
            'sortieForm' => $sortieForm->createView()
        ]);
    }
}
