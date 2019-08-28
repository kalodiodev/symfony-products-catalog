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

    /**
     * Edit - update an attribute
     *
     * @Route("/{id}/edit", name="admin_attributes_update", methods={"GET", "POST"})
     */
    public function update(Attribute $attribute, EntityManagerInterface $em, Request $request): Response
    {
        $form = $this->createForm(AttributeType::class, $attribute, [
            'action' => $this->generateUrl('admin_attributes_update', ['id' => $attribute->getId()]),
            'method' => 'POST'
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $attribute = $form->getData();

            $em->persist($attribute);
            $em->flush();

            $this->addFlash('success', 'admin.attributes.flash.success.updated');

            return $this->redirectToRoute('admin_attributes');
        }

        return $this->render('admin/attribute/update.html.twig', [
            'form' => $form->createView(),
            'attribute' => $attribute
        ]);
    }

    /**
     * Delete Attribute
     *
     * @Route("/{id}/delete", name="admin_attributes_delete", methods={"POST"})
     */
    public function destroy(Attribute $attribute, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            $this->addFlash('error', 'message.error.token_mismatch');

            return $this->redirectToRoute('admin_users_update', ['id' => $attribute->getId()]);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($attribute);
        $em->flush();

        $this->addFlash('success', 'admin.attributes.flash.success.deleted');

        return $this->redirectToRoute('admin_attributes');
    }
}