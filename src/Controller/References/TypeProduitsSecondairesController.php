<?php

namespace App\Controller\References;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TypeProduitsSecondairesController extends AbstractController
{
    #[Route('/type/produits/secondaires', name: 'app_type_produits_secondaires')]
    public function index(): Response
    {
        return $this->render('type_produits_secondaires/index.html.twig', [
            'controller_name' => 'TypeProduitsSecondairesController',
        ]);
    }
}
