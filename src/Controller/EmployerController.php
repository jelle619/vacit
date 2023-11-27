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

use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;

use App\Entity\Vacancy;

use Symfony\Component\HttpFoundation\Request;

use Doctrine\ORM\EntityManagerInterface;

use DateTime;

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
            'error' => $error,
            'firewall' => 'employer'
        ]);
    }

    #[Route('/vacancy/{vacancyId<\d+>}', name: 'app_employer_vacancy')]
    public function vacancy($vacancyId, Security $security): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $security->getUser();
        $vacancy = $this->vacancyService->fetchVacancy($vacancyId);
        $submissions = $vacancy->getSubmissions();

        if ($vacancy->getEmployer()->getId() != $user->getId()) {
            $this->addFlash('error', 'Je bent niet de werkgever van deze vacature. We hebben je doorgeleid naar de kandidatenpagina.');
            return $this->redirectToRoute('app_vacancy', ['vacancyId' => $vacancyId]);
        }

        return $this->render('employer/vacancy/index.html.twig', [
            'controller_name' => 'SubmissionController',
            'vacancy' => $vacancy,
            'submissions' => $submissions,
            'firewall' => 'employer'
        ]);
    }

    #[Route('/vacancy/list', name: 'app_employer_vacancy_list')]
    public function vacancyList(Security $security): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $security->getUser();
        $vacancies = $this->vacancyService->fetchEmployerVacancies($user->getId());

        return $this->render('employer/vacancy/list.html.twig', [
            'controller_name' => 'SubmissionController',
            'vacancies' => $vacancies,
            'firewall' => 'employer'
        ]);
    }

    #[Route('/vacancy/create', name: 'app_employer_vacancy_create')]
    public function vacancyCreate(Security $security, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $security->getUser();
        $vacancy = new Vacancy();
        $form = $this->createFormBuilder($vacancy)
            ->add('name', TextType::class, [
                'label' => 'Naam'
            ])
            ->add('imageFile', VichImageType::class, [
                'label' => "Afbeelding",
                'required' => false,
                'allow_delete' => true,
                'delete_label' => 'Verwijder',
                'download_label' => 'Download',
                'download_uri' => true,
                'image_uri' => true,
                // 'imagine_pattern' => '...',
                'asset_helper' => true,
            ])
            ->add('summary', TextareaType::class, [
                'label' => 'Samenvatting'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Omschrijving'
            ])
            ->add('level', TextType::class, [
                'label' => 'Level (e.g. Junior/Medior/Senior)'
            ])
            ->add('location', TextType::class, [
                'label' => 'Locatie'
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Creëren'
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $vacancy->setEmployer($user);
            $vacancy->setName($form->get('name')->getData());
            $vacancy->setImageFile($form->get('imageFile')->getData());
            $vacancy->setSummary($form->get('summary')->getData());
            $vacancy->setDescription($form->get('description')->getData());
            $vacancy->setDate(new DateTime());
            $vacancy->setLevel($form->get('level')->getData());
            $vacancy->setLocation($form->get('location')->getData());

            $entityManager->persist($vacancy);
            $entityManager->flush();

            $this->addFlash('success', 'Je vacature is aangemaakt en gepubliceerd. Deze is nu terug te vinden in je persoonlijke vacaturelijst.');
            return $this->redirectToRoute('app_employer_vacancy_list');
        }

        // ignore attribute in entry not working, must be set to null to prevent serialization errors
        $vacancy->setImageFile($form->get('imageFile')->getData());

        return $this->render('employer/vacancy/create.html.twig', [
            'controller_name' => 'SubmissionController',
            'form' => $form,
            'firewall' => 'employer'
        ]);
    }

    #[Route('/vacancy/edit/{vacancyId<\d+>}', name: 'app_employer_vacancy_edit')]
    public function vacancyEdit(Security $security, Request $request, EntityManagerInterface $entityManager, $vacancyId): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $security->getUser();
        $vacancy = $this->vacancyService->fetchVacancy($vacancyId);

        if ($vacancy->getEmployer()->getId() != $user->getId()) {
            $this->addFlash('error', 'Je probeerde een vacature aan te passen die niet van jou is. We hebben je teruggeleid naar je eigen vacaturelijst.');
            return $this->redirectToRoute('app_employer_vacancy_list');
        }

         # to prevent the controller from becoming too big, forms should be moved to a seperate file
        $form = $this->createFormBuilder($vacancy)
            ->add('name', TextType::class, [
                'label' => 'Naam'
            ])
            ->add('imageFile', VichImageType::class, [
                'label' => "Afbeelding",
                'required' => false,
                'allow_delete' => true,
                'delete_label' => 'Verwijder',
                'download_label' => 'Download',
                'download_uri' => true,
                'image_uri' => true,
                // 'imagine_pattern' => '...',
                'asset_helper' => true,
            ])
            ->add('summary', TextareaType::class, [
                'label' => 'Samenvatting'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Omschrijving'
            ])
            ->add('level', TextType::class, [
                'label' => 'Level (e.g. Junior/Medior/Senior)'
            ])
            ->add('location', TextType::class, [
                'label' => 'Locatie'
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Wijzigen'
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $vacancy->setName($form->get('name')->getData());
            $vacancy->setImageFile($form->get('imageFile')->getData());
            $vacancy->setSummary($form->get('summary')->getData());
            $vacancy->setDescription($form->get('description')->getData());
            $vacancy->setLevel($form->get('level')->getData());
            $vacancy->setLocation($form->get('location')->getData());

            $entityManager->persist($vacancy);
            $entityManager->flush();

            $this->addFlash('success', 'De inhoud van de vacature is succesvol aangepast. De wijzigingen zijn nu voor kandidaten zichtbaar.');
            return $this->redirectToRoute('app_employer_vacancy_list');
        }

        // ignore attribute in entry not working, must be set to null to prevent serialization errors
        $vacancy->setImageFile($form->get('imageFile')->getData());

        return $this->render('employer/vacancy/edit.html.twig', [
            'controller_name' => 'EmployerController',
            'form' => $form,
            'firewall' => 'employer'
        ]);
    }

    #[Route('/vacancy/delete/{vacancyId<\d+>}', name: 'app_employer_vacancy_delete')]
    public function vacancyDelete($vacancyId, Request $request, Security $security, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $security->getUser();
        $vacancy = $this->vacancyService->fetchVacancy($vacancyId);
        $deleteRequest = $request->request->get('delete');

        if (is_null($vacancy)) {
            $this->addFlash('error', 'Deze vacature bestaat niet. Daarom kon deze niet verwijderd worden.');
            return $this->redirectToRoute('app_employer_vacancy_list');
        } elseif ($vacancy->getEmployer()->getId() != $user->getId()) {
            $this->addFlash('error', 'Je bent geen eigenaar van deze vacature. Daarom kon deze niet verwijderd worden. Kies een vacature uit je eigen vacaturelijst om te verwijderen.');
            return $this->redirectToRoute('app_employer_vacancy_list');
        }

        if ($deleteRequest) {
            $entityManager->remove($vacancy);
            $entityManager->flush();
            $this->addFlash('success', 'De vacature, alsmede de sollicitaties op deze vacature, zijn permanent verwijderd.');
            return $this->redirectToRoute('app_employer_vacancy_list');
        }

        return $this->render('employer/vacancy/delete.html.twig', [
            'controller_name' => 'EmployerController',
            'vacancy' => $vacancy,
            'firewall' => 'employer'
        ]);
    }
}
