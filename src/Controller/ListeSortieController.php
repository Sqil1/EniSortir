<?php

namespace App\Controller;

use App\Data\SearchData;

use App\Form\SearchForm;
use App\Repository\SortieRepository;
use App\Service\SortieService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ListeSortieController extends AbstractController
{
    #[Route('/sortie/liste', name: 'liste_sortie')]
    public function liste(SortieService $sortieService, SortieRepository $sortieRepository, Request $request, Security $security): Response
    {
        $nombreParticipantsInscrits = $sortieRepository->participantsInscritsCounts();


        $data = new SearchData();
        $form = $this->createForm(SearchForm::class, $data);
        $form->handleRequest($request);

        $sortieService->organisateurFilter($data, $security);
        $sortieService->participantFilter($data, $security);
        $sortieService->notParticipantFilter($data, $security);
        $sortieService->utilisateurConnecte($data, $security);

        //dd($data);

        $sorties = $sortieRepository->findSearch($data);

        return $this->render('sortie/liste.html.twig', [
            'sorties' => $sorties,
            'form' => $form->createView(),
            'nombreParticipantsInscrits' => $nombreParticipantsInscrits,
            'searchData' => $data,
        ]);
    }
}
