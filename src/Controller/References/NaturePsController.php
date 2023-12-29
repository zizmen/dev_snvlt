<?php

namespace App\Controller\References;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NaturePsController extends AbstractController
{
    #[Route('/nature/ps', name: 'app_nature_ps')]
    public function index(): Response
    {
        return $this->render('nature_ps/index.html.twig', [
            'controller_name' => 'NaturePsController',
        ]);
    }
}
