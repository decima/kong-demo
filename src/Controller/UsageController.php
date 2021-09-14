<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Services\kong\Kong;
use phpDocumentor\Reflection\DocBlock;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UsageController extends AbstractController
{
    #[Route('/usage', name: 'usage')]
    public function index(LoggerInterface $logger, Request $request, UserRepository $userRepository, Kong $kong): Response
    {
        $data = $request->toArray();
        dump($data);
        if ($data["response"]["status"] < 299) {
            $id = $data["consumer"]["id"];
            $user = $userRepository->findOneBy(["kongId" => $id]);
            foreach ($user->getUsages() as $usage) {
                if ($usage->getService()->getKongId() != $data["service"]["id"]) {
                    continue;
                }
                if ($usage->getQuota() > 0) {
                    $usage->decrementQuota();
                    if ($usage->getQuota() == 0) {
                        $kong->getConsumerManager()->removeGroup($id, $data["service"]["name"]);
                    }
                }
            }
            $this->getDoctrine()->getManager()->flush();
        }


        return $this->json([], Response::HTTP_ACCEPTED);
    }
}
