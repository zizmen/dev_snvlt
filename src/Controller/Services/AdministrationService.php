<?php

namespace App\Controller\Services;

use App\Entity\Admin\Exercice;
use Doctrine\Persistence\ManagerRegistry;

class AdministrationService
{
    public function __construct(private ManagerRegistry $registry)
    {

    }

    public function getAnnee()
    {
        return $this->registry->getRepository(Exercice::class)->findBy(['cloture'=>false], ['id'=>'DESC'])[0];
    }

}