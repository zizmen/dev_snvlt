<?php

namespace App\Controller\Administration;

use App\Controller\Services\SendSMS;
use App\Controller\Services\Utils;
use App\Entity\Groupe;
use App\Entity\User;
use App\Events\Administration\AddNotificationEvent;
use App\Form\Administration\ProfileFormType;
use App\Form\Administration\UtilisateurFormType;
use App\Form\References\ForetType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\TypeAutorisationRepository;
use App\Repository\UserRepository;
use App\Security\AppCustomAuthenticator;
use App\Security\EmailVerifier;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UtilisateurController extends AbstractController
{
    private EmailVerifier $emailVerifier;
    private $translator;

    public function __construct(
        private EventDispatcherInterface $dispatcher,
        EmailVerifier $emailVerifier,
        TranslatorInterface $translator,
        private SluggerInterface $slugger,
        private  Utils $utils)
    {
        $this->emailVerifier = $emailVerifier;
        $this->translator = $translator;
    }
    #[Route('snvlt/admin/utilisateurs', name: 'app_utilisateur')]
    public function index(UserRepository $users,
                          MenuRepository $menus,
                          MenuPermissionRepository $permissions,
                          GroupeRepository $groupeRepository,
                          Request $request,
                          UserRepository $userRepository,
                          PaginatorInterface $paginator,
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
            $titre = $this->translator->trans("Structure mamanger");

            $pagination = $paginator->paginate(
                $users->findAll(),
                $request->query->getInt('page', 1),
                10 );

            return $this->render('administration/utilisateur/index.html.twig', [
                'liste_users' => $users->findAll(),
                'liste_menus'=>$menus->findOnlyParent(),
                "all_menus"=>$menus->findAll(),
                'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                'groupe'=>$code_groupe,
                'titre'=>$titre,
                'pagination' => $pagination,
                'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                'liste_parent'=>$permissions
            ]);
        } else {
                return  $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/admin/my_users', name: 'app_my_users')]
    public function my_users(UserRepository $users,
                          MenuRepository $menus,
                          MenuPermissionRepository $permissions,
                          GroupeRepository $groupeRepository,
                             GroupeRepository $myGroups,
                          Request $request,
                             User $user = null,
                          PaginatorInterface $paginator,
                            NotificationRepository $notification
    ): Response
    {
        $session = $request->getSession();
        {
            if(!$request->getSession()->has('user_session')){
                return $this->redirectToRoute('app_login');
            } else {
                if (!$this->isGranted('ADMIN')) {



            $user = $users->find($this->getUser());
            if ($user->getCodeOperateur()->getId() == 2){
                $myUsers = $users->findBy(['codeexploitant'=>$user->getCodeexploitant()],['isResponsable'=>'DESC']);
            }elseif ($user->getCodeOperateur()->getId() == 3){
                $myUsers = $users->findBy(['codeindustriel'=>$user->getCodeindustriel()]);
            }
            elseif ($user->getCodeOperateur()->getId() == 4){
                $myUsers = $users->findBy(['code_exportateur'=>$user->getCodeExportateur()]);
            }
            elseif ($user->getCodeOperateur()->getId() == 5){
                $myUsers = $users->findBy(['code_dr'=>$user->getCodeDr()]);
            }
            elseif ($user->getCodeOperateur()->getId() == 6){
                $myUsers = $users->findBy(['code_ddef'=>$user->getCodeDdef()]);
            }
            elseif ($user->getCodeOperateur()->getId() == 7){
                $myUsers = $users->findBy(['code_cantonnement'=>$user->getCodeCantonnement()]);
            }
            elseif ($user->getCodeOperateur()->getId() == 10){
                $myUsers = $users->findBy(['code_poste_controle'=>$user->getCodePosteControle()]);
            }

            if(!$user->getCodeGroupe()->getParentGroupe()){
                $code_groupe = $user->getCodeGroupe()->getId();
            } else {
                $code_groupe = $user->getCodeGroupe()->getParentGroupe();
            }


            return $this->render('administration/utilisateur/myteam.html.twig', [
                'liste_users' => $myUsers,
                'liste_menus'=>$menus->findOnlyParent(),
                "all_menus"=>$menus->findAll(),
                'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                'groupe'=>$code_groupe,
                'liste_parent'=>$permissions,
                'mygroups'=>$myGroups->findBy(['parent_groupe'=>$code_groupe]),
                'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0)
            ]);
        } else {
                    return $this->redirectToRoute('app_no_permission_user_active');
                }
            }
            }
    }

    #[Route('/snvlt/secauth/lu/{id_user?0}', name: 'secure_lock_user')]
    public function lockUser(ManagerRegistry $registry, User $user = null, $id_user){
        $user = $registry->getManager()->getRepository(User::class)->find($id_user);
        if ($user){
            $user->setActif(false);
            $registry->getManager()->persist($user);
            $registry->getManager()->flush();

            return true;
        }
    }


    #[Route('/snvlt/secauth/ulu/{id_user?0}', name: 'secure_unlock_user')]
    public function unlockUser(ManagerRegistry $registry, User $user = null, $id_user){
        $user = $registry->getManager()->getRepository(User::class)->find($id_user);
        if ($user){
            $user->setActif(true);
            $registry->getManager()->persist($user);
            $registry->getManager()->flush();

            return true;
        }
    }

    #[Route('/snvlt/user/detail/{id_user?0}', name: 'user_details')]
    public function UserDetail(ManagerRegistry $registry, User $user = null, $id_user){
        $user = $registry->getManager()->getRepository(User::class)->find($id_user);
        if ($user){
            $detailUser = json_encode([
                'id' => $user->getId(),
                'nom_prenoms' =>$user->getPrenomsUtilisateur()." ". $user->getNomUtilisateur(),
                'photo' => $user->getPhoto()
            ]);
            return new Response($detailUser);
        }
    }

    #[Route('/snvlt/auth/add-user', name: 'secure_au_user')]
    public function adduser (UserPasswordHasherInterface $userPasswordHasher,
                             UserAuthenticatorInterface $userAuthenticator,
                             AppCustomAuthenticator $authenticator,
                             ManagerRegistry $entityManager,
                             GroupeRepository $groupe,
                             TranslatorInterface $translator,
                             Request $request,
                             MenuRepository $menus,
                             MenuPermissionRepository $permissions,
                             GroupeRepository $groupeRepository,
                             SendSMS $sendSMS,
                            Utils $utils,
                            NotificationRepository $notification){

        $session = $request->getSession();
        if (!$session->has("user_session")){
            $this->addFlash('error',  $this->translator->trans('You must log in first to access SNVLT'));
            return $this->redirectToRoute('app_login');
        } else {
            //$utils = new Utils();
            $code_groupe = $groupeRepository->find(1);
            $user = new User();
            $datecreation = new \DateTimeImmutable();
            $form = $this->createForm(UtilisateurFormType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // encode the plain password
                //dd($form->get('codeindustriel')->getData('id'));
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('mobile')->getData()
                    )
                );
                $roles = ['ROLE_MINEF', 'ROLE_ADMINISTRATIF'];
                if($form->get('codeexploitant')->getData('id')){
                    $roles = ['ROLE_EXPLOITANT'];
                    //dd($form->get('codeexploitant')->getData('yd'));
                    $utils->MajRespoExploitant(
                        $entityManager,
                        $form->get('codeexploitant')->getData('id'),
                        $user->getPrenomsUtilisateur(). " ". $user->getNomUtilisateur(),
                        $user->getEmail(),
                        $user->getMobile(),
                        $user->getPrenomsUtilisateur(). " ". $user->getNomUtilisateur()
                    );

                } elseif ($form->get('codeindustriel')->getData('id')){
                    $roles = ['ROLE_INDUSTRIEL'];
                    $utils->MajRespoIndustriel(
                        $entityManager,
                        $form->get('codeindustriel')->getData('id'),
                        $user->getPrenomsUtilisateur(). " ". $user->getNomUtilisateur(),
                        $user->getEmail(),
                        $user->getMobile(),
                        $user->getPrenomsUtilisateur(). " ". $user->getNomUtilisateur()
                    );

                }elseif ($form->get('code_service')->getData('id') && $form->get('code_direction')->getData('id')){
                    $roles = ['ROLE_MINEF', 'ROLE_ADMINISTRATIF'];
                    $utils->MajRespoServiceMinef(
                        $entityManager,
                        $form->get('code_service')->getData('id'),
                        $user->getPrenomsUtilisateur(). " ". $user->getNomUtilisateur(),
                        $user->getEmail(),
                        $user->getMobile(),
                        $user->getPrenomsUtilisateur(). " ". $user->getNomUtilisateur()
                    );
                }elseif ($form->get('code_exportateur')->getData('id')){
                    $roles = ['EXPORTATEUR'];
                    $utils->MajRespoExportateur(
                        $entityManager,
                        $form->get('code_exportateur')->getData('id'),
                        $user->getPrenomsUtilisateur(). " ". $user->getNomUtilisateur(),
                        $user->getEmail(),
                        $user->getMobile(),
                        $user->getPrenomsUtilisateur(). " ". $user->getNomUtilisateur()
                    );
                }elseif (!$form->get('code_service')->getData('id') && $form->get('code_direction')->getData('id')){
                $roles = ['ROLE_MINEF', 'ROLE_ADMINISTRATIF'];
                    $utils->MajRespoDirectionMinef(
                        $entityManager,
                        $form->get('code_direction')->getData('id'),
                        $user->getPrenomsUtilisateur(). " ". $user->getNomUtilisateur(),
                        $user->getEmail(),
                        $user->getMobile(),
                        $user->getPrenomsUtilisateur(). " ". $user->getNomUtilisateur()
                    );
            }


                $user->setIsVerified(true);
                $user->setIsResponsable(true);
                $user->setActif(true);
                $user->setCreatedBy($this->getUser());
                $user->setUpdatedBy($this->getUser());
                $user->setUpdatedAt($datecreation);
                $user->setCreatedAt($datecreation);
                $code = strtoupper($utils->uniqidReal(4));
                $user->setCodeSms($code);
                $user->setRoles($roles);

                $entityManager->getManager()->persist($user);
                $entityManager->getManager()->flush();
                $texteSMS = $translator->trans("Hi ").$user->getPrenomsUtilisateur()." ". $user->getNomUtilisateur(). $this->translator->trans(" Your account has just been created in SNVLT and your verification code is ").  $code. $this->translator->trans(" Your accesses have been sent to you by email. THANKS.");

                //envoi du SMS à l'utilisateur
                /*$sendSMS->messagerie($user->getMobile(), $texteSMS);*/
                // generate a signed url and email it to the user

                $userEvent = new AddNotificationEvent($user);
                    $this->dispatcher->dispatch($userEvent, AddNotificationEvent::ADD_NOTIFICATION_EVENT);

              /* $this->emailVerifier->sendEmailRespoConfirmation('app_verify_email', $user,
                    (new TemplatedEmail())
                        ->from(new Address('snvlt@system2is.com', 'SNVLT INFOS'))
                        ->to($user->getEmail())
                        ->subject($translator->trans('Please confirm your email'))
                        ->htmlTemplate('registration/confirmation_email.html.twig')
                );*/

                // do anything else you need here, like send an email
                $this->addFlash('succes', $this->translator->trans("The structure manager has just been created"));
                $this->addFlash('succes', $this->translator->trans("The logging company has been updated"));
                return  $this->redirectToRoute("app_utilisateur");
            }

            return $this->render('Administration/utilisateur/add-user.html.twig', [
                'responsableForm' => $form->createView(),
                'liste_menus'=>$menus->findOnlyParent(),
                "all_menus"=>$menus->findAll(),
                'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                'liste_parent'=>$permissions
            ]);
        }
    }

    #[Route('/snvlt/profile/{id_utilisateur?0}', name: 'my_profile')]
    public function myProfile (UserPasswordHasherInterface $userPasswordHasher,
                             UserAuthenticatorInterface $userAuthenticator,
                             AppCustomAuthenticator $authenticator,
                             ManagerRegistry $entityManager,
                             GroupeRepository $groupe,
                             TranslatorInterface $translator,
                             Request $request,
                             MenuRepository $menus,
                             MenuPermissionRepository $permissions,
                             GroupeRepository $groupeRepository,
                             SendSMS $sendSMS,
                             Utils $utils,
                               int $id_utilisateur,
                               UserRepository $userRepository,
                               User $user = null,
                               User $utilisateur = null,
                               NotificationRepository $notification){

        $session = $request->getSession();
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            $user = $userRepository->find($this->getUser());
            $code_groupe = $user->getCodeGroupe()->getId();

            if ($user->getId() == $id_utilisateur)
            {
                $titre = $translator->trans("Edit my profile");
                $utilisateur = $userRepository->find($id_utilisateur);
                $form = $this->createForm(ProfileFormType::class, $user);

                $form->handleRequest($request);

                if ( $form->isSubmitted() && $form->isValid() ){
                    //dd($user);
                    $MajDate = new \DateTimeImmutable();

                    $photo = $form->get('photo')->getData();

                    if ($photo) {$originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                        // this is needed to safely include the file name as part of the URL
                        $safeFilename = $this->slugger->slug($originalFilename);
                        $newFilename = $safeFilename.'-'.uniqid().'.'.$photo->guessExtension();

                        // Move the file to the directory where brochures are stored
                        try {
                            $photo->move(
                                $this->getParameter('users_directory'),
                                $newFilename
                            );
                        } catch (FileException $e) {
                            // ... handle exception if something happens during file upload
                        }

                        // updates the 'brochureFilename' property to store the PDF file name
                        // instead of its contents
                        $user->setPhoto($newFilename);
                    }


                    $user->setCreatedAt($MajDate);
                    $user->setUpdatedBy($user->getNomUtilisateur(). " ". $user->getPrenomsUtilisateur());


                    $manager = $entityManager->getManager();
                    $manager->persist($user);
                    $manager->flush();

                    $this->addFlash('success',$this->translator->trans("Your profile has been edited successfully"));
                    return $this->redirect($request->getUri());
                } else {
                    return $this->render('administration/utilisateur/profile.html.twig', [
                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'form' =>$form->createView(),
                        'titre'=>$titre,
                        'groupe'=>$code_groupe,
                        'liste_parent'=>$permissions,
                        'mes_infos'=>$user,
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0)
                    ]);
                }
            }else {
                return $this->render('exceptions/user-active-pas-de-permissions.html.twig',[
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'liste_parent'=>$permissions,
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0)
                ]);
            }
        }
      }

        #[Route('/snvlt/profile/change/{id_utilisateur}/{id_groupe}', name: 'change_profile')]
        public function changeProfile(
            int $id_utilisateur,
            int $id_groupe,
            Request  $request,
            User $user = null,
            User $currentUser = null,
            Groupe $groupe = null,
            UserRepository $userRepository,
            GroupeRepository $groupeRepository,
            ManagerRegistry $registry
        ):Response
        {
            if(!$request->getSession()->has('user_session')){
                return $this->redirectToRoute('app_login');
            } else {
                if (!$this->isGranted('ADMIN')) {

                    $user = $userRepository->find($id_utilisateur);
                    $currentUser = $userRepository->find($this->getUser());
                    $groupe = $groupeRepository->find($id_groupe);

                    if($user){
                        $user->setCodeGroupe($groupe);
                        $registry->getManager()->persist($user);
                        $registry->getManager()->flush();

                        $this->utils->envoiNotification(
                            $registry,
                            "User Profile",
                            "Hi, Your user profile has been changed by your administrator. You have probabily new interfaces",
                            $user,
                            $currentUser->getId(),
                            "app_notifs",
                            "PROFILE",
                            $user->getId()
                        );

                        return $this->redirectToRoute('app_my_users');
                    }
                } else {
                    return $this->redirectToRoute('app_no_permission_user_active');
                }
            }
        }

    #[Route('snvlt/json_user/{id_user}', name: 'user_json.list')]
    public function affiche_user_infos(
        ManagerRegistry $registry,
        TypeAutorisationRepository $type_autorisations,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        User $profil = null,
        int $id_user,
        Request $request,
    ){
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {


                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $profil = $userRepository->find($id_user);
                if($profil){

                    /*$liste_docs_attribution = array();

                    foreach ($docs_attribution as $doc) {
                        $liste_docs_attribution[] = array(
                            'id' => $doc->getId(), //ID du document issu de la grille légalité
                            'libelle' => $doc->getLibelleDocument()
                        );
                    }*/

                return new JsonResponse(json_encode($profil));
                }else{
                    return new JsonResponse("BAD_USER_ID");
                }
        }
    }
}