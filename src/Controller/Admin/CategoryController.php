<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller used to manage categories
 *
 * @Route("/admin/categories")
 */
class CategoryController extends AbstractController
{
    /**
     * List all categories
     *
     * @Route("", name="admin_categories", methods={"GET"})
     */
    public function index(): Response
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        return $this->render('admin/category/index.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * Create category
     *
     * @Route("/create", name="admin_categories_create", methods={"GET", "POST"})
     */
    public function create(EntityManagerInterface $em, Request $request): Response
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category, [
            'action' => $this->generateUrl('admin_categories_create'),
            'method' => 'POST'
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();

            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'Category Created!');

            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/category/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Edit and update a category
     *
     * @Route("/{slug}/edit", name="admin_categories_update", methods={"GET", "POST"})
     */
    public function update(Category $category, EntityManagerInterface $em, Request $request): Response
    {
        $form = $this->createForm(CategoryType::class, $category, [
            'action' => $this->generateUrl('admin_categories_update', ['slug' => $category->getSlug()]),
            'method' => 'POST'
        ]);;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();

            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'Category Updated!');

            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/category/update.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
