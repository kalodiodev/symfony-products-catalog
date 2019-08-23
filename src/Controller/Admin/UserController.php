<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller used to manage users
 *
 * @Route("/admin/users")
 */
class UserController extends AbstractController
{
    /**
     * List all users
     *
     * @Route("", name="admin_users", methods={"GET"})
     */
    public function index(): Response
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        return $this->render('admin/user/index.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * Edit and Update user
     *
     * @Route("/{id}/edit", name="admin_users_update", methods={"GET","POST"})
     */
    public function update(User $user, EntityManagerInterface $em, Request $request): Response
    {
        $form = $this->createForm(UserType::class, $user, [
            'action' => $this->generateUrl('admin_users_update', ['id' => $user->getId()]),
            'method' => 'POST'
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setName($request->request->get('user')['name']);
            $user->setEmail($request->request->get('user')['email']);

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'admin.users.flash.success.updated');

            return $this->redirectToRoute('admin_users');
        }

        return $this->render('admin/user/update.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }
}