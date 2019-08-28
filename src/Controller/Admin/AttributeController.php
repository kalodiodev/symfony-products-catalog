<?php

namespace App\Controller\Admin;

use App\Entity\Attribute;
use App\Form\AttributeType;
use Doctrine\ORM\EntityManagerInterface;
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

    /**
     * Create Attribute
     *
     * @Route("/create", name="admin_attributes_create", methods={"GET", "POST"})
     */
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $attribute = new Attribute();

        $form = $this->createForm(AttributeType::class, $attribute, [
            'action' => $this->generateUrl('admin_attributes_create'),
            'method' => 'POST'
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $attribute = $form->getData();

            $em->persist($attribute);
            $em->flush();

            $this->addFlash('success', 'admin.attributes.flash.success.created');

            return $this->redirectToRoute('admin_attributes');
        }

        return $this->render('admin/attribute/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}