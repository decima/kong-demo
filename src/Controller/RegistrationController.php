<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Services\kong\Kong;
use App\Services\Slugger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, UserRepository $userRepository, Kong $kong): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            if ($userRepository->count([]) === 0) {
                $user->setRoles(["ROLE_ADMIN"]);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $result = $kong->getConsumerManager()->createConsumer(Slugger::slug($user->getCompany()) . "-" . time());
            $user->setKongId($result["id"]);
            $entityManager->persist($user);
            $entityManager->flush();


            // do anything else you need here, like send an email
            return $this->redirectToRoute('main');

        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
