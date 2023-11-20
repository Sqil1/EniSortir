<?php

namespace App\Controller;

use App\Data\SearchData;

use App\Form\SearchForm;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ListeSortieController extends AbstractController
{
    #[Route('/sortie/liste', name: 'liste_sortie')]
    public function liste(SortieRepository $sortieRepository, Request $request, Security $security): Response
    {
        $nombreParticipantsInscrits = $sortieRepository->participantsInscritsCounts();


        $data = new SearchData();
        $form = $this->createForm(SearchForm::class, $data);
        $form->handleRequest($request);

        $utilisateurConnecte = $this->getUser();
        $data->utilisateurInscrit = $utilisateurConnecte->getId();

        //dd($data);

        $sorties = $sortieRepository->findSearch($data);

        return $this->render('sortie/liste.html.twig', [
            'sorties' => $sorties,
            'form' => $form->createView(),
            'nombreParticipantsInscrits' => $nombreParticipantsInscrits,
            'utilisateurConnecte' => $utilisateurConnecte,
        ]);
    }
}
