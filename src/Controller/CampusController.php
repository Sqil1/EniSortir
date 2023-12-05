<?php

namespace App\Controller;


use App\Data\SearchDataCampus;
use App\Entity\Campus;
use App\Entity\Sortie;
use App\Form\CampusType;
use App\Form\SearchFormCampus;
use App\Form\SortieType;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/campus', name: 'campus_')]
class CampusController extends AbstractController
{
    #[Route('/liste', name: 'liste')]
    public function create(CampusRepository $campusRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = new SearchDataCampus();
        $form = $this->createForm(SearchFormCampus::class, $data);
        $form->handleRequest($request);

        $campus = $campusRepository->findSearch($data);

        $addCampus = new Campus();

        $addCampusForm = $this->createForm(CampusType::class, $addCampus);

        $addCampusForm->handleRequest($request);

        if ($addCampusForm->isSubmitted() && $addCampusForm->isValid()) {
            $entityManager->persist($addCampus);
            $entityManager->flush();

            return $this->redirectToRoute('campus_liste');
        }

        return $this->render('campus/listeCampus.html.twig', [
            'campus' => $campus,
            'form' => $form->createView(),
            'addCampusForm' => $addCampusForm->createView(),
            ]);
    }
}