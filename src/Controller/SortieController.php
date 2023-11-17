<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/sortie', name: 'sortie_')]
class SortieController extends AbstractController
{
    #[Route('/', name: 'creer')]
    public function create(): Response
    {
        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        return $this->render('sortie/create.html.twig', [
            'sortieForm' => $sortieForm->createView()
        ]);
    }

    #[Route('/show/{id}', name: 'detail')]
    public function show(int $id, SortieRepository $sortieRepository): Response
    {
        $sortie = $sortieRepository->find($id);
        $participant = $this->getUser();

        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie,
            'participant' => $participant,
        ]);
    }

    #[Route('/inscription/{id}', name: 'inscription')]
    public function inscription(Sortie $sortie, EntityManagerInterface $manager, EtatRepository $etat): RedirectResponse
    {
        $participant = $this->getUser();
        if (!$participant) {
            return $this->redirectToRoute('app_login');
        }

        $etatOuverte = $etat->findOneBy(['libelle' => 'Ouverte']);
        if (
            !$etatOuverte ||
            $sortie->getEtat() !== $etatOuverte ||
            new \DateTime() > $sortie->getDateLimiteInscription() ||
            $sortie->getParticipants()->count() >= $sortie->getNbInscriptionsMax()
        ) {
            return $this->redirectToRoute('home');
        }

        $sortie->addParticipant($participant);

        $manager->flush();
        return $this->redirectToRoute('sortie_detail', ['id' => $sortie->getId()]);
    }

    #[Route('/desistement/{id}', name: 'desistement')]
    public function desister(Sortie $sortie, EntityManagerInterface $manager): Response
    {
        $participant = $this->getUser();

        $now = new \DateTime();

        if (
            $sortie->getDateHeureDebut() > $now &&
            $sortie->getDateLimiteInscription() > $now
        ) {

            $participant = $this->getUser();
            $sortie->removeParticipant($participant);

            $manager->flush();

            return $this->redirectToRoute('sortie_detail', ['id' => $sortie->getId()]);
        } else {

            $this->addFlash(
                'warning',
                'Vous ne pouvez pas vous désister de cette sortie.'
            );

            return $this->redirectToRoute('sortie_detail', ['id' => $sortie->getId()]);
        }
    }
}
