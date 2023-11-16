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



        if(isset($data->isOrganisateur) && $data->isOrganisateur) {
            // Récupérez l'utilisateur connecté
            $currentUser = $security->getUser();
            // Vérifiez si l'utilisateur est connecté et si oui, récupérez son ID
            $organisateurId = $currentUser ? $currentUser->getId() : null;
            // Définissez la propriété dans l'objet SearchData
            $data->organisateur = $organisateurId;
        }


        // Utilisez l'ID de l'organisateur pour filtrer les sorties
        $sorties = $sortieRepository->findSearch($data);
        //dd($data);
        return $this->render('sortie/liste.html.twig', [
            'sorties' => $sorties,
            'form' => $form->createView()
        ]);
    }














//    public function liste(Request $request, SortieRepository $sortieRepository): Response
//    {
//        $sortie = new Sortie();
//        $sortiesForm = $this->createForm(ListeSortiesType::class, $sortie);
//        $sortiesForm->handleRequest($request);
//        $user = $this->getUser();
//
//
//        if ($sortiesForm->isSubmitted() && $sortiesForm->isValid()) {
//            $formData = $sortiesForm->getData();
//
//            $sorties = $sortieRepository->findByCritere(
//                [
//                'nom' => $formData->getNom(),
//                'campus' => $formData->getCampus(),
//                'dateHeureDebut' => $formData->getDateHeureDebut(),
//                ],
//            $formData->getIsOrganisateur()
//        );
//
//            //dd($formData);
//    } else {
//            $sorties = $sortieRepository->findAll();
//        }
//
//        $filtreCampus = $request->query->get('campus');
//
//        return $this->render('sortie/liste.html.twig', [
//            "sorties" => $sorties,
//            "filtreCampus" => $filtreCampus,
//            'sortieForm' => $sortiesForm->createView(),
//        ]);
//    }
}
