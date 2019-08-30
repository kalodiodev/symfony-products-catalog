<?php

namespace App\Controller\Admin;

use App\Entity\Product;
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
    public function index()
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();

        return $this->render('admin/product/index.html.twig', [
            'products' => $products
        ]);
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
