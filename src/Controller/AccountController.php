<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;

use Doctrine\ORM\EntityManagerInterface;

class AccountController extends AbstractController
{
    #[Route('/profile', name: 'app_account_redirect')]
    public function profile(): Response
    {
        return $this->redirectToRoute('app_account', [], Response::HTTP_MOVED_PERMANENTLY);
    }

    #[Route('/account', name: 'app_account')]
    public function account(Security $security, Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = $security->getUser();

        $form = $this->createFormBuilder($user)
            ->add('firstName', TextType::class, [
                'label' => 'Voornaam'
                ])
            ->add('lastName', TextType::class, [
                'label' => 'Achternaam'
                ])
            ->add('email', EmailType::class, [
                'label' => 'E-mail'
                ])
            ->add('password', PasswordType::class, [
                'label' => 'Wachtwoord',
                'required' => false,
                'empty_data' => ''
                ])
            ->add('birthDate', BirthdayType::class, [
                'label' => 'Geboortedatum'
                ])
            ->add('phoneNumber', TelType::class, [
                'label' => 'Telefoonnummer'
                ])
            ->add('address', TextType::class, [
                'label' => 'Adres'
                ])
            ->add('postalCode', TextType::class, [
                'label' => 'Postcode'
                ])
            ->add('city', TextType::class, [
                'label' => 'Plaats'
                ])
            ->add('coverLetter', TextareaType::class, [
                'label' => 'Motivatie',
                'required' => false
                ])
            ->add('save', SubmitType::class, [
                'label' => 'Opslaan'
                ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {  
            $user->setFirstName($form->get('firstName')->getData());
            $user->setLastName($form->get('lastName')->getData());
            $user->setEmail($form->get('email')->getData());
            if ($form->get('password') != '') {
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
            }
            $user->setPhoneNumber($form->get('phoneNumber')->getData());
            $user->setAddress($form->get('address')->getData());
            $user->setPostalCode($form->get('postalCode')->getData());
            $user->setCity($form->get('city')->getData());
            $user->setCoverLetter($form->get('coverLetter')->getData());

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Je profiel is bijgewerkt met de nieuwe gegevens.');
        }
        
        return $this->render('account/index.html.twig', [
            'controller_name' => 'AccountController',
            'form' => $form
        ]);
    }
}