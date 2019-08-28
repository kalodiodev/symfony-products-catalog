<?php

namespace App\Controller\Admin;

use App\Entity\Attribute;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller used to manage attributes
 *
 * @Route("/admin/attributes")
 */
class AttributeController extends AbstractController
{
    /**
     * List all attributes
     *
     * @Route("", name="admin_attributes", methods={"GET"})
     */
    public function index(Request $request): Response
    {
        $attributes = $this->getDoctrine()->getRepository(Attribute::class)->findAll();

        return $this->render('admin/attribute/index.html.twig', [
            'attributes' => $attributes
        ]);
    }
}