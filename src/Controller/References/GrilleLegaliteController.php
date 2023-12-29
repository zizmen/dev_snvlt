<?php

namespace App\Controller\References;


use App\Entity\References\GrilleLegalite;
use App\Entity\User;
use App\Form\References\GrilleLegaliteType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\References\GrilleLegaliteRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class GrilleLegaliteController extends AbstractController
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    #[Route('snvlt/ref/gl', name: 'ref_grille_legalite')]
    public function listing(GrilleLegaliteRepository $grille_legalites,
    MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        Request $request,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification
        ): Response
    {
        if (!$request->getSession()->has('user_session')) {

            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN')) {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
                $titre = $this->translator->trans("Edit Legality grid");

                return $this->render('references/grille_legalite/index.html.twig', [
                    'liste_grille_legalites' => $grille_legalites->findAll(),
                    'liste_menus' => $menus->findOnlyParent(),
                    "all_menus" => $menus->findAll(),
                    'menus' => $permissions->findBy(['code_groupe_id' => $code_groupe]),
                    'groupe' => $code_groupe,
                    'titre' => $titre,
                    'liste_parent'=>$permissions,
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                ]);

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }


    #[Route('snvlt/ref/edit/gl/{id_grille_legalite?0}', name: 'grille_legalite.edit')]
    public function editGrilleLegalite(
        GrilleLegalite $grille_legalite = null,
        ManagerRegistry $doctrine,
        Request $request,
        GrilleLegaliteRepository $grille_legalites,
        MenuPermissionRepository $permissions,
        MenuRepository $menus,
        GroupeRepository $groupeRepository,
        int $id_grille_legalite,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification): Response
    {
        if(!$request->getSession()->has('user_session')){

            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN')) {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
                $date_creation = new \DateTimeImmutable();
                $code_groupe = $groupeRepository->find(1);
                $titre = $this->translator->trans("Edit Document template");
                $grille_legalite = $grille_legalites->find($id_grille_legalite);
                //dd($grille_legalite);
                if (!$grille_legalite) {
                    $new = true;
                    $grille_legalite = new GrilleLegalite();
                    $titre = $this->translator->trans("Add Document template");

                    $grille_legalite->setCreatedAt($date_creation);
                    $grille_legalite->setCreatedBy($this->getUser());
                }

                $new = false;
                if (!$grille_legalite) {
                    $new = true;
                    $grille_legalite = new GrilleLegalite();
                }
                $form = $this->createForm(GrilleLegaliteType::class, $grille_legalite);

                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {

                    $grille_legalite->setUpdatedAt($date_creation);
                    $grille_legalite->setUpdatedBy($this->getUser());

                    $manager = $doctrine->getManager();
                    $manager->persist($grille_legalite);
                    $manager->flush();

                    $this->addFlash('success', $this->translator->trans("Document template has been edited successfully"));
                    return $this->redirectToRoute("ref_grille_legalites");
                } else {
                    return $this->render('references/grille_legalite/add-grille_legalite.html.twig', [
                        'form' => $form->createView(),
                        'titre' => $titre,
                        'liste_grille_legalites' => $grille_legalites->findAll(),
                        'liste_menus' => $menus->findOnlyParent(),
                        "all_menus" => $menus->findAll(),
                        'menus' => $permissions->findBy(['code_groupe_id' => $code_groupe]),
                        'groupe' => $code_groupe,
                        'liste_parent'=>$permissions,
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    ]);
                }
                /*}*/
            }else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

//    #[Route('snvlt/ref/edit/gl/{id_grille_legalite?0}', name: 'grille_legalite.edit')]
//    public function extract_grille(
//        GrilleLegalite $grille_legalite = null,
//        ManagerRegistry $doctrine,
//        Request $request,
//        GrilleLegaliteRepository $grille_legalites,
//        MenuPermissionRepository $permissions,
//        MenuRepository $menus,
//        GroupeRepository $groupeRepository,
//        UserRepository $userRepository,
//        User $user = null,
//        NotificationRepository $notification): Response
//    {
//        if (!$request->getSession()->has('user_session')) {
//
//            return $this->redirectToRoute('app_login');
//        } else {
//            if ($this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN')) {
//                $user = $userRepository->find($this->getUser());
//                $code_groupe = $user->getCodeGroupe()->getId();
//
//
//            }else {
//                return $this->redirectToRoute('app_no_permission_user_active');
//            }
//        }
//    }

}
