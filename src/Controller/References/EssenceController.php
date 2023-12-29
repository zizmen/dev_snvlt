<?php

namespace App\Controller\References;


use App\Entity\References\Essence;
use App\Entity\User;
use App\Form\References\EssencesType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\References\EssenceRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class EssenceController extends AbstractController
{

    public function __construct(private TranslatorInterface $translator)
    {

    }


    #[Route('/essence', name: 'ref_essences')]
    public function index(EssenceRepository $essences): Response
    {
        return $this->render('essence/liste.html.twig', [
            'liste_essences' => $essences->findAll(),
        ]);
    }

    #[Route('snvlt/ref/essences/{id_essence?0}', name: 'ref_essences')]
    public function listing(EssenceRepository $essences,
    MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        Essence $essence = null,
        ManagerRegistry $doctrine,
        int $id_essence,
        Request $request,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification): Response
    {

            $code_groupe = $groupeRepository->find(1);
            $titre = $this->translator->trans("Edit Wood species");
            $essence = $essences->find($id_essence);
            //dd($essence);
            if(!$essence){
                $new = true;
                $essence = new Essence();
            }
            $form = $this->createForm(EssencesType::class, $essence);

            $form->handleRequest($request);

            if ( $form->isSubmitted() && $form->isValid() ){


                $manager = $doctrine->getManager();
                $manager->persist($essence);
                $manager->flush();

                $this->addFlash('success',$this->translator->trans("Wood species has been updated successfully"));
                return $this->redirectToRoute("liste_essence");
            } else {
                return $this->render('references/essence/index.html.twig', [
                    'liste_essences' => $essences->findAll(),
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'groupe'=>$code_groupe->getId(),
                    'form' =>$form->createView(),
                    'titre'=>$titre,
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                    'liste_parent'=>$permissions
                ]);
            }
       /* }*/

    }


    #[Route('snvlt/ref/essence/edit/{id_essence?0}', name: 'essence.edit')]
    public function editEssence(
        Essence $essence = null,
        ManagerRegistry $doctrine,
        Request $request,
        EssenceRepository $essences,
        MenuPermissionRepository $permissions,
        MenuRepository $menus,
        GroupeRepository $groupeRepository,
        int $id_essence,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification): Response
    {
        $code_groupe = $groupeRepository->find(1);
        $titre = $this->translator->trans("Edit Wood species");
        $essence = $essences->find($id_essence);
        //dd($essence);
        if(!$essence){
            $new = true;
            $essence = new Essence();
            $titre = $this->translator->trans("Add Wood species");
        }

            $new = false;
            if(!$essence){
                $new = true;
                $essence = new Essence();
            }
            $form = $this->createForm(EssencesType::class, $essence);

            $form->handleRequest($request);

            if ( $form->isSubmitted() && $form->isValid() ){

                $manager = $doctrine->getManager();
                $manager->persist($essence);
                $manager->flush();

                $this->addFlash('success',$this->translator->trans("Wood species has been updated successfully"));
                return $this->redirectToRoute("ref_essences");
            } else {
                return $this->render('references/essence/add-essence.html.twig',[
                    'form' =>$form->createView(),
                    'titre'=>$titre,
                    'liste_essences' => $essences->findAll(),
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'groupe'=>$code_groupe->getId(),
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                    'liste_parent'=>$permissions
                ]);
            }
        /*}*/
    }

    #[Route('/snvlt/ref/edit-essence/{id_essence?0}', name: 'edit-essence')]
    public function affiche(EssenceRepository $doctrine, $id_essence, Essence $essence = null): Response
    {

        $essence = $doctrine->find($id_essence);
        //dd($essence);
        if(!$essence) {
            throw $this->createNotFoundException($this->translator->trans('The requested entity was not found.'));
        }

        $EssenceArray = json_encode([
            'id' => $essence->getId(),
            'code_essence' => $essence->getNumeroEssence(),
            'nom_vernaculaire'=>$essence->getNomVernaculaire(),
            'nom_scientifique' => $essence->getNomScientifique(),
            'categorie' => $essence->getCategorieEssence(),
            'dm_minima' => $essence->getDmMinima(),
            'famille_essence' => $essence->getFamilleEssence(),
            'taxe_abattage'=>$essence->getTaxeAbattage(),
            'taxe_preservation'=>$essence->getTaxePreservation()
        ]);
        return new Response($EssenceArray);
    }

    #[Route('/snvlt/essence/lstjson', name: 'essences.json')]
    public function essencesJson(EssenceRepository $essencesRepo): Response
    {
        $essences = array();
        $liste_essences = $essencesRepo->findAll();
        //dd($liste_essences);
        foreach ($liste_essences as $essence){
            $essences[] = array(
                'essence_id'=>$essence->getId(),
                'nom_vernaculaire'=>$essence->getNomVernaculaire(),
                'code_essence'=>$essence->getNumeroEssence(),
                'dm'=>$essence->getDmMinima()
            );
        }

        return new JsonResponse(json_encode($essences));
    }
}
