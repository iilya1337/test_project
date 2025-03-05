<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Filter\BlogFilter;
use App\Form\BlogFilterType;
use App\Form\BlogType;
use App\Message\ContentWatchJob;
use App\Repository\BlogRepository;
use App\Services\GenerateTextAI;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('user/Blog')]
class BlogController extends AbstractController
{
    #[Route(name: 'app_user_blog_index', methods: ['GET'])]
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

    /**
     * @throws ExceptionInterface
     */
    #[Route('/New', name: 'app_user_blog_new', methods: ['GET', 'POST'])]
    public function new(
        Request                $request,
        EntityManagerInterface $entityManager,
        MessageBusInterface $bus,
    ): Response
    {
        $blog = new Blog($this->getUser());
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($blog);
            $entityManager->flush();

            $content = [$blog->getId(), $request->request->all()['blog']['text']];
            $bus->dispatch(new ContentWatchJob(json_encode($content)));

            return $this->redirectToRoute('app_user_blog_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('blog/new.html.twig', [
            'blog' => $blog,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/Edit', name: 'app_user_blog_edit', methods: ['GET', 'POST']),
        IsGranted('edit', 'blog')]
    public function edit(Request $request, Blog $blog, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($blog);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_blog_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('blog/edit.html.twig', [
            'blog' => $blog,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_user_blog_delete', methods: ['POST'])]
    public function delete(Request $request, Blog $blog, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $blog->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($blog);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_blog_index', [], Response::HTTP_SEE_OTHER);
    }
}
