<?php

namespace App\Controller;

use App\Data\SearchDataVille;
use App\Form\SearchFormVille;
use App\Repository\VilleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ville', name: 'ville_')]
class VilleController extends AbstractController
{
    #[Route('/liste', name: 'liste')]
    public function create(VilleRepository $villeRepository, Request $request): Response
    {

        $data = new SearchDataVille();
        $form = $this->createForm(SearchFormVille::class, $data);
        $form->handleRequest($request);

        $villes = $villeRepository->findSearch($data);

        return $this->render('ville/listeVille.html.twig', [
            'villes' => $villes,
            'form' => $form->createView()
        ]);
    }
}