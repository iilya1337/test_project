<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AuthorizationType;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/Authorization')]
class AuthorizationController extends AbstractController
{
    #[Route(name: 'app_authorization')]
    public function authorization_index(AuthenticationUtils $utils,  Request $request): Response
    {
        $error = $utils->getLastAuthenticationError();
        $lastUsername = $utils->getLastUsername();
        $from = $this->createForm(AuthorizationType::class);
        return $this->render('login/index.html.twig', [
            'Form' => $from->createView(),
            'error' => $error,
            'last_username' => $lastUsername
        ]);
    }


    #[Route('/Check', name: 'app_authorization_check', methods: ['POST'])]
    public function authorization(
        Request                  $request,
        JWTTokenManagerInterface $JWTManager,
        UserRepository           $userRepository,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse
    {
        $form = $request->request->all()['authorization_form'];
        $user = $userRepository->findOneBy(['email' => $form['email']]);

        if (null === $user || !$passwordHasher->isPasswordValid($user, $form['password'])) {
            return new JsonResponse(['message' => 'Invalid credentials'], 401);
        }

        $token = $JWTManager->create($user);

        return new JsonResponse(['token' => $token]);
    }

    #[Route('/Registration', name: 'app_registration')]
    public function registration(
        Request                     $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface      $entityManager
    ): Response
    {

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);


            $entityManager->persist($user);
            $entityManager->flush();

            #return $this->redirectToRoute('app_blog_new');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route(path: '/Disapproval', name: 'app_disapproval')]
    public function logout(): void {}
}
