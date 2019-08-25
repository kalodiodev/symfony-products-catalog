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
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1) ?: 1;

        $categories = $this->getDoctrine()->getRepository(Category::class)
            ->findAllPaginated($page, 20);

        $categories->setCustomParameters(['align' => 'center']);

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

            $this->addFlash('success', 'admin.categories.flash.success.created');

            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/category/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Edit and update a category
     *
     * @Route("/{id}/edit", name="admin_categories_update", methods={"GET", "POST"})
     */
    public function update(Category $category, EntityManagerInterface $em, Request $request): Response
    {
        $form = $this->createForm(CategoryType::class, $category, [
            'action' => $this->generateUrl('admin_categories_update', ['id' => $category->getId()]),
            'method' => 'POST'
        ]);;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();

            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'admin.categories.flash.success.updated');

            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/category/update.html.twig', [
            'form' => $form->createView(),
            'category' => $category
        ]);
    }

    /**
     * Delete Category
     *
     * @Route("/{id}/delete", name="admin_categories_delete", methods={"POST"})
     */
    public function destroy(Category $category, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            $this->addFlash('error', 'messages.error.token_mismatch');

            return $this->redirectToRoute('admin_categories_update', ['id' => $category->getId()]);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();

        $this->addFlash('success', 'admin.categories.flash.success.deleted');

        return $this->redirectToRoute('admin_categories');
    }
}
