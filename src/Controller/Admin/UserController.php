<?php

namespace App\Controller\Admin;

use App\Entity\User;
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

}