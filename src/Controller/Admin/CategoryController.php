<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{
    /**
     * @Route("/admin/categories", name="admin_categories", methods={"GET"})
     */
    public function index()
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        return $this->render('admin/category/index.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/admin/categories/create", name="admin_categories_create", methods={"GET", "POST"})
     */
    public function create(EntityManagerInterface $em, Request $request)
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
     * @Route("/admin/categories/{slug}/edit", name="admin_categories_update", methods={"GET", "POST"})
     */
    public function update(Category $category, EntityManagerInterface $em, Request $request)
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
