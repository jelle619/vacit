<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\SecurityBundle\Security;
use Doctrine\ORM\EntityManagerInterface;

use DateTime;

use App\Entity\Submission;

use App\Service\SubmissionService;
use App\Service\VacancyService;
use Doctrine\ORM\EntityManager;

class SubmissionController extends AbstractController
{
    private $submissionService;
    private $vacancyService;

    public function __construct(SubmissionService $submissionService, VacancyService $vacancyService) {
        $this->submissionService = $submissionService;
        $this->vacancyService = $vacancyService;
    }

    #[Route('/submission/{vacancyId<\d+>}', name: 'app_submission')]
    public function submission($vacancyId, Request $request, Security $security, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $security->getUser();
        $vacancy = $this->vacancyService->fetchVacancy($vacancyId);
        $existingSubmission = $this->submissionService->findSubmission($user->getId(), $vacancyId);
        $submissionAction = $request->request->get('submission');

        # should possibly be its own function
        if ($submissionAction == "add" && is_null($existingSubmission)) {
            $submission = new Submission();
            $submission->setCandidate($user);
            $submission->setVacancy($vacancy);
            $submission->setDate(new DateTime());
            $submission->setInvited(false);
            $entityManager->persist($submission);
            $entityManager->flush();
            $this->addFlash('success', 'Je sollicitatie voor deze vacature is opgeslagen en je profiel is verstuurd naar de werkgever.');
        } else if ($submissionAction == "add" && isset($existingSubmission)) {
            $this->addFlash('error', 'Je hebt je al gesoliciteerd voor deze vacature.');
        } else if ($submissionAction == "remove" && isset($existingSubmission)) {
            $entityManager->remove($existingSubmission);
            $entityManager->flush();
            $this->addFlash('success', 'Je sollicitatie voor deze vacature is ongedaan gemaakt. De werkgever kan niet langer je profiel inzien.');
        } else if ($submissionAction == "remove" && is_null($existingSubmission)) {
            $this->addFlash('error', 'Je sollicitatie is niet ongedaan gemaakt omdat deze niet bleek te bestaan.');
        }
        
        if (isset($submissionAction)) $existingSubmission = $this->submissionService->findSubmission($user->getId(), $vacancyId);

        return $this->render('submission/edit.html.twig', [
            'controller_name' => 'SubmissionController',
            'vacancy' => $this->vacancyService->fetchVacancy($vacancyId),
            'existing_submission' => $existingSubmission
        ]);
    }

    #[Route('/submission/list', name: 'app_submission_list')]
    public function submissionList(Security $security): Response
    {   
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $security->getUser();
        $recentSubmissions = $this->submissionService->fetchRecentUserSubmissions($user->getId());

        return $this->render('submission/list.html.twig', [
            'controller_name' => 'SubmissionController',
            'submissions' => $recentSubmissions
        ]);
    }
}