<?php

namespace App\Controller;

use App\Entity\Administration\Notification;
use App\Entity\User;
use App\Repository\Administration\NotificationRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class TdbAdminController extends AbstractController
{
    private $translator;
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    #[Route('/snvlt/admin', name: 'app_tdb_admin')]
    public function index(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
       ): Response
    {
        //dd($this->getUser());


       if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
           $user = $userRepository->find($this->getUser());
           $request->getSession()->set('id_session', $user->getId());
       }
       if (!$user->isVerified()){
           return $this->render('exceptions/non_verifie.html.twig');
       } elseif (!$user->getActif()){
           $this->addFlash('erreur',$this->translator->trans('Sorry, Your access has been blocked by your administrator'));
           return $this->redirectToRoute('app_login');
       }
        $code_groupe = $user->getCodeGroupe()->getId();

        return $this->render('tdb_admin/index.html.twig',
        [

            'liste_menus'=>$menus->findOnlyParent(),
            'liste_parent'=>$permissions,
            "all_menus"=>$menus->findAll(),
            'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
            'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],['created_at'=>'DESC'],5,0),
            'groupe'=>$code_groupe
        ]);
    }
}
