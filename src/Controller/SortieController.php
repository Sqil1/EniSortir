<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
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
        /*Récupération d'un participant et un campus - A RECUPERER AVEC LA SESSION*/
        //$organisateur = $this->getUser();
        $organisateur = $participantRepository->find(1);

        $sortie = new Sortie();
        $sortie->setOrganisateur($organisateur);
        $sortie->setCampus($organisateur->getCampus());

        $sortieForm = $this->createForm( SortieType::class, $sortie );
        $sortieForm->get('campus')->setData($organisateur->getCampus()->getNom() );
        $sortieForm->get('dateHeureDebut')->setData( new \DateTimeImmutable('+2 days') );
        $sortieForm->get('dateLimiteInscription')->setData( new \DateTimeImmutable('+1 days') );

        $sortieForm->handleRequest($request);

        if ( $sortieForm->isSubmitted() && $sortieForm->isValid() ) {

            //L'état est 'Créée' si validation avec 'Enregistrer', sinon il est 'Ouverte'
            $etatCréée = $etatRepository->findOneBy([ "libelle" => "Créée" ]);
            $etatOuverte = $etatRepository->findOneBy([ "libelle" => "Ouverte" ]);
            if ( $sortieForm->get('enregistrer')->isClicked() ) {
                $sortie->setEtat($etatCréée);
            } elseif ( $sortieForm->get('publier')->isClicked() ) {
                $sortie->setEtat($etatOuverte);
            } else {
                return $this->render('sortie/create.html.twig', [
                    'sortieForm' => $sortieForm->createView()
                ]);
            }

            //Sauvegarde de la sortie
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash( 'success', 'La sortie a bien été ajoutée !' );
            return $this->redirectToRoute( 'home' );
        }

        return $this->render('sortie/create.html.twig', [
            'sortieForm' => $sortieForm->createView()
        ]);
    }


    //utilisée par une requête ajax dans create.html.twig
    #[Route( '/updateLieu', name: 'updateLieu', methods: ['GET'] )]
    public function affichageLieu( Request $request, LieuRepository $lieuRepository ) : Response {

        $id = $request->query->get('sortie_lieu');
        $lieuChoisi = $lieuRepository->find($id);
        $villeCorrespondante = $lieuChoisi->getVille();

        return $this->json([
            'rue' => $lieuChoisi->getRue(),
            'codePostal' => $villeCorrespondante->getCodePostal(),
            'latitude' => $lieuChoisi->getLatitude(),
            'longitude' => $lieuChoisi->getLongitude()
        ]);

    }

}
