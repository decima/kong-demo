<?php

namespace App\Controller;

use App\Entity\Service;
use App\Entity\Usage;
use App\Entity\User;
use App\Services\kong\Kong;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/services', name: 'service')]
class ServiceController extends AbstractController
{
    #[Route('/add/{service}', name: '_add')]
    public function add(Service $service, Kong $kong, EntityManagerInterface $entityManager): Response
    {
        /**
         * @var $user User
         */
        $user = $this->getUser();
        $kong->getConsumerManager()->addGroup($user->getKongId(), $service->getName());
        $usage = new Usage();
        $usage->setUser($user);
        $usage->setService($service);
        $usage->setQuota(10);
        $entityManager->persist($usage);
        $entityManager->flush();

        return $this->redirectToRoute("main");
    }

    #[Route('/remove/{service}', name: '_remove')]
    public function remove(Service $service, Kong $kong, EntityManagerInterface $entityManager): Response
    {
        /**
         * @var $user User
         */
        $user = $this->getUser();
        $kong->getConsumerManager()->removeGroup($user->getKongId(), $service->getName());
        foreach ($user->getUsages() as $u) {
            if ($u->getService()->getId() == $service->getId()) {
                $entityManager->remove($u);
                break;
            }
        }
        $entityManager->flush();
        return $this->redirectToRoute("main");
    }

    #[Route('/quota/{service}/{quota}', name: '_quota')]
    public function quota(Service $service, $quota, Kong $kong): Response
    {
        /**
         * @var $user User
         */
        $user = $this->getUser();
        $usage = null;
        foreach ($user->getUsages() as $u) {
            if ($u->getService()->getId() == $service->getId()) {
                $usage = $u;
                break;
            }
        }
        if ($usage == null) {
            return $this->redirectToRoute("main");
        }
        if ($quota === "lock"){
            $kong->getConsumerManager()->removeGroup($user->getKongId(), $service->getName());

        }
        if ($usage->getQuota() === 0 && $quota !== "lock") {
            $kong->getConsumerManager()->addGroup($user->getKongId(), $service->getName());
        }
        $new = $quota === "inf" ? -1 : ($quota === "lock" ? 0 : $usage->getQuota() + $quota);
        $usage->setQuota($new);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute("main");
    }
}
