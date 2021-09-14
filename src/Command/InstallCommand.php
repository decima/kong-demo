<?php

namespace App\Command;

use App\Entity\Service;
use App\Services\kong\Kong;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'install',
    description: 'Create services in Kong',
)]
class InstallCommand extends Command
{

    public function __construct(private Kong $kong, private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $services = [
            "serviceA" => ["int" => "http://serviceA/", "ext" => "/service_a"],
            "serviceB" => ["int" => "http://serviceB/", "ext" => "/service_b"],
        ];
        $this->kong->installLogger();

        foreach ($services as $name => $properties) {
            $service = $this->kong->getServiceManager()->create($name, $properties["int"]);
            $s = new Service();
            $s->setName($name);
            $s->setKongId($service["id"]);
            $s->setInternalUrl($properties["int"]);
            $s->setPublicUrl(rtrim($this->kong->getPublicUrl(), "/") . $properties["ext"]);

            $this->kong->getServiceManager()->addRoute($s->getKongId(), $properties["ext"]);
            $this->kong->getServiceManager()->addAuth($s->getKongId());
            $this->kong->getServiceManager()->addACL($s->getKongId(), $name);
            $this->entityManager->persist($s);
        }
        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}
