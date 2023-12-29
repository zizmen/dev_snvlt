<?php

namespace App\Controller\Administration;

use App\Controller\Services\Utils;
use App\Entity\Administration\FicheProspection;
use App\Entity\References\Exploitant;
use App\Entity\User;
use App\Events\Administration\AddFicheProspectionEvent;
use App\Events\Autorisation\AddAttributionEvent;
use App\Form\Administration\FicheProspectionType;
use App\Form\References\ExploitantType;
use App\Repository\Administration\InventaireForestierRepository;
use App\Repository\Administration\NotificationRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\References\ForetRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use League\Csv\Reader;
use Proxies\__CG__\App\Entity\Autorisation\Attribution;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class InventaireForestierController extends AbstractController
{


    public function __construct(private SluggerInterface $slugger, private EventDispatcherInterface $dispatcher,private TranslatorInterface $translator, private Utils $utils, private ManagerRegistry $registry)
    {
    }

    #[Route('snvlt/admin/prospect/{id_attribution?0}', name: 'app_inventaire')]
    public function index(
        Request                       $request,
        MenuRepository                $menus,
        MenuPermissionRepository      $permissions,
        GroupeRepository              $groupeRepository,
        UserRepository                $userRepository,
        User                          $user = null,
        int                           $id_attribution,
        FicheProspection              $ficheProspection = null,
        Attribution                   $attribution = null,
        ManagerRegistry               $registry,
        NotificationRepository        $notification,
        InventaireForestierRepository $inventaires,
        ForetRepository               $foretRepository): Response
    {

        if (!$request->getSession()->has('user_session')) {
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT')) {
                    $user = $userRepository->find($this->getUser());
                    $code_groupe = $user->getCodeGroupe()->getId();
                    $mes_attributions = [];

                    if ($user->getCodeexploitant()) {
                        $exploitant = $this->registry->getRepository(Exploitant::class)->find($user->getCodeexploitant());
                        $mes_attributions = $this->registry->getRepository(Attribution::class)->findBy(['code_exploitant' => $exploitant]);
                    }

                    $attribution = $registry->getRepository(Attribution::class)->find($id_attribution);

                    /*if ($attribution) {*/
                        $fiche_prospection =new FicheProspection();

                        $form = $this->createForm(FicheProspectionType::class, $fiche_prospection);

                        $form->handleRequest($request);

                        if ($form->isSubmitted() && $form->isValid()) {

                            $createdDate = new \DateTimeImmutable();

                            $fichier = $form->get('fichier')->getData();

                            if ($fichier) {$originalFilename = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);
                                // this is needed to safely include the file name as part of the URL
                                $safeFilename = $this->slugger->slug($originalFilename);
                                $newFilename = $safeFilename.'-'.uniqid().'.'.$fichier->guessExtension();

                                // Move the file to the directory where brochures are stored
                                try {
                                    $fichier->move(
                                        $this->getParameter('prospections_csv_directory'),
                                        $newFilename
                                    );
                                    sleep(4);
                                } catch (FileException $e) {
                                    // ... handle exception if something happens during file upload
                                }

                                // updates the 'brochureFilename' property to store the PDF file name
                                // instead of its contents
                                $fiche_prospection->setFichier($newFilename);
                            }


                            $fiche_prospection->setCreatedAt($createdDate);
                            $fiche_prospection->setCreatedBy($user);


                            $fiche_prospection->setCodeAttribution($attribution);

                            $manager = $registry->getManager();
                            $manager->persist($fiche_prospection);
                            $manager->flush();

                            // Créé l'évènement d'enregistrement du contenu du fichier dans la table ProspectionTem
                            $addProspectionEvent = new AddFicheProspectionEvent($fiche_prospection);

                            //Dispatcher l'evenement
                            $this->dispatcher->dispatch($addProspectionEvent, AddFicheProspectionEvent::ADD_FICHE_PROSPECTION_EVENT);


                            return $this->redirectToRoute("app_inventaire");
                        } else {

                            return $this->render('administration/inventaire_forestier/index.html.twig',
                                [
                                    'liste_menus' => $menus->findOnlyParent(),
                                    "all_menus" => $menus->findAll(),
                                    'menus' => $permissions->findBy(['code_groupe_id' => $code_groupe]),
                                    'mes_notifs' => $notification->findBy(['to_user' => $user, 'lu' => false], [], 5, 0),
                                    'groupe' => $code_groupe,
                                    'mes_attribution' => $mes_attributions,
                                    'liste_parent' => $permissions,
                                    'form'=>$form->createView()
                                ]);
                        }
                    } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }
}
