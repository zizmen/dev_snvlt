<?php

namespace App\Controller\Autorisation;

use App\Entity\Admin\Option;
use App\Entity\Autorisation\Attribution;
use App\Entity\Autorisation\Reprise;
use App\Entity\References\DocumentOperateur;
use App\Entity\References\Exploitant;
use App\Entity\References\TypeOperateur;
use App\Entity\User;
use App\Events\Autorisation\AddAttributionEvent;
use App\Events\Autorisation\AddRepriseEvent;
use App\Form\Autorisation\AttributionType;
use App\Form\Autorisation\RepriseType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\Autorisations\AttributionRepository;
use App\Repository\Autorisations\RepriseRepository;
use App\Repository\DocumentOperateurRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\TypeAutorisationRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RepriseController extends AbstractController
{
    public function __construct(private EventDispatcherInterface $dispatcher, private TranslatorInterface $translator)
    {}


    #[Route('/autorisation/reprises', name: 'app_reprises')]
    public function index(RepriseRepository $repriseRepository,
                          MenuRepository $menus,
                          MenuPermissionRepository $permissions,
                          GroupeRepository $groupeRepository,
                          Request $request,
                          UserRepository $userRepository,
                          User $user = null,
                          NotificationRepository $notification
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
                $titre = $this->translator->trans("Add an Authorisation");


                return $this->render('autorisation/reprise/index.html.twig', [
                    'reprises' => $repriseRepository->findAll(),
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'groupe'=>$code_groupe,
                    'titre'=>$titre,
                    'liste_parent'=>$permissions
                ]);
            } else {
                return  $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/autorisation/ra/{id_reprise?0}', name: 'reprise.edit')]
    public function editReprise(
        Reprise $reprise = null,
        ManagerRegistry $doctrine,
        Request $request,
        RepriseRepository $reprises,
        MenuPermissionRepository $permissions,
        MenuRepository $menus,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        int $id_reprise,
        TypeAutorisationRepository $type_autorisations,
        NotificationRepository $notification): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $date_creation = new \DateTimeImmutable();

                $titre = $this->translator->trans("Edit an authorization");
                $reprise = $reprises->find($id_reprise);
                //dd($ddef);
                if(!$reprise){
                    $new = true;
                    $reprise = new Reprise();
                    $titre = $this->translator->trans("Add authorization");

                    $reprise->setCreatedAt($date_creation);
                    $reprise->setCreatedBy($this->getUser());
                }



                $form = $this->createForm(RepriseType::class, $reprise);

                $form->handleRequest($request);

                if ( $form->isSubmitted() && $form->isValid() ){


                    $reprise->setCreatedAt($date_creation);
                    $reprise->setCreatedBy($this->getUser());

                    /*$attribution = $form->getData('codeAttribution');
                    $attribution*/

                    $option = $doctrine->getRepository(Option::class)->findBy(['name'=>'autorisations_validation'])[0];

                    if($option->getValue() == "1"){
                        $reprise->setValidationDocument(true);
                    }else{
                        $reprise->setValidationDocument(false);
                    }


                    $manager = $doctrine->getManager();
                    $manager->persist($reprise);
                    $manager->flush();


                    //Crer l'evenement pour modifier la valeur REPRISE dans la table ATTRIBUTION à true
                    $addRepriseEvent = new AddRepriseEvent($reprise);

                    //Dispatcher l'evenement
                    $this->dispatcher->dispatch($addRepriseEvent, AddRepriseEvent::ADD_REPRISE_EVENT);


                    $this->addFlash('success',$this->translator->trans("The resumption of annual activities has been successfully updated"));



                    return $this->redirectToRoute("app_reprises");
                } else {
                    return $this->render('autorisation/reprise/add-reprise.html.twig',[
                        'form' =>$form->createView(),
                        'titre'=>$titre,
                        'liste_ddefs' => $reprises->findAll(),
                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'groupe'=>$code_groupe,
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                        'liste_parent'=>$permissions,
                        'type_autorisations'=>$type_autorisations->find(1)
                    ]);
                }

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }

    #[Route('snvlt/docs_op/reprises/{id_attribution}', name: 'docs_op_reprises.list')]
    public function affiche_docs_operateur(
        int $id_attribution,
        Exploitant $exploitant = null,
        ManagerRegistry $registry,
        TypeAutorisationRepository $type_autorisations,
        DocumentOperateurRepository $doc_operateurRepository,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        Request $request,
    ){
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_ADMIN')) {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
                if($id_attribution){
                    $attribution  = new Attribution();
                    $attribution = $registry->getRepository(Attribution::class)->find($id_attribution);

                    $exploitant = $registry->getRepository(Exploitant::class)->find($attribution->getCodeExploitant()->getId());

                    $typeOperateur = $registry->getRepository(TypeOperateur::class)->find(2);
                    $doc_operateurRepository = $registry->getRepository(DocumentOperateur::class)->findBy(['type_operateur'=>$typeOperateur, 'codeOperateur'=>$exploitant->getId()]);


                    $doc_reprise = $type_autorisations->find(1);
                    $docs_reprise = $doc_reprise->getCodeDocGrille();

                    $liste_docs = array();
                    $recherche_doc = new DocumentOperateur();
                    foreach ($docs_reprise as $doc) {
                        //foreach ($doc_operateurRepository as $doc_op) {
                        $recherche_doc = $registry->getRepository(DocumentOperateur::class)->searchDocOperateur(
                            $typeOperateur,
                            $exploitant->getId(),
                            $doc
                        );
                        //dd($recherche_doc);
                        if ($recherche_doc){
                            $liste_docs[] = array(
                                'id' => $recherche_doc[0]->getId(),
                                'libelle' => $recherche_doc[0]->getCodeDocumentGrille()->getLibelleDocument(),
                                'fichier' => $recherche_doc[0]->getImageName(),
                                'statut' => $recherche_doc[0]->getStatut(), //ID de la grille de légalité
                                'exploitant'=>$exploitant->getRaisonSocialeExploitant()
                            );
                        } else {
                            $liste_docs[] = array(
                                'id' => $doc->getId(),
                                'libelle' => $doc->getLibelleDocument(),
                                'fichier' => '-',
                                'statut' => '-', //ID de la grille de légalité
                                'exploitant'=>$exploitant->getRaisonSocialeExploitant()
                            );
                        }

                        //}

                    }
                    return new JsonResponse(json_encode($liste_docs));

                } else {
                    $response = false;
                    return new JsonResponse(json_encode($response));
                }

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/docs_exp/reprises/', name: 'docs_reprise.list')]
    public function affiche_docs_reprise(
        ManagerRegistry $registry,
        TypeAutorisationRepository $type_autorisations,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        Request $request,
    ){
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            $user = $userRepository->find($this->getUser());
            $code_groupe = $user->getCodeGroupe()->getId();


            $doc_reprise = $type_autorisations->find(1);
            $docs_reprise = $doc_reprise->getCodeDocGrille();

            $liste_docs_reprise = array();

            foreach ($docs_reprise as $doc) {
                $liste_docs_reprise[] = array(
                    'id' => $doc->getId(), //ID du document issu de la grille légalité
                    'libelle' => $doc->getLibelleDocument()
                );
            }
            return new JsonResponse(json_encode($liste_docs_reprise));
        }
    }
}
