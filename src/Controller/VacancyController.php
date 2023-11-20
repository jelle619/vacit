<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Service\VacancyService;
use App\Service\CompanyService;
use App\Service\EmployerService;

class VacancyController extends AbstractController
{

    private $vacancyService;
    private $candidateService;
    private $companyService;
    private $employerService;

    public function __construct(VacancyService $vacancyService, CompanyService $companyService, EmployerService $employerService) {
        $this->vacancyService = $vacancyService;
        $this->employerService = $employerService;
        $this->companyService = $companyService;
    }

    #[Route('/vacancy/{id}', name: 'app_vacancy')]
    public function vacancy($id): Response
    {
        $vacancy = $this->vacancyService->fetchVacancy($id);
        $relatedVacancies = $this->vacancyService->fetchRelatedVacancies($id);

        return $this->render('vacancy/index.html.twig', [
            'controller_name' => 'VacancyController',
            'vacancy' => $vacancy,
            'related_vacancies' => $relatedVacancies
        ]);
    }
}