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
        $participantsInscrits = $sortieService->getParticipantsInscritsCounts();

        $data = new SearchData();
        $form = $this->createForm(SearchForm::class, $data);
        $form->handleRequest($request);

        $sortieService->organisateurFilter($data, $security);

        $sorties = $sortieRepository->findSearch($data);

        return $this->render('sortie/liste.html.twig', [
            'sorties' => $sorties,
            'form' => $form->createView(),
            'participantsInscrits' => $participantsInscrits
        ]);
    }
}
