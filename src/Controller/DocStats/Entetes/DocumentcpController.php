<?php

namespace App\Controller\DocStats\Entetes;

use App\Entity\Autorisation\Attribution;
use App\Entity\Autorisation\Reprise;
use App\Entity\DocStats\Entetes\Documentcp;
use App\Entity\DocStats\Pages\Pagecp;
use App\Entity\DocStats\Saisie\Lignepagecp;
use App\Entity\References\Cantonnement;
use App\Entity\References\Ddef;
use App\Entity\References\Foret;
use App\Entity\User;
use App\Repository\Administration\NotificationRepository;
use App\Repository\DocStats\Entetes\DocumentcpRepository;
use App\Repository\DocStats\Pages\PagecpRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DocumentcpController extends AbstractController
{
    public function __construct(private ManagerRegistry $m)
    {
    }

    #[Route('/doc/stats/entetes/documentcp', name: 'app_op_doccp')]
    public function index(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DocumentcpRepository $docs_cp,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or  $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_MINEF') )
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                return $this->render('doc_stats/entetes/documentcp/index.html.twig', [
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'groupe'=>$code_groupe,
                    'liste_parent'=>$permissions
                ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }



    }

    #[Route('/snvlt/doccp/op', name: 'app_docs_cp_json')]
    public function my_doc_cp(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DocumentcpRepository $docs_cp,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or  $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_MINEF') )
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $mes_docs_cp = array();
                //------------------------- Filtre les CP par type Opérateur ------------------------------------- //

                //------------------------- Filtre les CP ADMIN ------------------------------------- //
                if($user->getCodeGroupe()->getId() == 1){
                            $documents_cp = $registry->getRepository(Documentcp::class)->findAll();
                            foreach ($documents_cp as $document_cp){
                                $mes_docs_cp[] = array(
                                    'id_document_cp'=>$document_cp->getId(),
                                    'numero_doccp'=>$document_cp->getNumeroDoccp(),
                                    'foret'=>$document_cp->getCodeReprise()->getCodeAttribution()->getCodeForet()->getDenomination(),
                                    'cantonnement'=>$document_cp->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()->getNomCantonnement(),
                                    'dr'=>$document_cp->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()->getCodeDr()->getDenomination(),
                                    'date_delivrance'=>$document_cp->getDelivreDoccp()->format("d m Y"),
                                    'etat'=>$document_cp->isEtat(),
                                    'exploitant'=>$document_cp->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getRaisonSocialeExploitant(),
                                    'code_exploitant'=>$document_cp->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getNumeroExploitant(),
                                    'volume_arbre'=>$this->getVolumeCp($document_cp)
                                );
                            }
                        //------------------------- Filtre les CP DR ------------------------------------- //
                        } else {
                            if ($user->getCodeDr()){
                                //dd($user->getCodeDr());
                                $cantonnements = $registry->getRepository(Cantonnement::class)->findBy(['code_dr'=>$user->getCodeDr()]);
                                foreach ($cantonnements as $cantonnement){
                                    $forets = $registry->getRepository(Foret::class)->findBy(['code_cantonnement'=>$cantonnement]);

                                    foreach ($forets as $foret){
                                        $attributions = $registry->getRepository(Attribution::class)->findBy(['code_foret'=>$foret]);
                                        foreach ($attributions as $attribution){
                                            $reprises = $registry->getRepository(Reprise::class)->findBy(['code_attribution'=>$attribution]);
                                            foreach ($reprises as $reprise){
                                                $documents_cp = $registry->getRepository(Documentcp::class)->findBy(['code_reprise'=>$reprise]);
                                                foreach ($documents_cp as $document_cp){
                                                    $mes_docs_cp[] = array(
                                                        'id_document_cp'=>$document_cp->getId(),
                                                        'numero_doccp'=>$document_cp->getNumeroDoccp(),
                                                        'foret'=>$document_cp->getCodeReprise()->getCodeAttribution()->getCodeForet()->getDenomination(),
                                                        'cantonnement'=>$document_cp->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()->getNomCantonnement(),
                                                        'dr'=>$document_cp->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()->getCodeDr()->getDenomination(),
                                                        'date_delivrance'=>$document_cp->getDelivreDoccp()->format("d m Y"),
                                                        'etat'=>$document_cp->isEtat(),
                                                        'attribution_attribue'=>$document_cp->getCodeReprise()->getCodeAttribution()->isStatut(),
                                                        'reprise_attribue'=>$document_cp->getCodeReprise()->getCodeAttribution()->isReprise(),
                                                        'exploitant'=>$document_cp->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getRaisonSocialeExploitant(),
                                                        'code_exploitant'=>$document_cp->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getNumeroExploitant(),
                                                        'volume_arbre'=>$this->getVolumeCp($document_cp)
                                                    );
                                                }

                                            }
                                        }
                                    }
                                }

                            //------------------------- Filtre les CP DD ------------------------------------- //
                        } elseif ($user->getCodeDdef()){
                                        $cantonnements = $registry->getRepository(Cantonnement::class)->findBy(['code_ddef'=>$user->getCodeDdef()]);
                                        foreach ($cantonnements as $cantonnement){
                                            $forets = $registry->getRepository(Foret::class)->findBy(['code_cantonnement'=>$cantonnement]);
                                            foreach ($forets as $foret){
                                                $attributions = $registry->getRepository(Attribution::class)->findBy(['code_foret'=>$user->getCodeexploitant()]);
                                                foreach ($attributions as $attribution){
                                                    $reprises = $registry->getRepository(Reprise::class)->findBy(['code_attribution'=>$attribution]);
                                                    foreach ($reprises as $reprise){
                                                        $documents_cp = $registry->getRepository(Documentcp::class)->findBy(['code_reprise'=>$reprise]);
                                                        foreach ($documents_cp as $document_cp){
                                                            $mes_docs_cp[] = array(
                                                                'id_document_cp'=>$document_cp->getId(),
                                                                'numero_doccp'=>$document_cp->getNumeroDoccp(),
                                                                'foret'=>$document_cp->getCodeReprise()->getCodeAttribution()->getCodeForet()->getDenomination(),
                                                                'cantonnement'=>$document_cp->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()->getNomCantonnement(),
                                                                'dr'=>$document_cp->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()->getCodeDr()->getDenomination(),
                                                                'date_delivrance'=>$document_cp->getDelivreDoccp()->format("d m Y"),
                                                                'etat'=>$document_cp->isEtat(),
                                                                'attribution_attribue'=>$document_cp->getCodeReprise()->getCodeAttribution()->isStatut(),
                                                                'reprise_attribue'=>$document_cp->getCodeReprise()->getCodeAttribution()->isReprise(),
                                                                'exploitant'=>$document_cp->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getRaisonSocialeExploitant(),
                                                                'code_exploitant'=>$document_cp->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getNumeroExploitant(),
                                                                'volume_arbre'=>$this->getVolumeCp($document_cp)
                                                            );
                                                        }

                                                    }
                                                }
                                            }
                                        }

                    //------------------------- Filtre les CP CANTONNEMENT ------------------------------------- //
                        } elseif ($user->getCodeCantonnement()){
                                    $forets = $registry->getRepository(Foret::class)->findBy(['code_cantonnement'=>$user->getCodeCantonnement()]);

                                    foreach ($forets as $foret){
                                        $attributions = $registry->getRepository(Attribution::class)->findBy(['code_foret'=>$foret]);
                                        foreach ($attributions as $attribution){
                                            $reprises = $registry->getRepository(Reprise::class)->findBy(['code_attribution'=>$attribution]);
                                            foreach ($reprises as $reprise){
                                                $documents_cp = $registry->getRepository(Documentcp::class)->findBy(['code_reprise'=>$reprise]);
                                                foreach ($documents_cp as $document_cp){
                                                    $mes_docs_cp[] = array(
                                                        'id_document_cp'=>$document_cp->getId(),
                                                        'numero_doccp'=>$document_cp->getNumeroDoccp(),
                                                        'foret'=>$document_cp->getCodeReprise()->getCodeAttribution()->getCodeForet()->getDenomination(),
                                                        'cantonnement'=>$document_cp->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()->getNomCantonnement(),
                                                        'dr'=>$document_cp->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()->getCodeDr()->getDenomination(),
                                                        'date_delivrance'=>$document_cp->getDelivreDoccp()->format("d m Y"),
                                                        'etat'=>$document_cp->isEtat(),
                                                        'attribution_attribue'=>$document_cp->getCodeReprise()->getCodeAttribution()->isStatut(),
                                                        'reprise_attribue'=>$document_cp->getCodeReprise()->getCodeAttribution()->isReprise(),
                                                        'exploitant'=>$document_cp->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getRaisonSocialeExploitant(),
                                                        'code_exploitant'=>$document_cp->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getNumeroExploitant(),
                                                        'volume_arbre'=>$this->getVolumeCp($document_cp)
                                                    );
                                                }

                                            }
                                        }
                                    }

                    //------------------------- Filtre les CP POSTE CONTROLE ------------------------------------- //
                  } elseif ($user->getCodePosteControle()){
                    $forets = $registry->getRepository(Foret::class)->findBy(['code_cantonnement'=>$user->getCodePosteControle()->getCodeCantonnement()]);
                    foreach ($forets as $foret){
                        $attributions = $registry->getRepository(Attribution::class)->findBy(['code_foret'=>$user->getCodeexploitant()]);
                        foreach ($attributions as $attribution){
                            $reprises = $registry->getRepository(Reprise::class)->findBy(['code_attribution'=>$attribution]);
                            foreach ($reprises as $reprise){
                                $documents_cp = $registry->getRepository(Documentcp::class)->findBy(['code_reprise'=>$reprise]);
                                foreach ($documents_cp as $document_cp){
                                    $mes_docs_cp[] = array(
                                        'id_document_cp'=>$document_cp->getId(),
                                        'numero_doccp'=>$document_cp->getNumeroDoccp(),
                                        'foret'=>$document_cp->getCodeReprise()->getCodeAttribution()->getCodeForet()->getDenomination(),
                                        'cantonnement'=>$document_cp->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()->getNomCantonnement(),
                                        'dr'=>$document_cp->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()->getCodeDr()->getDenomination(),
                                        'date_delivrance'=>$document_cp->getDelivreDoccp()->format("d m Y"),
                                        'etat'=>$document_cp->isEtat(),
                                        'attribution_attribue'=>$document_cp->getCodeReprise()->getCodeAttribution()->isStatut(),
                                        'reprise_attribue'=>$document_cp->getCodeReprise()->getCodeAttribution()->isReprise(),
                                        'exploitant'=>$document_cp->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getRaisonSocialeExploitant(),
                                        'code_exploitant'=>$document_cp->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getNumeroExploitant(),
                                        'volume_arbre'=>$this->getVolumeCp($document_cp)
                                    );
                                }

                            }
                        }
                    }
                //------------------------- Filtre les CP EXPLOITANT------------------------------------- //
                } elseif ($user->getCodeexploitant()){
                    $attributions = $registry->getRepository(Attribution::class)->findBy(['code_exploitant'=>$user->getCodeexploitant(), 'reprise'=>true, 'statut'=>true]);
                    foreach ($attributions as $attribution){
                        $reprises = $registry->getRepository(Reprise::class)->findBy(['code_attribution'=>$attribution]);

                        foreach ($reprises as $reprise){
                            $documents_cp = $registry->getRepository(Documentcp::class)->findBy(['code_reprise'=>$reprise]);
                            foreach ($documents_cp as $document_cp){
                                $mes_docs_cp[] = array(
                                    'id_document_cp'=>$document_cp->getId(),
                                    'numero_doccp'=>$document_cp->getNumeroDoccp(),
                                    'foret'=>$document_cp->getCodeReprise()->getCodeAttribution()->getCodeForet()->getDenomination(),
                                    'cantonnement'=>$document_cp->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()->getNomCantonnement(),
                                    'dr'=>$document_cp->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()->getCodeDr()->getDenomination(),
                                    'date_delivrance'=>$document_cp->getDelivreDoccp()->format("d m Y"),
                                    'etat'=>$document_cp->isEtat(),
                                    'volume_arbre'=>$this->getVolumeCp($document_cp)
                                );
                            }

                        }

                    }
                }


                }
                return new JsonResponse(json_encode($mes_docs_cp));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }



    }

    #[Route('/snvlt/doccp/op/pages/{id_cp}', name: 'affichage_cp_json')]
    public function affiche_cp(
        Request $request,
        int $id_cp,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DocumentcpRepository $docs_cp,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or  $this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_EXPORTATEUR'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $numerodoc = "";

                $documentcp = $registry->getRepository(Documentcp::class)->find($id_cp);
                if($documentcp){$numerodoc = $documentcp->getNumeroDoccp();}

                return $this->render('doc_stats/entetes/documentcp/affiche_cp.html.twig', [
                    'document_name'=>$documentcp,
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'groupe'=>$code_groupe,
                    'liste_parent'=>$permissions
                ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/doccp/op/pages_cp/{id_cp}', name: 'affichage_pages_cp_json')]
    public function affiche_pages_cp(
        Request $request,
        int $id_cp,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DocumentcpRepository $docs_cp,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or  $this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_EXPORTATEUR'))
            {
                $doc_cp = $docs_cp->find($id_cp);
                if($doc_cp){
                    $pages_cp = $registry->getRepository(Pagecp::class)->findBy(['code_doccp'=>$doc_cp], ['id'=>'ASC']);
                    $my_cp_pages = array();

                    foreach ($pages_cp as $page){
                        $my_cp_pages[] = array(
                            'id_page'=>$page->getId(),
                            'numero_page'=>$page->getNumeroPagecp()
                        );
                    }
                    return  new JsonResponse(json_encode($my_cp_pages));
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/doccp/op/pages_cp/data/{id_page}', name: 'affichage_page_data_cp_json')]
    public function affiche_page_courante(
        Request $request,
        int $id_page,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        PagecpRepository $pages_cp,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or  $this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_EXPORTATEUR'))
            {
                $page_cp = $pages_cp->find($id_page);
                if($page_cp){
                    $my_cp_page = array();
                        $my_cp_page[] = array(
                            'id_page'=>$page_cp->getId(),
                            'numero_page'=>$page_cp->getNumeroPagecp(),
                            'mois'=>$page_cp->getMois(),
                            'annee'=>$page_cp->getAnnee(),
                            'village'=>$page_cp->getVillagePagecp()
                        );

                    return  new JsonResponse(json_encode($my_cp_page));
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/doccp/op/lignes_cp/data/{id_page}', name: 'affichage_ligne_cp_data_cp_json')]
    public function affiche_lignes_cp_courante(
        Request $request,
        int $id_page,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        PagecpRepository $pages_cp,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or  $this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_EXPORTATEUR'))
            {
                $page_cp = $pages_cp->find($id_page);
                if($page_cp){
                    $lignes_cp = $registry->getRepository(Lignepagecp::class)->findBy(['code_pagecp'=>$page_cp]);
                    $my_cp_page = array();
                    foreach ($lignes_cp as $lignecp){
                        $my_cp_page[] = array(
                            'id_ligne'=>$lignecp->getId(),
                            'numero_ligne'=>$lignecp->getNumeroArbrecp(),
                            'essence'=>$lignecp->getNomEssencecp()->getNomVernaculaire(),
                            'x_arbre'=>$lignecp->getXArbrecp(),
                            'y_arbre'=>$lignecp->getYArbrecp(),
                            'zh_arbre'=>$lignecp->getZhArbrecp()->getZone(),
                            'jour'=>$lignecp->getJourAbattage(),
                            'lng_arbre'=>$lignecp->getLongeurArbrecp(),
                            'dm_arbre'=>$lignecp->getDiametreArbrecp(),
                            'cubage_arbre'=>$lignecp->getVolumeArbrecp(),
                            'lng_billea'=>$lignecp->getLongeuraBillecp(),
                            'dm_billea'=>$lignecp->getDiametreaBillecp(),
                            'cubage_billea'=>$lignecp->getVolumeaBillecp(),
                            'lng_billeb'=>$lignecp->getLongeurbBillecp(),
                            'dm_billeb'=>$lignecp->getDiametrebBillecp(),
                            'cubage_billeb'=>$lignecp->getVolumebBillecp(),
                            'lng_billec'=>$lignecp->getLongeurcBillecp(),
                            'dm_billec'=>$lignecp->getDiametrecBillecp(),
                            'cubage_billec'=>$lignecp->getVolumecBillecp(),
                            'a_abandon'=>$lignecp->isAAbandon(),
                            'b_abandon'=>$lignecp->isBAbandon(),
                            'c_abandon'=>$lignecp->isCAbandon(),
                            'fut_abandon'=>$lignecp->isFutAbandon()
                        );
                    }


                    return  new JsonResponse(json_encode($my_cp_page));
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }
    function getVolumeCp(Documentcp $documentcp):float
    {
        $volumecp = 0;
        if($documentcp){
            $pagecp =$this->m->getRepository(Pagecp::class)->findBy(['code_doccp'=>$documentcp]);
            foreach ($pagecp as $page){
                $lignepages = $this->m->getRepository(Lignepagecp::class)->findBy(['code_pagecp'=>$page]);
                foreach ($lignepages as $ligne){
                   $volumecp = $volumecp +  $ligne->getVolumeArbrecp();
                }
            }
            return $volumecp;
        } else {
            return $volumecp;
        }
    }
}
