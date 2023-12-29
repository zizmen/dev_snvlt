<?php

namespace App\Controller\Administration;

use App\Controller\Services\Utils;
use App\Entity\Administration\DemandeOperateur;
use App\Entity\Administration\DocStatsGen;
use App\Entity\Autorisation\Reprise;
use App\Entity\DocStats\Entetes\Documentbrh;
use App\Entity\DocStats\Entetes\Documentcp;
use App\Entity\DocStats\Pages\Pagebrh;
use App\Entity\DocStats\Pages\Pagecp;
use App\Entity\References\Exploitant;
use App\Entity\References\PageDocGen;
use App\Entity\References\TypeDocumentStatistique;
use App\Entity\References\TypeOperateur;
use App\Entity\User;
use App\Events\DocPages\AddPagesCpEvent;
use App\Events\References\AddDemandeOperateurEvent;
use App\Events\References\AddDocumentOperateurEvent;
use App\Form\Administration\DemandeOperateurType;
use App\Form\Administration\ValidationDemandeType;
use App\Repository\Administration\DocStatsGenRepository;
use App\Repository\Administration\NotificationRepository;
use App\Repository\Administration\StockDocRepository;
use App\Repository\DemandeOperateurRepository;
use App\Repository\DocumentOperateurRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\References\CircuitCommunicationRepository;
use App\Repository\References\ExploitantRepository;
use App\Repository\References\PageDocGenRepository;
use App\Repository\References\TypeDocumentStatistiqueRepository;
use App\Repository\References\TypeOperateurRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Contracts\Translation\TranslatorInterface;
use function MongoDB\BSON\toPHP;

class DemandeOperateurController extends AbstractController
{
    private $translator;

    public function __construct(
        TranslatorInterface $translator,
        private Utils $utils,
        private EventDispatcherInterface $dispatcher,
        private ManagerRegistry $rm,
        private LoggerInterface $logger)
    {
        $this->translator = $translator;
    }

