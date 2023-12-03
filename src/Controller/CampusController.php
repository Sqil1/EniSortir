<?php

namespace App\Controller;


use App\Data\SearchDataCampus;
use App\Form\SearchFormCampus;
use App\Repository\CampusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/campus', name: 'campus_')]
class CampusController extends AbstractController
{
    #[Route('/liste', name: 'liste')]
    public function create(CampusRepository $campusRepository, Request $request): Response
    {
        $data = new SearchDataCampus();
        $form = $this->createForm(SearchFormCampus::class, $data);
        $form->handleRequest($request);

        $campus = $campusRepository->findSearch($data);

        return $this->render('campus/listeCampus.html.twig', [
            'campus' => $campus,
            'form' => $form->createView()
            ]);
    }
}