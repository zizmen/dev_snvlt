<?php

namespace App\Controller\References;


use App\Entity\References\Usine;
use App\Entity\User;
use App\Form\References\UsineType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\References\UsineRepository;
use App\Repository\TypeAutorisationRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class UsineController extends AbstractController
{
    public function __construct(private TranslatorInterface $translator)
    {
    }
    #[Route('snvlt/ref/usines/{id_usine?0}', name: 'ref_usines')]
    public function listing(UsineRepository $usines,
                            ManagerRegistry $doctrine,
                            Request $request,
                            TypeAutorisationRepository $autorisations,
                            MenuPermissionRepository $permissions,
                            MenuRepository $menus,
                            GroupeRepository $groupeRepository,
                            int $id_usine,
                            UserRepository $userRepository,
                            User $user = null,
                            NotificationRepository $notification
        ): Response
    {
/*        $session = $request->getSession();
        if (!$session->has("user_session")){
            return $this->redirectToRoute('app_login');
        } else {*/
            $code_groupe = $groupeRepository->find(1);
            $titre = $this->translator->trans("Edit Wood Factory");
            $usine = $usines->find($id_usine);
            //dd($usine);
            if(!$usine){
                $new = true;
                $usine = new Usine();
            }
            $form = $this->createForm(UsineType::class, $usine);

            $form->handleRequest($request);

            if ( $form->isSubmitted() && $form->isValid() ){

                $manager = $doctrine->getManager();
                $manager->persist($usine);
                $manager->flush();

                $this->addFlash('success',"Le Usine  vient dêtre éditée avec succès");
                return $this->redirectToRoute("ref_usines");
            } else {
                return $this->render('references/usine/index.html.twig', [
                    'liste_usines' => $usines->findAll(),
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                    'groupe'=>$code_groupe->getId(),
                    'form' =>$form->createView(),
                    'titre'=>$titre,
                    'liste_parent'=>$permissions
                ]);
            }
       /* }*/

    }


    #[Route('/edit/usine/{id_usine?0}', name: 'usine.edit')]
    public function editUsine(
        Usine $usine = null,
        UsineRepository $usines,ManagerRegistry $doctrine,
        Request $request,
        TypeAutorisationRepository $autorisations,
        MenuPermissionRepository $permissions,
        MenuRepository $menus,
        GroupeRepository $groupeRepository,
        int $id_usine,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification): Response
    {
        $code_groupe = $groupeRepository->find(1);
        $titre = $this->translator->trans("Edit Wood Factory");
        $usine = $usines->find($id_usine);
        //dd($usine);
        if(!$usine){
            $new = true;
            $usine = new Usine();
            $titre = $this->translator->trans("Edit Wood Factory");
        }

            $new = false;
            if(!$usine){
                $new = true;
                $usine = new Usine();
            }
            $form = $this->createForm(UsineType::class, $usine);

            $form->handleRequest($request);

            if ( $form->isSubmitted() && $form->isValid() ){


                $manager = $doctrine->getManager();
                $manager->persist($usine);
                $manager->flush();

                $this->addFlash('success',$this->translator->trans("Wood Factory has been edited successfully"));
                return $this->redirectToRoute("ref_usines");
            } else {
                return $this->render('references/usine/add-usine.html.twig',[
                    'form' =>$form->createView(),
                    'titre'=>$titre,
                    'liste_usines' => $usines->findAll(),
                    'liste_menus'=>$menus->findOnlyParent(),
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'groupe'=>$code_groupe->getId(),
                    'liste_parent'=>$permissions
                ]);
            }
        /*}*/
    }

}
