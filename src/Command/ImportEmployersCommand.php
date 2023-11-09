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
use App\Repository\CompanyRepository;
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
    private CompanyRepository $companyRepository;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher, CompanyRepository $companyRepository)
    {
        $this->entityManager = $entityManager;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->companyRepository = $companyRepository;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('document_location', InputArgument::REQUIRED, 'Location of the Excel sheet to be imported.');
    }

    # Working, but the function still needs to be split up into multiple for readability.
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        # BEGIN document import

        $documentLocation = $input->getArgument('document_location');
        $io->note(sprintf('Attempting to import spreadsheet: %s', $documentLocation));

        try {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($documentLocation);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($documentLocation);
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
            die('Error loading file: ' . $e->getMessage());
        }

        $io->note('Imported the spreadsheet successfully.');

        # END document import

        $worksheet = $spreadsheet->getActiveSheet();
        $array = $worksheet->toArray();

        # BEGIN index determination

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

        # END index determination

        # BEGIN user import loop

        for ($row = 1; $row < sizeof($array); $row++) {
            # initiate new instances
            $user = new Employer();
            $company  = $this->companyRepository->find($array[$row][$companyIdIndex]);
    
            # set properties on user instance
            $user->setCompany($company);
            $user->setFirstName($array[$row][$firstNameIndex]);
            $user->setLastName($array[$row][$lastNameIndex]);
            $user->setEmail($array[$row][$emailIndex]);
            $user->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $user,
                    '%env(EMPLOYER_DEFAULT_PASSWORD)%'
                )
            );
            
            # enter user into database
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
        # END user import loop

        $io->success(sprintf('Successfully imported %s employer(s).', strval(sizeof($array) - 1)));

        return Command::SUCCESS;
    }
}