    #[Route('snvlt/demop', name: 'app_demande_operateur')]
    public function index(
        Request $request,
        MenuRepository $menus,
        CircuitCommunicationRepository $circuitCommunicationRepository,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DemandeOperateurRepository $demandes): Response
    {

        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or  $this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_EXPORTATEUR'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $code_structure = 0;
                if($user->getCodeOperateur()->getId() == 2){
                    $code_structure = $user->getCodeexploitant()->getId();
                }elseif($user->getCodeOperateur()->getId() == 3){
                    $code_structure = $user->getCodeindustriel()->getId();
                }elseif($user->getCodeOperateur()->getId() == 4){
                    $code_structure = $user->getCodeExportateur()->getId();
                }
                return $this->render('administration/demande_operateur/index.html.twig',
                    [
                        'mes_circuits'=>$circuitCommunicationRepository,
                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                        'groupe'=>$code_groupe,
                        'mes_demandes'=>$demandes->findBy(['code_operateur'=>$user->getCodeOperateur(), 'code_structure'=>$code_structure],['created_at'=>'DESC']),
                        'liste_parent'=>$permissions
                    ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('snvlt/demop/add', name: 'demande.add')]
    public function editDemandeOperateur(
        ManagerRegistry $doctrine,
        Request $request,
        UserRepository $userRepository,
        User $user = null,
        DemandeOperateurRepository $demandes,
        MenuPermissionRepository $permissions,
        MenuRepository $menus,
        NotificationRepository $notification,
        GroupeRepository $groupeRepository
       /* GroupeRepository $groupeValidation*/
    ): Response
    {

        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or  $this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_EXPORTATEUR'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();



                    return $this->render('administration/demande_operateur/add-demande.html.twig',[
                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                        'groupe'=>$code_groupe,
                        'liste_parent'=>$permissions
                    ]);
                } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('snvlt/ver_demop', name: 'app_validation_demande')]
    public function validation_document(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DemandeOperateurRepository $demandes): Response
    {
        //dd($request);
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_ADMINISTRATIF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
                return $this->render('administration/verification_demandes/index.html.twig',
                    [

                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                        'groupe'=>$code_groupe,
                        'mes_demandes'=>$demandes->findBy(['verification'=>false]),
                        'liste_parent'=>$permissions
                    ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }


    #[Route('snvlt/demop/all', name: 'app_validation_demande_all')]
    public function document_operateur_all(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DemandeOperateurRepository $demandes): Response
    {
        //dd($request);
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_ADMINISTRATIF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
                return $this->render('administration/verification_demandes/all.html.twig',
                    [

                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                        'groupe'=>$code_groupe,
                        'mes_demandes'=>$demandes->findBy([],['verification'=>'ASC','created_at'=>'DESC']),
                        'liste_parent'=>$permissions
                    ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }


    #[Route('snvlt/ver_demop/apply/{id_demande?0}/{id_notif}', name: 'app_validation_demande_validate')]
    public function validate_document(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DemandeOperateurRepository $demandes,
        int $id_demande,
        ManagerRegistry $doctrine,
        DocumentOperateurRepository $operateurRepository): Response
    {
        //dd($request);
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_ADMINISTRATIF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $titre = $this->translator->trans("Validate the request");
                $demande = $demandes->find($id_demande);


                $form = $this->createForm(ValidationDemandeType::class, $demande);

                $form->handleRequest($request);

                if ( $form->isSubmitted() && $form->isValid() ){

                    $dateMAJ  = new \DateTime();
                    $demande->setUpdatedBy($user);
                    $demande->setUpdatedAt($dateMAJ);
                    $demande->setVerification(true);


                    $manager = $doctrine->getManager();
                    $manager->persist($demande);
                    $manager->flush();

                    $this->addFlash('success',$this->translator->trans("The request has just been validated successfully"));
                    return $this->redirectToRoute("app_validation_demande");
                } else {
                    return $this->render('administration/verification_demandes/validation-demande.html.twig',
                        [
                            'form' =>$form->createView(),
                            'titre'=>$titre,
                            'liste_menus'=>$menus->findOnlyParent(),
                            "all_menus"=>$menus->findAll(),
                            'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                            'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                            'groupe'=>$code_groupe,
                            'mes_demandes'=>$demandes->findBy(['verification'=>false]),
                            'liste_parent'=>$permissions,
                            'documents_operateur'=>$operateurRepository->findBy(['codeOperateur'=>$demande->getCodeStructure()])
                        ]);
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('snvlt/ver_demop/delivrance', name: 'app_delivrance_document')]
    public function delivrance_document(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DemandeOperateurRepository $demandes): Response
    {
        //dd($request);
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
                return $this->render('administration/verification_demandes/index.html.twig',
                    [

                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                        'groupe'=>$code_groupe,
                        'mes_demandes'=>$demandes->findBy(['verification'=>false]),
                        'liste_parent'=>$permissions
                    ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('snvlt/docs_op_type/{id_operateur}', name: 'docs_op_type.list')]
    public function docstype_operateur(
        Request $request,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        int $id_operateur,
        TypeOperateur $operateur = null,
        TypeOperateurRepository $operateurRepository,
        ): Response
    {
        //dd($request);
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or  $this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_EXPORTATEUR'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
                $response = array();
                $operateur = $operateurRepository->find($id_operateur);
                if ($operateur){
                    $documentsOperateur = $operateur->getTypeDocumentStatistiques();

                    foreach ($documentsOperateur as $doc){
                        $response[] = array(
                            'id_doc'=>$doc->getId(),
                            'libelle'=>$doc->getAbv()
                        );
                    }

                    return new JsonResponse(json_encode($response));
                } else {
                    return new JsonResponse(json_encode("ERROR! THIS OPERATOR IS NOT RECOGNIZED"));
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('snvlt/reprises_operateurs/{id_exploitant}', name: 'reprises_op.list')]
    public function reprises_operateur(
        Request $request,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        int $id_exploitant,
        Exploitant $exploitant = null,
        ExploitantRepository $exploitantRepository,
    ): Response
    {
        //dd($request);
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or  $this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_EXPORTATEUR'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
                $response = array();
                $exploitant = $exploitantRepository->find($id_exploitant);
                if ($exploitant){
                    $attributions = $exploitant->getAttributions();
                    foreach ($attributions as $attribution){
                        $reprises = $attribution->getReprises();
                        foreach ($reprises as $reprise){
                            $response[] = array(
                                'id_reprise'=>$reprise->getId(),
                                'libelle'=>$reprise->getCodeAttribution()->getCodeForet()->getDenomination()
                            );

                        }
                    }
                    return new JsonResponse(json_encode($response));
                } else {
                    return new JsonResponse(json_encode("ERROR! THIS OPERATOR IS NOT RECOGNIZED"));
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('snvlt/demandes_op/save/{id_type_doc}/{id_reprise?0}/{qte}', name: 'reprises_op.add')]
    public function add_demande_operateur(
        Request $request,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        int $id_type_doc,
        int $qte,
        int $id_reprise,
        Exploitant $exploitant = null,
        ExploitantRepository $exploitantRepository,
        ManagerRegistry $registry,
    ): Response
    {
        //dd($request);
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or  $this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_EXPORTATEUR'))
            {
                if ($id_type_doc && $qte){
                    $user = $userRepository->find($this->getUser());
                    $code_groupe = $user->getCodeGroupe()->getId();


                    $date_creation = new \DateTimeImmutable();

                    $demande = new DemandeOperateur();

                    $demande->setCreatedAt($date_creation);
                    $demande->setCreatedBy($this->getUser());
                    $demande->setStatut('EN ATTENTE');
                    $demande->setTransmission(false);
                    $demande->setVerification(false);
                    $demande->setDelivrance(false);
                    $demande->setDemandeur($user);

                    $demande->setQte($qte);
                    $demande->setDocStat($registry->getRepository(TypeDocumentStatistique::class)->find($id_type_doc));

                    if($user->getCodeOperateur()->getId() == 2){
                        $demande->setCodeStructure($user->getCodeexploitant()->getId());
                        if ($id_reprise){
                            $reprise = $registry->getRepository(Reprise::class)->find($id_reprise);
                            $demande->setCodeReprise($reprise);
                        }

                    } elseif($user->getCodeOperateur()->getId() == 3){
                        $demande->setCodeStructure($user->getCodeindustriel()->getId());

                    }elseif($user->getCodeOperateur()->getId() == 4){
                        $demande->setCodeStructure($user->getCodeExportateur()->getId());
                    }

                    $demande->setCodeOperateur($user->getCodeOperateur());
                    $demande->setDocsGeneres(false);
                    $manager = $registry->getManager();
                    $manager->persist($demande);
                    $manager->flush();


                    //Crer l'evenement pour la génération de circuit de validation
                    $addDemandeperateurEvent = new AddDemandeOperateurEvent($demande);

                    //Dispatcher l'evenement
                    $this->dispatcher->dispatch($addDemandeperateurEvent, AddDemandeOperateurEvent::ADD_DEMANDE_OPERATEUR_EVENT);

                    $this->addFlash('success',$this->translator->trans("The document request was sent successfully"));
                    return $this->redirectToRoute("app_demande_operateur");
                    }


                    $this->addFlash('success',$this->translator->trans("The document request was sent successfully"));
                    return $this->redirectToRoute("app_demande_operateur");
                } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

            }

        }
    #[Route('snvlt/demop/retrieve', name: 'app_demande_retrieve')]
    public function retrieve_documents(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DemandeOperateurRepository $demandes): Response
    {

        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();



                return $this->render('administration/demande_operateur/reception.twig',
                    [
                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                        'groupe'=>$code_groupe,
                        'mes_demandes'=>$demandes->findBy(['docs_generes'=>false, 'statut'=>'APPROUVE'],['created_at'=>'DESC']),
                        'liste_parent'=>$permissions
                    ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('snvlt/demop/stock/{id_demande}', name: 'app_demande_get_docs_stock')]
    public function get_doc_stock(
        Request $request,
        int $id_demande,
        DemandeOperateur $demandeOperateur = null,
        ManagerRegistry $registry,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DemandeOperateurRepository $demandes,
        StockDocRepository $stockDocRepository ): Response
    {

        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $demandeOperateur = $registry->getRepository(DemandeOperateur::class)->find($id_demande);
                $nb_docs = 0;
                $reponse = array();
                if($demandeOperateur){
                    $docs = $stockDocRepository->findBy(['code_type_doc_stat'=>$demandeOperateur->getDocStat()]);

                    foreach ($docs as $doc){
                        $nb_docs = $doc->getDocStatsGens()->count();
                        foreach ($doc->getDocStatsGens() as $doc_detail){
                            $code_reprise = 0;
                            if ($demandeOperateur->getCodeReprise()){
                                $code_reprise = $demandeOperateur->getCodeReprise()->getId();
                            }
                                $reponse[] = array(
                                    'id_doc'=> $doc_detail->getId(),
                                    'numero_doc'=>$doc_detail->getNumeroDoc(),
                                    'nb'=>$nb_docs,
                                    'qte_livree'=>$demandeOperateur->getQteDelivree(),
                                    'qte_demandee'=>$demandeOperateur->getQte(),
                                    'code_reprise'=>$code_reprise,
                                    'type_doc'=>$doc_detail->getCodeTypeDoc()->getCodeTypeDocStat()->getId()
                                );
                            }

                    }

                }
                return new JsonResponse(json_encode($reponse));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }


    #[Route('snvlt/demop_cp/gen_from_stock/{id_stock_doc}/{id_reprise}/{id_demande}', name: 'app_demande_gen_doccp')]
    public function generateDocStats(
        Request $request,
        int $id_stock_doc,
        int $id_reprise,
        int $id_demande,
        ManagerRegistry $registry,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null): Response
    {

        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $doc_stock = $registry->getRepository(DocStatsGen::class)->find($id_stock_doc);

                $reponse = "";

                $type_doc_stock = 0;
                if($doc_stock){
                    $delivre_doc = new \DateTime();
                    $majDoc = new \DateTime();

                    $type_doc_stock = $doc_stock->getCodeTypeDoc()->getCodeTypeDocStat();
                    $doc_generate = null;


                    //Routine pour la génération du CP et ses pages depuis le Stock
                    if ($type_doc_stock->getId() == 1){
                        $doc_generate = new Documentcp();
                        $reprise = $registry->getRepository(Reprise::class)->find($id_reprise);
                        $doc_generate->setCodeReprise($reprise);
                        $doc_generate->setNumeroDoccp($doc_stock->getNumeroDoc());
                        $doc_generate->setDelivreDoccp($delivre_doc);
                        $doc_generate->setExercice(0);
                        $doc_generate->setCreatedAt($majDoc);
                        $doc_generate->setCreatedBy($user);
                        $doc_generate->setTypeDocument($type_doc_stock);
                        $doc_generate->setUniqueDoc($doc_stock->getUniqueDoc());
                        $doc_generate->setCodeGeneration($doc_stock);

                        $registry->getManager()->persist($doc_generate);


                                $registry->getManager()->persist($doc_generate);

                                    //Génétion des pages du document CP

                                    $pages_doc_stock = $registry->getRepository(PageDocGen::class)->findBy(['code_doc_gen'=>$doc_stock]);

                                    foreach ($pages_doc_stock as $page){
                                        $page_cp = new Pagecp();
                                        $page_cp->setUniqueDoc($doc_generate->getUniqueDoc().$page->getNumpage());
                                        $page_cp->setCodeDoccp($doc_generate);
                                        $page_cp->setNumeroPagecp($page->getNumeroPage());
                                        $page_cp->setCreatedAt(new \DateTime());
                                        $page_cp->setCreatedBy($doc_generate->getCreatedBy());
                                        $page_cp->setIndex($page->getNumeroPage());
                                        $page_cp->setFini(false);
                                        $page_cp->setCodeGeneration($page);

                                        $registry->getManager()->persist($page_cp);
                                        //dd($page_cp);

                                        //Mise à jour de la page stock
                                        $page->setAttribue(true);
                                        $registry->getManager()->persist($page);


                                        $this->logger->info($this->translator->trans("Page No ". $page_cp->getNumeroPagecp() . " " . " from document CP No ". $doc_generate->getNumeroDoccp(). " has been created by ". $doc_generate->getCreatedBy()));
                                    }

                                    $registry->getManager()->flush();


                        $reponse = "DOCUMENT CP AND PAGES CP GENERATED";
                        $this->addFlash("success", $this->translator->trans("DOCUMENT CP AND PAGES CP GENERATED"));

                    //Routine pour la génération du BRH et ses pages depuis le Stock
                    } else if ($type_doc_stock->getId() == 2){
                            $doc_generate = new Documentbrh();
                            $reprise = $registry->getRepository(Reprise::class)->find($id_reprise);
                            $doc_generate->setCodeReprise($reprise);
                            $doc_generate->setNumeroDocbrh($doc_stock->getNumeroDoc());
                            $doc_generate->setDelivreDocbrh($delivre_doc);
                            $doc_generate->setExercice(0);
                            $doc_generate->setCreatedAt($majDoc);
                            $doc_generate->setCreatedBy($user);
                            $doc_generate->setTypeDocument($type_doc_stock);
                            $doc_generate->setUniqueDoc($doc_stock->getUniqueDoc());
                            $doc_generate->setCodeGeneration($doc_stock);

                            $registry->getManager()->persist($doc_generate);



                            $registry->getManager()->persist($doc_generate);

                            //Génétion des pages du document BRH

                            $pages_doc_stock = $registry->getRepository(PageDocGen::class)->findBy(['code_doc_gen'=>$doc_stock]);

                            foreach ($pages_doc_stock as $page){
                                $page_brh = new Pagebrh();
                                $page_brh->setUniqueDoc($doc_generate->getUniqueDoc().$page->getNumpage());
                                $page_brh->setCodeDocbrh($doc_generate);
                                $page_brh->setNumeroPagebrh($page->getNumeroPage());
                                $page_brh->setCreatedAt(new \DateTime());
                                $page_brh->setCreatedBy($doc_generate->getCreatedBy());
                                $page_brh->setindex_page($page->getNumeroPage());
                                $page_brh->setFini(false);
                                $page_brh->setCodeGeneration($page);

                                $registry->getManager()->persist($page_brh);
                                //dd($page_cp);

                                //Mise à jour de la page stock
                                $page->setAttribue(true);
                                $registry->getManager()->persist($page);


                                $this->logger->info($this->translator->trans("Page No ". $page_brh->getNumeroPagebrh() . " " . " from document BRH No ". $doc_generate->getNumeroDocbrh(). " has been created by ". $doc_generate->getCreatedBy()));
                            }

                            $registry->getManager()->flush();


                            $reponse = "DOCUMENT BRH AND PAGES BRH GENERATED";
                            $this->addFlash("success", $this->translator->trans("DOCUMENT BRH AND PAGES BRH GENERATED"));


                        }

                    // Mise à jour du document stock
                    $doc_stock->setAttribue(true);
                    $registry->getManager()->persist($doc_stock);

                    //Mise à jour de la demande
                    $demande = new DemandeOperateur();
                    $demande = $registry->getRepository(DemandeOperateur::class)->find($id_demande);
                    $demande->setDocsGeneres(true);

                    $registry->getManager()->persist($demande);
                    $registry->getManager()->persist($doc_stock);
                    $registry->getManager()->flush();

                }

                return $this->redirectToRoute('app_demande_retrieve');

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }
}
