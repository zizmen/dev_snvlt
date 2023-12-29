<?php

namespace App\Controller;


use App\Entity\Groupe;
use App\Entity\Menu;
use App\Entity\MenuPermission;
use App\Entity\Permission;
use App\Entity\User;
use App\Repository\Administration\NotificationRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\PermissionRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Util\Json;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Contracts\Translation\TranslatorInterface;

class GroupeController extends AbstractController
{

    public function __construct(private TranslatorInterface $translator)
    {

    }

    #[Route('snvlt/admin/security/gr_rules', name: 'ref_groupes')]
    public function listing(GroupeRepository $groupes,
    MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        MenuRepository $menuRepository,
        Groupe $groupe = null,
        Groupe $groupe_encours = null,
        UserRepository $userRepository,
        NotificationRepository $notification,
        ManagerRegistry $doctrine,
        Request $request,
        User $user = null,
        ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

            $titre = $this->translator->trans("Edit Group");

                return $this->render('administration/groupe/index.html.twig', [
                    'ref_groupes' => $groupeRepository->findWithoutAucun(),
                    'menu_groupe'=>$menuRepository,
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'groupe'=>$code_groupe,
                    'titre'=>$titre,
                    'liste_parent'=>$permissions,
                ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }


    #[Route('/snvlt/ref/show_menu_parent', name: 'ref_groupe_menu_parent')]
    public function affiche_parent_menu(MenuRepository $doctrine): Response
    {
        $jsonValues = '' ;
        $liste_parent = $doctrine->findOnlyParent();

        foreach ($liste_parent as $parent){

                $MenuParentArray = json_encode([
                    'id' => $parent->getId(),
                    'nom_menu' => $parent->getNomMenu(),
                    'classname_menyu' => $parent->getClassnameMenu(),
                    'icon_menu' => $parent->getIconMenu(),
                    'parent_menu' => $parent->getParentMenu()
                ]);
            $jsonValues .= $MenuParentArray . ",";
            }


        return new Response($jsonValues);
    }

    #[Route('/snvlt/ref/show_menu/{id_groupe?0}', name: 'ref_groupe_show_menu')]
    public function affiche(MenuPermissionRepository $doctrine, $id_groupe): Response
    {


            $menus = $doctrine->showMenuByGroupe($id_groupe);

        $response = array();
        foreach ($menus as $menu) {
            $response[] = array(
                'id' => $menu->getIdPermission(),
                'nommenu' => $menu->getNomMenu(),
                'classname_menyu' => $menu->getClassnameMenu(),
                'code_groupe' => $menu->getCodeGroupe(),
                'icon_menu' => $menu->getIconMenu(),
                'parent_menu' => $menu->getParentMenu()
            );
        }

        return new JsonResponse(json_encode($response));
    }


    #[Route('/snvlt/ref/p_menu/{id_groupe?0}/{id_menu}', name: 'ref_groupe_persist_menu')]
    public function addPermission(ManagerRegistry $registry,
                                  Permission $menuPermission = null,
                                  $id_groupe,
                                  $id_menu,
                                  Request $request,
                                  GroupeRepository $groupeRepository,
                                  MenuRepository $menuRepository,
                                    Groupe $groupe = null,
                                    Menu $menu = null,
    ): Response
    {

        $menuPermission = new Permission();
        $groupe = $groupeRepository->find($id_groupe);
        $menu = $menuRepository->find($id_menu);


        $menuPermission->setCodeGroupe($groupe);
        $menuPermission->setCodeMenu($menu);
        $date_en_cours = new \DateTime();

        $menuPermission->setCreatedAt($date_en_cours);
        $menuPermission->setCreatedBy($request->getUser());

        $registry->getManager()->persist($menuPermission);
        $registry->getManager()->flush();

        return $this->redirectToRoute('ref_groupes');
    }

    #[Route('/snvlt/ref/r_menu/{id_permission}', name: 'ref_groupe_remove_menu')]
    public function remPermission(ManagerRegistry $registry,

                                  $id_permission,
                                  Request $request,
                                  PermissionRepository $permissionRepository,
                                  MenuRepository $menuRepository,
                                  Groupe $groupe = null,
                                  Menu $menu = null,
    ): Response
    {
       // $menuPermission = new Permission();
        $menuPermission = $permissionRepository->find($id_permission);


        $registry->getManager()->remove($menuPermission);
        $registry->getManager()->flush();

        return $this->redirectToRoute('ref_groupes');
    }
}
