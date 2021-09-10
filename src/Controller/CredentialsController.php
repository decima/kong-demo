<?php

namespace App\Controller;

use App\Entity\Credentials;
use App\Form\CredentialsType;
use App\Services\kong\Kong;
use App\Services\Slugger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CredentialsController extends AbstractController
{
    #[Route('/credentials', name: 'credentials')]
    public function index(): Response
    {

        return $this->render('credentials/index.html.twig', [
            'credentialsList' => $this->getUser()->getCredentials(),
        ]);
    }


    #[Route('/credentials/new', name: 'credentials_add')]
    public function add(Request $request, EntityManagerInterface $entityManager, Kong $kong)
    {
        $credentials = new Credentials();
        $credentials->setUser($this->getUser());
        $form = $this->createForm(CredentialsType::class, $credentials);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$credentials->getName()) {
                $credentials->setName(strtoupper(md5(uniqid())));
            }
            $entityManager->persist($credentials);
            $result = $kong->getConsumerManager()->createCredentials($credentials->getName(), $credentials->getType(), $credentials->getUser()->getKongId());
            $credentials->setKongId($result["id"]);
            $entityManager->flush();
            unset($result["id"]);
            $this->addFlash("credentials", json_encode($result));
            return $this->redirectToRoute("credentials");
        }
        return $this->render('credentials/add.html.twig', [
            'form' => $form->createView(),
        ]);

    }

    #[Route('/credentials/{credentials}/delete', name: 'credentials_delete')]
    public function delete(Credentials $credentials, Request $request, EntityManagerInterface $entityManager, Kong $kong)
    {
        if ($credentials->getUser()->getId() != $this->getUser()->getId()) {
            $this->denyAccessUnlessGranted("ROLE_ADMIN");
        }

        $entityManager->remove($credentials);
        $kong->getConsumerManager()->dropCredentials($credentials->getUser()->getKongId(), $credentials->getKongId(), $credentials->getType());
        $entityManager->flush();


        return $this->redirectToRoute("credentials");


    }

}
