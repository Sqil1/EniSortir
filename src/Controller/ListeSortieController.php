<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Data\SearchData;

use App\Form\SearchForm;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ListeSortieController extends AbstractController
{
    #[Route('/sortie/liste', name: 'liste_sortie')]
    public function liste(SortieRepository $sortieRepository, Request $request, Security $security)
    {
        $data = new SearchData();
        $form = $this->createForm(SearchForm::class, $data);
        $form->handleRequest($request);

        //Ajout pour les boutons d'actions
        $participant = null;
        $currentUser = $security->getUser();
        if ($currentUser instanceof Participant) {
            $participant = $currentUser;
        }

        if (isset($data->isOrganisateur) && $data->isOrganisateur) {
            $currentUser = $security->getUser();
            $organisateurId = $currentUser ? $currentUser->getId() : null;
            $data->organisateur = $organisateurId;
        }

        $sorties = $sortieRepository->findSearch($data);
        //dd($data);
        return $this->render('sortie/copieliste.html.twig', [
            'sorties' => $sorties,
            'participant' => $participant,
            'form' => $form->createView()
        ]);
    }
}
