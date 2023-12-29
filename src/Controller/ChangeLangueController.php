<?php

namespace App\Controller;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ChangeLangueController extends AbstractController
{

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }


    #[Route('/changeLocale', name: 'changeLocale')]
    public function changeLocale(RequestStack $requestStack, Request $request, EntityManagerInterface $em)
    {

        $lang = $request->request->get('lang');
        $user = $this->getUser();
        if($user)
        {
            $user->setLocale($lang);
            $em->persist($user);
            $em->flush();

           // dd($lang);
        }



        $requestStack->getSession()->set('_locale', $lang);
        return new Response("ok");
      
    }
    


}
