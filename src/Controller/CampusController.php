<?php

namespace App\Controller;


use App\Repository\CampusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/campus', name: 'campus_')]
class CampusController extends AbstractController
{
    #[Route('/liste', name: 'liste')]
    public function create(CampusRepository $campusRepository): Response
    {

        $campus = $campusRepository->findAll();

        return $this->render('campus/listeCampus.html.twig', [ 'campus' => $campus ]);
    }
}