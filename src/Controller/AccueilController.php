<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\LivreRepository;


class AccueilController extends AbstractController
{
    /**
     * @Route("/", name="accueil")
     */
    public function index(LivreRepository $livreRepository): Response
    {
        $liste_livres = $livreRepository->findAll();
        //dd($liste_livres);

        return $this->render('base.html.twig', [
            'livres' => $liste_livres,
            
        ]);
    }
}
