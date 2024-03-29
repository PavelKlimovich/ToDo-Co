<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Services\UserService;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    private ManagerRegistry $doctrine;
    private UserService $userService;

    public function __construct(ManagerRegistry $doctrine, UserService $userService)
    {
        $this->doctrine = $doctrine;
        $this->userService = $userService;
    }

    /**
     * @Route("/users", name="user_list", methods={"GET"})
     */
    public function listAction(UserRepository $userRepository): Response
    {
        if (!$this->userService->ifAuthorisation()) {
            return $this->redirectToRoute('homepage');
        }

        return $this->render('user/list.html.twig', ['users' => $userRepository->findAll()]);
    }

    /**
     * @Route("/users/create", name="user_create")
     */
    public function createAction(Request $request, UserPasswordHasherInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->doctrine->getManager();
            $password =  $passwordEncoder->hashPassword($user, $user->getPassword());
            $user->setPassword($password);
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', "L'utilisateur a bien été ajouté.");

            return $this->redirectToRoute('homepage');
        }

        return $this->render('user/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/users/{user}/edit", name="user_edit")
     */
    public function editAction(User $user, Request $request, UserPasswordHasherInterface $passwordEncoder): Response
    {
        if (!$this->userService->ifAuthorisation()) {
            return $this->redirectToRoute('homepage');
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->hashPassword($user, $user->getPassword());
            $user->setPassword($password);
            $this->doctrine->getManager()->flush();
            $this->addFlash('success', "L'utilisateur a bien été modifié");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }
}
