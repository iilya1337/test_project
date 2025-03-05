<?php

namespace App\MessageHandler;

use App\Message\ContentWatchJob;
use App\Repository\BlogRepository;
use App\Services\GenerateTextAI;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ContentWatchJobHandler
{
    public function __construct(
        private readonly GenerateTextAI $generator,
        private readonly BlogRepository $blogRepository,
        private readonly EntityManagerInterface $em,
    )
    {

    }

    public function __invoke(ContentWatchJob $message)
    {
        list($id, $text) = json_decode($message->getContent());
        $query = $this->generator->createQueryString($text)->generateTexT();
        $blog = $this->blogRepository->find($id);
        $blog->setText(json_decode($query, true)['data']);
        $this->em->persist($blog);
        $this->em->flush();
    }
}