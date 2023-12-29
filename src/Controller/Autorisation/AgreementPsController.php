<?php

namespace App\Controller\Autorisation;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AgreementPsController extends AbstractController
{
    #[Route('/agreement/ps', name: 'app_agreement_ps')]
    public function index(): Response
    {
        return $this->render('agreement_ps/index.html.twig', [
            'controller_name' => 'AgreementPsController',
        ]);
    }
}
