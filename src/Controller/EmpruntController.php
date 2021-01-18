<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Emprunt;
use App\Repository\EmpruntRepository;

class EmpruntController extends AbstractController
{
    /**
     * @Route("/admin/emprunt", name="emprunt")
     */
    public function index(EmpruntRepository $empruntRepository): Response
    {
        return $this->render('emprunt/index.html.twig', [
            'emprunts' => $empruntRepository->findAll()
        ]);
    }
}
