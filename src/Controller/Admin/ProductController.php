<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller used to manage products
 *
 * @Route("/admin/products")
 */
class ProductController extends AbstractController
{
    /**
     * List all products
     *
     * @Route("", name="admin_products")
     */
    public function index(Request $request)
    {
        $page = $request->query->getInt('page', 1) ?: 1;

        $products = $this->getDoctrine()->getRepository(Product::class)
            ->findAllPaginated($page, 20);

        $products->setCustomParameters(['align' => 'center']);

        return $this->render('admin/product/index.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * @Route("/create", name="admin_products_create", methods={"GET", "POST"})
     */
    public function create(Request $request, EntityManagerInterface $em)
    {
        $product = new Product();

        $form = $this->createForm(ProductType::class, $product, [
            'action' => $this->generateUrl('admin_products_create'),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'admin.products.flash.success.created');

            return $this->redirectToRoute('admin_products');
        }

        return $this->render('admin/product/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_products_update", methods={"GET", "POST"})
     */
    public function update(Product $product, Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(ProductType::class, $product, [
            'action' => $this->generateUrl('admin_products_update', ['id' => $product->getId()]),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'admin.products.flash.success.updated');

            return $this->redirectToRoute('admin_products');
        }

        return $this->render('admin/product/update.html.twig', [
            'product' => $product,
            'form' => $form->createView()
        ]);
    }

    /**
     * Delete product
     *
     * @Route("/{id}/delete", name="admin_products_delete", methods={"POST"})
     */
    public function destroy(Product $product, Request $request, EntityManagerInterface $em)
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            $this->addFlash('error', 'messages.error.token_mismatch');

            return $this->redirectToRoute('admin_products_update', ['id' => $product->getId()]);
        }

        $em->remove($product);
        $em->flush();

        $this->addFlash('success', 'admin.products.flash.success.deleted');

        return $this->redirectToRoute('admin_products');
    }

    /**
     * Show Product
     *
     * @Route("/{id}", name="admin_products_show", methods={"GET"})
     */
    public function show(Product $product)
    {
        return $this->render('admin/product/show.html.twig', [
            'product' => $product
        ]);
    }
}
