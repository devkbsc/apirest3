<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;

class UserCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'app:import-user';

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setName('User')
            ->setDescription('Users Import')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client = new Client(['verify' => false]);
        $response = $client->request('GET', 'https://jsonplaceholder.typicode.com/users');
        $content = $response->getBody()->getContents();
        $data = json_decode($content, true);

        foreach ($data as $item) {
            $user = new User();
            $user->setId($item['id']);
            $user->setName($item['name']);
            $user->setEmail($item['email']);
            $user->setAddress($item['address']);
            $user->setPhone($item['phone']);
            $user->setWebsite($item['website']);
            $user->setCompany($item['company']);

            $em = $this->getContainer()->get('doctrine')->getManager();
            $em->persist($user);
        }
        $em->flush();

        $output->writeln('Users Json data imported successfully.');

        return 0;
    }

}
