<?php

namespace App\Controller\References;


use App\Entity\References\PosteForestier;
use App\Form\References\PosteForestierType;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\References\PosteForestierRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class PosteForestierController extends AbstractController
{
    public function __construct(private TranslatorInterface $translator)
    {
    }
    #[Route('snvlt/ref/poste_f/{id_poste_forestier?0}', name: 'ref_poste_forestiers')]
    public function listing(PosteForestierRepository $poste_forestiers,
    MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        PosteForestier $poste_forestier = null,
        ManagerRegistry $doctrine,
        int $id_poste_forestier,
        Request $request,
        ): Response
    {
/*        $session = $request->getSession();
        if (!$session->has("user_session")){
            return $this->redirectToRoute('app_login');
        } else {*/
            $code_groupe = $groupeRepository->find(1);
            $titre = $this->translator->trans("Edit forester checkpoint");
            $poste_forestier = $poste_forestiers->find($id_poste_forestier);
            //dd($poste_forestier);
            if(!$poste_forestier){
                $new = true;
                $poste_forestier = new PosteForestier();
            }
            $form = $this->createForm(PosteForestierType::class, $poste_forestier);

            $form->handleRequest($request);

            if ( $form->isSubmitted() && $form->isValid() ){


                $manager = $doctrine->getManager();
                $manager->persist($poste_forestier);
                $manager->flush();

                $this->addFlash('success',$this->translator->trans("forester checkpoint has been edited succesfully"));
                return $this->redirectToRoute("ref_poste_forestiers");
            } else {
                return $this->render('references/poste_forestier/index.html.twig', [
                    'liste_poste_forestiers' => $poste_forestiers->findAll(),
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'groupe'=>$code_groupe->getId(),
                    'form' =>$form->createView(),
                    'titre'=>$titre
                ]);
            }
       /* }*/

    }


    #[Route('/edit/ref/poste_f/{id_poste_forestier?0}', name: 'poste_forestier.edit')]
    public function editPosteForestier(
        PosteForestier $poste_forestier = null,
        ManagerRegistry $doctrine,
        Request $request,
        PosteForestierRepository $poste_forestiers,
        MenuPermissionRepository $permissions,
        MenuRepository $menus,
        GroupeRepository $groupeRepository,
        int $id_poste_forestier): Response
    {
        $code_groupe = $groupeRepository->find(1);
        $titre = $this->translator->trans("Edit forester checkpoint");
        $poste_forestier = $poste_forestiers->find($id_poste_forestier);
        //dd($poste_forestier);
        if(!$poste_forestier){
            $new = true;
            $poste_forestier = new PosteForestier();
            $titre = $this->translator->trans("Add forester checkpoint");
        }


            $new = false;
            if(!$poste_forestier){
                $new = true;
                $poste_forestier = new PosteForestier();
            }
            $form = $this->createForm(PosteForestierType::class, $poste_forestier);

            $form->handleRequest($request);

            if ( $form->isSubmitted() && $form->isValid() ){

                $manager = $doctrine->getManager();
                $manager->persist($poste_forestier);
                $manager->flush();

                $this->addFlash('success',$this->translator->trans("forester checkpoint has been edited succesfully"));
                return $this->redirectToRoute("ref_poste_forestiers");
            } else {
                return $this->render('references/poste_forestier/add-poste_forestier.html.twig',[
                    'form' =>$form->createView(),
                    'titre'=>$titre,
                    'liste_poste_forestiers' => $poste_forestiers->findAll(),
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'groupe'=>$code_groupe->getId()
                ]);
            }
        /*}*/
    }

}
