<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

use App\Entity\Employer;
use App\Service\CompanyService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:import-employers',
    description: 'Import employers into the database using an Excel sheet.',
    hidden: false,
    aliases: ['app:add-employers', 'app:import_employer', 'app:add-employer']
)]
class ImportEmployersCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $userPasswordHasher;
    private CompanyService $companyService;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher, CompanyService $companyService)
    {
        $this->entityManager = $entityManager;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->companyService = $companyService;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('document_location', InputArgument::REQUIRED, 'Location of the Excel sheet to be imported.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
    
        $documentLocation = $input->getArgument('document_location');

        $io->note(sprintf('Attempting to import spreadsheet: %s', $documentLocation));
        $spreadsheet = $this->documentImport($documentLocation);
        $io->note('Imported the spreadsheet successfully.');

        $worksheet = $spreadsheet->getActiveSheet();
        $array = $worksheet->toArray();

        $indexDetermination = $this->indexDetermination($array);

        $io->note(sprintf('Creating employers with default password: %s. Please change this password as soon as possible.', $_ENV['EMPLOYER_DEFAULT_PASSWORD']));
        $this->userImport($array, $indexDetermination["companyIdIndex"], $indexDetermination["firstNameIndex"], $indexDetermination["lastNameIndex"], $indexDetermination["emailIndex"]);

        $io->success(sprintf('Successfully imported %s employer(s).', strval(sizeof($array) - 1)));

        return Command::SUCCESS;
    }

    protected function documentImport($documentLocation) {
        try {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($documentLocation);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($documentLocation);
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
            die('Error loading file: ' . $e->getMessage());
        }

        return $spreadsheet;
    }

    protected function userImport($array, $companyIdIndex, $firstNameIndex, $lastNameIndex, $emailIndex) {
        for ($row = 1; $row < sizeof($array); $row++) {
            # initiate new instances
            $user = new Employer();
    
            # set properties on user instance
            $user->setCompany($this->companyService->fetchCompany($array[$row][$companyIdIndex]));
            $user->setFirstName($array[$row][$firstNameIndex]);
            $user->setLastName($array[$row][$lastNameIndex]);
            $user->setEmail($array[$row][$emailIndex]);
            $user->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $user,
                    $_ENV['EMPLOYER_DEFAULT_PASSWORD']
                )
            );
            
            # enter user into database
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
    }

    protected function indexDetermination($array) {
        $companyIdIndex = NULL;
        $firstNameIndex = NULL;
        $lastNameIndex = NULL;
        $emailIndex = NULL;

        for ($column = 0; $column < sizeof($array[0]); $column++) {
            switch ($array[0][$column]) {
                case "company_id":
                    $companyIdIndex = $column;
                    break;
                case "first_name":
                    $firstNameIndex = $column;
                    break;
                case "last_name":
                    $lastNameIndex = $column;
                    break;
                case "email":
                    $emailIndex = $column;
                    break;
            }
        }

        return array("companyIdIndex" => $companyIdIndex, "firstNameIndex" => $firstNameIndex, "lastNameIndex" => $lastNameIndex, "emailIndex" => $emailIndex);
    }
}