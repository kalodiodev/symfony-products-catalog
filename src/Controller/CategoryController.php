<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller used to manage categories in the public part of the site
 */
class CategoryController extends AbstractController
{
    /**
     * List all categories
     *
     * @Route("/category", name="category")
     */
    public function index(): Response
    {
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
        ]);
    }

    /**
     * Render categories menu in the navigation bar
     * Called directly via the render() function
     */
    public function navbarCategories(): Response
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        return $this->render('_navbar_categories.html.twig', [
            'categories' => $categories
        ]);
    }
}
