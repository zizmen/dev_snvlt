<?php

namespace App\Controller\References;


use App\Entity\References\DocumentOperateur;
use App\Entity\User;
use App\Events\References\AddDocumentOperateurEvent;
use App\Form\References\DocumentOperateurType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\References\CircuitCommunicationRepository;
use App\Repository\References\DocumentOperateurRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class DocumentOperateurController extends AbstractController
{

    public function __construct(private TranslatorInterface $translator, private EventDispatcherInterface $dispatcher)
    {

    }

    #[Route('snvlt/ref/docop', name: 'ref_document_operateur')]
    public function listing(
        DocumentOperateurRepository $document_operateurs,
        CircuitCommunicationRepository $circuitCommunicationRepository,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        User $user = null,
        UserRepository $userRepository,
        ManagerRegistry $doctrine,
        Request $request,
        NotificationRepository $notification
        ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or  $this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_EXPORTATEUR'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
            $titre = $this->translator->trans("Edit Operator Document");

                $codeoperateur = 0;
                $typeOperateur = $user->getCodeOperateur();
                if($typeOperateur->getId() == 2){
                    $codeoperateur = $user->getCodeexploitant()->getId();
                }elseif($typeOperateur->getId() == 3){
                    $codeoperateur = $user->getCodeindustriel()->getId();
                }elseif($typeOperateur->getId() == 4){
                    $codeoperateur = $user->getCodeExportateur()->getId();
                }

                return $this->render('references/document_operateur/index.html.twig', [
                    'liste_document_operateurs' => $document_operateurs->findBy(['codeOperateur'=>$codeoperateur,'type_operateur'=>$typeOperateur],['created_at'=>'DESC']),
                    'mes_circuits'=>$circuitCommunicationRepository,
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                    'groupe'=>$code_groupe,
                    'titre'=>$titre,
                    'liste_parent'=>$permissions
                ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
            }
        }




    #[Route('snvlt/ref/docoperateur//edit/{id_document_operateur?0}', name: 'document_operateur.edit')]
    public function editDocumentOperateur(
        DocumentOperateur $document_operateur = null,
        ManagerRegistry $doctrine,
        Request $request,
        DocumentOperateurRepository $document_operateurs,
        MenuPermissionRepository $permissions,
        MenuRepository $menus,
        NotificationRepository $notification,
        GroupeRepository $groupeRepository,
        int $id_document_operateur,
        User $user = null,
        UserRepository $userRepository,): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or  $this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_EXPORTATEUR'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();


        $dateMAJ = new \DateTimeImmutable();

        $titre = $this->translator->trans("Edit Operator Document");
        $document_operateur = $document_operateurs->find($id_document_operateur);
        //dd($document_operateur);
        if(!$document_operateur){
            $new = true;
            $document_operateur = new DocumentOperateur();
            $titre = $this->translator->trans("Add Operator Document");
        }

            $new = false;
            if(!$document_operateur){
                $new = true;
                $document_operateur = new DocumentOperateur();
            }
            $form = $this->createForm(DocumentOperateurType::class, $document_operateur);

            $form->handleRequest($request);

            if ( $form->isSubmitted() && $form->isValid() ){
                $document_operateur->setStatut("EN ATTENTE");
                $document_operateur->setCreatedAt($dateMAJ);
                $document_operateur->setCreatedBy($user);
                $document_operateur->setDemandeurId($user);

                //Affectation du type Operateur
                $typeOperateur = $user->getCodeOperateur();
                $document_operateur->setTypeOperateur($typeOperateur);

                //Recherche et affectation du code opérateur
                $codeoperateur = 0;
                if($typeOperateur->getId() == 2){
                    $codeoperateur = $user->getCodeexploitant()->getId();
                }elseif($typeOperateur->getId() == 3){
                    $codeoperateur = $user->getCodeindustriel()->getId();
                }elseif($typeOperateur->getId() == 4){
                    $codeoperateur = $user->getCodeExportateur()->getId();
                }
                $document_operateur->setCodeOperateur($codeoperateur);

                $manager = $doctrine->getManager();
                $manager->persist($document_operateur);
                $manager->flush();

                //Crer l'evenement pour la génération de circuit de validation
                $addDocumentOperateurEvent = new AddDocumentOperateurEvent($document_operateur);

                //Dispatcher l'evenement
                $this->dispatcher->dispatch($addDocumentOperateurEvent, AddDocumentOperateurEvent::ADD_DOCUMENT_OPERATEUR_EVENT);


                $this->addFlash('success',$this->translator->trans("Operator document has been updated successfully"));
                return $this->redirectToRoute("ref_document_operateur");
            } else {
                return $this->render('references/document_operateur/add-document_operateur.html.twig',[
                    'form' =>$form->createView(),
                    'titre'=>$titre,
                    'liste_document_operateurs' => $document_operateurs->findAll(),
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'groupe'=>$code_groupe,
                    'liste_parent'=>$permissions
                ]);
            }
        /*}*/
    } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
    }
    }

}
