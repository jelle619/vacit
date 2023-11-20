<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Service\CandidateService;
use App\Service\VacancyService;

class HomeController extends AbstractController
{
    private $vacancyService;
    private $candidateService;

    public function __construct(VacancyService $vacancyService, CandidateService $candidateService) {
        $this->vacancyService = $vacancyService;
        $this->candidateService = $candidateService;
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $recentVacancies = $this->vacancyService->fetchRecentVacancies(3);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'vacancies' => $recentVacancies
        ]);
    }
}
