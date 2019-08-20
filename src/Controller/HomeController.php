<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Home controller in the public part of the site
 */
class HomeController extends AbstractController
{
    /**
     * Homepage
     *
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->render('home.html.twig');
    }
}
