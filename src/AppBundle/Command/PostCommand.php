<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;

class PostCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'app:import-post';

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }


    protected function configure()
    {
        $this
            ->setName('Post')
            ->setDescription('Import Posts')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client = new Client(['verify' => false]);
        $response = $client->request('GET', 'https://jsonplaceholder.typicode.com/posts');
        $content = $response->getBody()->getContents();
        $data = json_decode($content, true);

        foreach ($data as $item) {
            $post = new Post();
            $post->setId($item['id']);
            $post->setTitle($item['title']);
            $post->setBody($item['body']);
            $post->setUserId($item['userId']);
            $this->entityManager->persist($post);
        }
        $this->entityManager->flush();

        $output->writeln('Post Json data imported successfully.');

        return 0;
    }
}
