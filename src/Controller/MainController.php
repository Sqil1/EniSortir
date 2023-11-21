<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class MainController extends AbstractController
{
    #[Route('/', name: 'main')]
    public function index(Security $security): Response
    {
        // Vérifier si l'utilisateur est connecté
        if ($security->getUser()) {
            return $this->redirectToRoute('sortie_liste');
        } else {
            return $this->redirectToRoute('app_login');
        }
    }
}