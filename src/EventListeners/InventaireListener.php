<?php

namespace App\EventListeners;


use App\Controller\Services\ImportProspectionService;
use App\Entity\Autorisation\Attribution;
use App\Entity\References\Foret;
use App\Events\Administration\AddFicheProspectionEvent;
use App\Events\Autorisation\AddAttributionEvent;
use App\Events\Autorisation\AddRepriseEvent;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

class InventaireListener
{
    public function __construct( private ImportProspectionService $prospectionService,
        private LoggerInterface $logger)
    {
    }

    public function onFicheProspectionAdd(AddFicheProspectionEvent $event){
        $fichier = $event->getFicheProspection()->getFichier();

        $this->prospectionService->importCsvProspectionTemp($fichier, $event->getFicheProspection());

        $this->logger->info("Fichier Temporaire enregistré");
    }
}