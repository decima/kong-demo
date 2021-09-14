<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'main')]
    public function index(ServiceRepository $serviceRepository): Response
    {
        $user = $this->getUser();
        if(! $user instanceof  User){
            throw new \Exception("Not logged");
        }
        return $this->render('main/index.html.twig', [
            "services" => $serviceRepository->findAll(),
            "user" => $this->getUser(),


        ]);
    }
}
