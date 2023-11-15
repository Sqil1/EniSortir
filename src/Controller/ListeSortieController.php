<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\ListeSortiesType;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListeSortieController extends AbstractController
{
    #[Route('/sortie/liste', name: 'liste_sortie')]
    public function liste(Request $request, SortieRepository $sortieRepository): Response
    {
        $sortie = new Sortie();
        $sortiesForm = $this->createForm(ListeSortiesType::class, $sortie);
        $sortiesForm->handleRequest($request);

        if ($sortiesForm->isSubmitted() && $sortiesForm->isValid()) {

            $formData = $sortiesForm->getData();

            $sorties = $sortieRepository->findByCriteria([
                'nom' => $formData->getNom(),
                'campus' => $formData->getCampus(),
            ]);
        } else {
            $sorties = $sortieRepository->findAll();
        }

        $filtreCampus = $request->query->get('campus');

        return $this->render('sortie/liste.html.twig', [
            "sorties" => $sorties,
            "filtreCampus" => $filtreCampus,
            'sortieForm' => $sortiesForm->createView()
        ]);
    }
}
