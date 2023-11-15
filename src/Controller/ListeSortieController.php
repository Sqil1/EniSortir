<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\ListeSortiesType;
use App\Repository\CampusRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListeSortieController extends AbstractController
{
    #[Route('/sortie/liste', name: 'liste_sortie')]
    public function liste(Request $request, SortieRepository $sortieRepository, CampusRepository $campusRepository): Response
    {
        $sortie = new Sortie();
        $sortiesForm = $this->createForm(ListeSortiesType::class, $sortie);

        $user = $this->getUser();

        $isParticipant = $request->query->get('isParticipant');
        $sorties = $isParticipant ? $sortieRepository->findParticipations($user) : $sortieRepository->findAll();


        //$sorties = $sortieRepository->findAll();



        $filtreCampus = $request->query->get('campus');

        return $this->render('sortie/liste.html.twig', [
            "sorties" => $sorties,
            "filtreCampus" => $filtreCampus,
            'sortieForm' => $sortiesForm->createView()
        ]);
    }

}
