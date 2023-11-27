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
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;

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
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $security->getUser();

        # to prevent the controller from becoming too big, forms should be moved to a seperate file
        $form = $this->createFormBuilder($user)
            ->add('firstName', TextType::class, [
                'label' => 'Voornaam'
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Achternaam'
            ])
            ->add('imageFile', VichImageType::class, [
                'label' => "Profielfoto",
                'required' => false,
                'allow_delete' => false,
                'delete_label' => 'Verwijder',
                'download_label' => 'Download',
                'download_uri' => false,
                'image_uri' => false,
                'asset_helper' => true,
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-mail'
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'De wachtwoorden moeten overeenkomen.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Wachtwoord'],
                'second_options' => ['label' => 'Wachtwoord (herhaling)'],
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
            ->add('cvFile', VichFileType::class, [
                'label' => "CV",
                'required' => false,
                'allow_delete' => true,
                'delete_label' => 'Verwijder',
                'download_uri' => true,
                'download_label' => 'Download',
                'asset_helper' => true,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Opslaan'
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setFirstName($form->get('firstName')->getData());
            $user->setLastName($form->get('lastName')->getData());
            $user->setImageFile($form->get('imageFile')->getData());
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
            $user->setCvFile($form->get('cvFile')->getData());

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Je profiel is bijgewerkt met de nieuwe gegevens.');
        }

        // ignore attribute in entry not working, must be set to null to prevent serialization errors
        $user->setImageFile(null);
        $user->setCvFile(null);

        return $this->render('account/index.html.twig', [
            'controller_name' => 'AccountController',
            'form' => $form,
            'user' => $user
        ]);
    }
}
