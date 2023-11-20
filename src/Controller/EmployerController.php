<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Service\SubmissionService;
use App\Service\VacancyService;
use App\Service\CompanyService;
use App\Service\EmployerService;
use App\Service\CandidateService;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Bundle\SecurityBundle\Security;

#[Route('/employer')]
class EmployerController extends AbstractController
{
    private $vacancyService;
    private $candidateService;
    private $companyService;
    private $employerService;
    private $submissionService;


    public function __construct(VacancyService $vacancyService, CandidateService $candidateService, CompanyService $companyService, EmployerService $employerService, SubmissionService $submissionService)
    {
        $this->vacancyService = $vacancyService;
        $this->candidateService = $candidateService;
        $this->companyService = $companyService;
        $this->employerService = $employerService;
        $this->submissionService = $submissionService;
    }

    #[Route('/login', name: 'app_employer_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('employer/login.html.twig', [
            'controller_name' => 'EmployerController',
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route('/vacancy/{vacancyId<\d+>}', name: 'app_submission')]
    public function submission($vacancyId): Response
    {

        return $this->render('employer/vacancy.html.twig', [
            'controller_name' => 'SubmissionController',
            'vacancy' => $this->vacancyService->fetchVacancy($vacancyId)
        ]);
    }

    #[Route('/vacancy/list', name: 'app_vacancy_list')]
    public function submissionList(Security $security): Response
    {
        $user = $security->getUser();
        $vacancies = $this->vacancyService->fetchEmployerVacancies($user->getId());

        return $this->render('employer/vacancy_list.html.twig', [
            'controller_name' => 'SubmissionController',
            'vacancies' => $vacancies
        ]);
    }
}
