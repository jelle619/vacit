<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Service\SubmissionService;
use App\Service\VacancyService;

class SubmissionController extends AbstractController
{
    private $submissionService;
    private $vacancyService;

    public function __construct(SubmissionService $submissionService, VacancyService $vacancyService) {
        $this->submissionService = $submissionService;
        $this->vacancyService = $vacancyService;
    }

    #[Route('/submission/{vacancyId<\d+>}', name: 'app_submission')]
    public function submission($vacancyId): Response
    {

        return $this->render('submission/new.html.twig', [
            'controller_name' => 'SubmissionController',
            'vacancy' => $this->vacancyService->fetchVacancy($vacancyId)
        ]);
    }

    #[Route('/submission/list', name: 'app_submission_list')]
    public function submissionList(): Response
    {   
        $recentSubmissions = $this->submissionService->fetchRecentSubmissions();

        return $this->render('submission/list.html.twig', [
            'controller_name' => 'SubmissionController',
            'submissions' => $recentSubmissions
        ]);
    }
}