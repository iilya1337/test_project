<?php

namespace App\Controller;

use App\Filter\BlogFilter;
use App\Form\BlogFilterType;
use App\Repository\BlogRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'app_default')]
    public function index(
        Request            $request,
        BlogRepository     $blogRepository,
        PaginatorInterface $paginator,
    ): Response
    {
        $blogFilter = new BlogFilter($this->getUser());
        $form = $this->createForm(BlogFilterType::class, $blogFilter);
        $form->handleRequest($request);


        $pagination = $paginator->paginate(
            $blogRepository->findByBlogFilter($blogFilter),
            $request->query->getInt('page', 1),
            5
        );


        return $this->render('blog/index.html.twig', [
            'blogs' => $pagination,
            'searchForm' => $form->createView(),
        ]);
    }
}