<?php

namespace App\Controller;

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

        if (isset($data->isOrganisateur) && $data->isOrganisateur) {
            $currentUser = $security->getUser();
            $organisateurId = $currentUser ? $currentUser->getId() : null;
            $data->organisateur = $organisateurId;
        }

        $sorties = $sortieRepository->findSearch($data);
        //dd($data);
        return $this->render('sortie/liste.html.twig', [
            'sorties' => $sorties,
            'form' => $form->createView()
        ]);
    }
}
