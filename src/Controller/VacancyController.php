<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VacancyController extends AbstractController
{
    #[Route('/vacancy', name: 'app_vacancy')]
    public function index(): Response
    {
        return $this->render('vacancy/index.html.twig', [
            'controller_name' => 'VacancyController',
        ]);
    }
}
