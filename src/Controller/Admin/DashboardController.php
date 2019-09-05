<?php

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Dashboard Controller
 *
 * @Route("/admin")
 */
class DashboardController extends AbstractController
{
    /**
     * Dashboard
     *
     * @Route("", name="admin_dashboard", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('admin/dashboard/index.html.twig');
    }
}