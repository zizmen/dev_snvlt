<?php

namespace App\Controller\References;


use App\Entity\References\Exportateur;
use App\Entity\User;
use App\Form\References\ExportateurType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\DemandeOperateurRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\References\ExportateurRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExportateurController extends AbstractController
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    #[Route('snvlt/ref/exportmin1', name: 'ref_exportateurs')]
    public function listing(ExportateurRepository $exportateurs,
    MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        ManagerRegistry $doctrine,
        Request $request,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification): Response
    {
        //dd($request);
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_MINEF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
                return $this->render('references/exportateur/index.html.twig',
                    [

                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                        'groupe'=>$code_groupe,
                        'exportateurs'=>$exportateurs->findAll()
                    ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }


    #[Route('snvlt/ref/exportmin1/edit/exportateur/{id_exportateur?0}', name: 'exportateur.edit')]
    public function editExportateur(
        Exportateur $exportateur = null,
        ManagerRegistry $doctrine,
        Request $request,
        ExportateurRepository $exportateurs,
        MenuPermissionRepository $permissions,
        MenuRepository $menus,
        GroupeRepository $groupeRepository,
        int $id_exportateur,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_MINEF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();



        $titre = $this->translator->trans("Edit Exporter");
        $exportateur = $exportateurs->find($id_exportateur);

        if(!$exportateur){
            $new = true;
            $exportateur = new Exportateur();
            $titre = $this->translator->trans("Add Exporter");
        }

            $new = false;
            if(!$exportateur){
                $new = true;
                $exportateur = new Exportateur();

            }
            $form = $this->createForm(ExportateurType::class, $exportateur);

            $form->handleRequest($request);

            if ( $form->isSubmitted() && $form->isValid() ){

                $manager = $doctrine->getManager();
                $manager->persist($exportateur);
                $manager->flush();

                $this->addFlash('success',$this->translator->trans("Exporter file has been updated successfully"));
                return $this->redirectToRoute("ref_exportateurs");
            } else {
                return $this->render('references/exportateur/add-exportateur.html.twig',[
                    'form' =>$form->createView(),
                    'titre'=>$titre,
                    'ref_exportateurs' => $exportateurs->findAll(),
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'groupe'=>$code_groupe,
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0)
                ]);
            }
        } else {
            return $this->redirectToRoute('app_no_permission_user_active');
        }
      }

    }

}
