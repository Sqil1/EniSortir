<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\SearchForm;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use App\Service\MajStatusSortie;

#[Route('/sortie', name: 'sortie_')]
class SortieController extends AbstractController
{
    #[Route('/', name: 'creer')]
    public function create(Request               $request, EntityManagerInterface $entityManager,
                           ParticipantRepository $participantRepository,
                           EtatRepository        $etatRepository): Response
    {
        //Récupération d'un participant et un campus
        $organisateur = $this->getUser();

        $sortie = new Sortie();
        $sortie->setOrganisateur($organisateur);
        $sortie->setCampus($organisateur->getCampus());

        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->get('campus')->setData($organisateur->getCampus()->getNom());
        $sortieForm->get('dateHeureDebut')->setData(new \DateTimeImmutable('+2 days'));
        $sortieForm->get('dateLimiteInscription')->setData(new \DateTimeImmutable('+1 days'));

        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {

            //L'état est 'Créée' si validation avec 'Enregistrer', sinon il est 'Ouverte'
            $etatCreee = $etatRepository->findOneBy(["libelle" => "Créée"]);
            $etatOuverte = $etatRepository->findOneBy(["libelle" => "Ouverte"]);
            if ($sortieForm->get('enregistrer')->isClicked()) {
                $sortie->setEtat($etatCreee);
            } elseif ($sortieForm->get('publier')->isClicked()) {
                $sortie->setEtat($etatOuverte);
            } else {
                return $this->render('sortie/create.html.twig', [
                    'sortieForm' => $sortieForm->createView()
                ]);
            }

            //Sauvegarde de la sortie
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash('success', 'La sortie a bien été ajoutée !');
            return $this->redirectToRoute('sortie_liste');
        }

        return $this->render('sortie/create.html.twig', [
            'sortieForm' => $sortieForm->createView()
        ]);
    }

    private $majStatusSortie;

    public function __construct(MajStatusSortie $majStatusSortie)
    {
        $this->majStatusSortie = $majStatusSortie;
    }


    //utilisée par une requête ajax dans create.html.twig
    #[Route('/updateLieu', name: 'updateLieu', methods: ['GET'])]
    public function affichageLieu(Request $request, LieuRepository $lieuRepository): Response
    {

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


    #[Route('/modifier/{id}', name: 'modifier', requirements: ['id' => '\d+'])]
    public function modifierSortie(Request        $request, SortieRepository $sortieRepository, Sortie $sortie,
                                   EtatRepository $etatRepository, EntityManagerInterface $entityManager): Response
    {

        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->get('ville')->setData($sortie->getLieu()->getVille());
        $sortieForm->get('rue')->setData($sortie->getLieu()->getRue());
        $sortieForm->get('codePostal')->setData($sortie->getLieu()->getVille()->getCodePostal());
        $sortieForm->get('latitude')->setData($sortie->getLieu()->getLatitude());
        $sortieForm->get('longitude')->setData($sortie->getLieu()->getLongitude());

        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {

            //L'état est 'Créée' si validation avec 'Enregistrer', sinon il est 'Ouverte'
            $etatCreee = $etatRepository->findOneBy(["libelle" => "Créée"]);
            $etatOuverte = $etatRepository->findOneBy(["libelle" => "Ouverte"]);
            if ($sortieForm->get('enregistrer')->isClicked()) {
                $sortie->setEtat($etatCreee);
            } elseif ($sortieForm->get('publier')->isClicked()) {
                $sortie->setEtat($etatOuverte);
            } else {
                return $this->render('sortie/create.html.twig', [
                    'sortieForm' => $sortieForm->createView()
                ]);
            }

            //Sauvegarde de la sortie modifiée
            $entityManager->flush();
            $this->addFlash('success', 'La sortie a bien été modifiée !');
            return $this->redirectToRoute('sortie_liste');
        }

        return $this->render('sortie/create.html.twig', [
            'sortieForm' => $sortieForm->createView()
        ]);
    }


    #[Route('/supprimer/{id}', name: 'supprimer', requirements: ['id' => '\d+'])]
    public function supprimerSortie(Request $request, Sortie $sortie, EntityManagerInterface $entityManager): Response
    {

        $entityManager->remove($sortie);
        $entityManager->flush();
        $this->addFlash('success', 'La sortie a bien été supprimée !');
        return $this->redirectToRoute('sortie_liste');

    }


    #[Route('/liste', name: 'liste')]
    public function liste(SortieRepository $sortieRepository, Request $request, Security $security, MajStatusSortie $dateFin): Response
    {
        $this->majStatusSortie->updateSortieStates();

        $nombreParticipantsInscrits = $sortieRepository->participantsInscritsCounts();

        $data = new SearchData();
        $form = $this->createForm(SearchForm::class, $data);
        $form->handleRequest($request);

        $utilisateurConnecte = $this->getUser();
        $data->utilisateurInscrit = $utilisateurConnecte->getId();

        $sorties = $sortieRepository->findSearch($data);

        // Utilisez JsonResponse pour retourner les résultats en JSON

        return $this->render('sortie/liste.html.twig', [
            'sorties' => $sorties,
            'form' => $form->createView(),
            'nombreParticipantsInscrits' => $nombreParticipantsInscrits,
            'utilisateurConnecte' => $utilisateurConnecte,
        ]);
    }

    #[Route('/show/{id}', name: 'detail')]
    public function show(int $id, SortieRepository $sortieRepository): Response
    {
        $sortie = $sortieRepository->find($id);
        $participant = $this->getUser();

        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie,
            'participant' => $participant,
        ]);
    }

    #[Route('/inscription/{id}', name: 'inscription')]
    public function inscription(Sortie $sortie, EntityManagerInterface $manager, EtatRepository $etat): RedirectResponse
    {
        $participant = $this->getUser();
        $dateActuelle = new \DateTime('midnight');
        $etatOuverte = $etat->findOneBy(['libelle' => 'Ouverte']);
        if (
            !$etatOuverte ||
            $sortie->getEtat() !== $etatOuverte ||
            $dateActuelle > $sortie->getDateLimiteInscription() ||
            $sortie->getParticipants()->count() >= $sortie->getNbInscriptionsMax()
        ) {

            return $this->redirectToRoute('sortie_liste');
        }

        $sortie->addParticipant($participant);
        $this->addFlash(
            'success',
            'Vous vous êtes inscrit à la sortie.'
        );

        $manager->flush();
        return $this->redirectToRoute('sortie_liste', ['id' => $sortie->getId()]);
    }

    #[Route('/desistement/{id}', name: 'desistement')]
    public function desister(Sortie $sortie, EntityManagerInterface $manager): Response
    {
        $participant = $this->getUser();

        $now = new \DateTime();

        if ($sortie->getDateHeureDebut() > $now && ($sortie->getEtat()->getLibelle() === 'Ouverte' || $sortie->getEtat()->getLibelle() === 'Clôturée')) {
            $sortie->removeParticipant($participant);
            $manager->flush();

            $this->addFlash(
                'success',
                'Vous vous êtes désisté de la sortie avec succès.'
            );
        } else {
            $this->addFlash(
                'warning',
                'Vous ne pouvez pas vous désister de cette sortie.'
            );
        }

        return $this->redirectToRoute('sortie_liste', ['id' => $sortie->getId()]);
    }

}
