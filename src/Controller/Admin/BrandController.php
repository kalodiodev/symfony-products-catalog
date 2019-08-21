<?php

namespace App\Controller\Admin;

use App\Entity\Brand;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller used to manage brands
 *
 * @Route("/admin/brands")
 */
class BrandController extends AbstractController
{
    /**
     * List all brands
     *
     * @Route("", name="admin_brands", methods={"GET"})
     */
    public function index(): Response
    {
        $brands = $this->getDoctrine()->getRepository(Brand::class)->findAll();

        return $this->render('admin/brand/index.html.twig', [
            'brands' => $brands
        ]);
    }
}