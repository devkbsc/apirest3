<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Entity\Comment;
use AppBundle\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;

class CommentCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'app:import-comment';

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();

    }


    protected function configure()
    {
        $this
            ->setName('Comment')
            ->setDescription('Import Comments')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->importComment();
        $output->writeln('Comments Json data imported successfully.');

        return 0;
    }


    public function importComment(){
        
        $client = new Client(['verify' => false]);
        $commentResponse = $client->request('GET', 'https://jsonplaceholder.typicode.com/comments');
        $commentContent = $commentResponse->getBody()->getContents();
        $commentData = json_decode($commentContent, true);

        foreach ($commentData as $item) {

            $comment = new Comment();
            $comment->setId($item['id']);
            $comment->setName($item['name']);
            $comment->setEmail($item['email']);
            $comment->setBody($item['body']);
            $comment->setPost($item['postId']);
            
            $this->entityManager->persist($comment);
        }

        $this->entityManager->flush();

    }

}
