<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Repository\BlogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    public function __construct(
        public BlogRepository $blogRepository,
        public EntityManagerInterface $em,
    )
    {

    }

    #[Route('/', name: 'app_default')]
    public function index(Request $request): Response
    {
        return $this->redirectToRoute('app_blog_index');
        /*
        $blog = (new Blog())
            ->setTitle('Title')
            ->setText('Text')
            ->setDescription('Description');

        $this->em->persist($blog);
        $this->em->flush();
        */


    }
}