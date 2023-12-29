<?php

namespace App\Controller\Services;

use App\Entity\Administration\Notification;
use App\Entity\References\Direction;
use App\Entity\References\Exploitant;
use App\Entity\References\Exportateur;
use App\Entity\References\ServiceMinef;
use App\Entity\References\Usine;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class Utils
{
    private $mailer;
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public  function uniqidReal($lenght) {
        // uniqid gives 13 chars, but you could adjust it to your needs.
        if (function_exists("random_bytes")) {
            $bytes = random_bytes(ceil($lenght / 2));
        } elseif (function_exists("openssl_random_pseudo_bytes")) {
            $bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
        } else {
            throw new Exception("no cryptographically secure random function available");
        }
        return substr(bin2hex($bytes), 0, $lenght);
    }

    public function envoiNotification(
        ManagerRegistry $registry,
        $sujet,
        $description,
        User $user,
        $from_user,
        $reference,
        $relatedToEntity,
        $relatedToId
    ):void
    {


        $notification = new Notification();

        $created_at = new \DateTime();
        $notification->setCreatedAt($created_at);
        $notification->setCreatedBy($from_user);
        $notification->setLu(false);
        $notification->setSujet($sujet);
        $notification->setDescription($description);
        $notification->setToUser($user);
        $notification->setFromUser($from_user);
        $notification->setReference($reference);
        $notification->setRelatedToEntity($relatedToEntity);
        $notification->setRelatedToId($relatedToId);


        $registry->getManager()->persist($notification);
        $registry->getManager()->flush();

        }

        public function MajRespoExploitant(ManagerRegistry $registry, $id_exploitant, $reponsable, $email_responsable, $contact_responsable, $updated_by){
            $exploitant = $registry->getRepository(Exploitant::class)->find($id_exploitant);
            $exploitant->setPersonneRessource($reponsable);
            $exploitant->setEmailPersonneRessource($email_responsable);
            $exploitant->setMobilePersonneRessource($contact_responsable);

            $dateMAJ = new \DateTime();

            $exploitant->setUpdatedAt($dateMAJ);
            $exploitant->setUpdatedBy($updated_by);

            $registry->getManager()->persist($exploitant);
            $registry->getManager()->flush();
        }

        public function MajRespoIndustriel(ManagerRegistry $registry, $id_usine, $reponsable, $email_responsable, $contact_responsable, $updated_by){
            $industriel = $registry->getRepository(Usine::class)->find($id_usine);
            $industriel->setPersonneRessource($reponsable);
            $industriel->setEmailPersonneRessource($email_responsable);
            $industriel->setMobilePersonneRessource($contact_responsable);

            $dateMAJ = new \DateTime();

            $industriel->setUpdatedAt($dateMAJ);
            $industriel->setUpdatedBy($updated_by);

            $registry->getManager()->persist($industriel);
            $registry->getManager()->flush();
        }

    public function MajRespoExportateur(ManagerRegistry $registry, $id_exportateur, $reponsable, $email_responsable, $contact_responsable, $updated_by){
        $exportateur = $registry->getRepository(Exportateur::class)->find($id_exportateur);
        $exportateur->setPersonneRessource($reponsable);
        $exportateur->setEmailPersonneRessource($email_responsable);
        $exportateur->setMobilePersonneRessource($contact_responsable);

        $dateMAJ = new \DateTime();

        $exportateur->setUpdatedAt($dateMAJ);
        $exportateur->setUpdatedBy($updated_by);

        $registry->getManager()->persist($exportateur);
        $registry->getManager()->flush();
    }

    public function MajRespoServiceMinef(ManagerRegistry $registry, $id_service, $reponsable, $email_responsable, $contact_responsable, $updated_by){
        $servMinef = $registry->getRepository(ServiceMinef::class)->find($id_service);
        $servMinef->setPersonneRessource($reponsable);
        $servMinef->setEmailPersonneRessource($email_responsable);
        $servMinef->setMobilePersonneRessource($contact_responsable);

        $dateMAJ = new \DateTime();

        $servMinef->setUpdatedAt($dateMAJ);
        $servMinef->setUpdatedBy($updated_by);

        $registry->getManager()->persist($servMinef);
        $registry->getManager()->flush();
    }

    public function MajRespoDirectionMinef(ManagerRegistry $registry, $id_direction, $reponsable, $email_responsable, $contact_responsable, $updated_by){
        $dirMinef = $registry->getRepository(Direction::class)->find($id_direction);

        $dirMinef->setPersonneRessource($reponsable);
        $dirMinef->setEmailPersonneRessource($email_responsable);
        $dirMinef->setMobilePersonneRessource($contact_responsable);

        $dateMAJ = new \DateTime();

        $dirMinef->setUpdatedAt($dateMAJ);
        $dirMinef->setUpdatedBy($updated_by);

        $registry->getManager()->persist($dirMinef);
        $registry->getManager()->flush();
    }


    public function sendEmail($to, $subject, $message){
        $email = (new Email())
            ->from('Infos SNVLT <snvlt@system2is.com>')
            ->to($to)
            ->subject($subject)
            ->html($message);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            $e->getTrace();            // some error prevented the email sending; display an
            // error message or try to resend the message
        }
    }
}