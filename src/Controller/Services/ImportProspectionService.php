<?php

namespace App\Controller\Services;

use App\Entity\Administration\FicheProspection;
use App\Entity\Administration\ProspectionTemp;
use App\Repository\Administration\ProspectionTempRepository;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportProspectionService
{
    public function __construct(
        private LoggerInterface $logger,
        private ProspectionTempRepository $prospectionsTemp,
        private EntityManagerInterface $em)
    {

    }
    public function importCsvProspectionTemp(string $fichier, FicheProspection $code_prospection):void
    {


        $prospections = $this->readCsvFile($fichier);

        foreach ($prospections as $ArrayProspection){
            $prospection = $this->UpdateOrCreateInventaireTemp($ArrayProspection, $code_prospection);
            $this->em->persist($prospection);
        }
        $this->em->flush();
        $this->logger->info("Inventory data uploaded to Prospection Temp");
    }

    public function importCSV(SymfonyStyle $io):void
{
    $io->title('Importation du fichier prospection');
    $fichier = 'test-657c73efe0778.csv';
    $prospections = $this->readCsvFile($fichier);

    $io->progressStart(count($prospections));

    foreach ($prospections as $ArrayProspection){
        $io->progressAdvance();
        $prospection = $this->UpdateOrCreateInventaireTemp($ArrayProspection);
        $this->em->persist($prospection);
    }
    $this->em->flush();

    $io->progressFinish();

    $io->success('Importation terminée');
}

    public function readCsvFile(string $fichier): Reader
    {
        $csv = Reader::createFromPath('%kernel.root_dir%/../public/images/uploads/prospections/'.$fichier, 'r+');
        $csv->setHeaderOffset(0);

        return $csv;
    }

    public function UpdateOrCreateInventaireTemp(array $inventairetemp, FicheProspection $prospection): ProspectionTemp
    {
        //$inventairetemp = $this->prospectionsTemp->findOneBy([]);
        $prospectiontemp = new ProspectionTemp();

        //dd($inventairetemp['numero']);
        $prospectiontemp->setNumero($inventairetemp['numero']);
        $prospectiontemp->setForet($inventairetemp['foret']);
        $prospectiontemp->setCodeEssence($inventairetemp['code_essence']);
        $prospectiontemp->setZoneH($inventairetemp['zone_h']);
        $prospectiontemp->setX($inventairetemp['x']);
        $prospectiontemp->setY($inventairetemp['y']);
        $prospectiontemp->setLng($inventairetemp['lng']);
        $prospectiontemp->setDm($inventairetemp['dm']);
        $prospectiontemp->setCodeFichep($prospection);

        //Calcul du cubage
        $prospectiontemp->setVolume((($inventairetemp['dm']/2) * ($inventairetemp['dm']/2) * 3.14159 * $inventairetemp['lng']) / 10000);

        return $prospectiontemp;
    }


}