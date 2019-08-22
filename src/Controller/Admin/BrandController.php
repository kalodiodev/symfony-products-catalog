<?php

namespace App\Controller\Admin;

use App\Entity\Brand;
use App\Form\BrandType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
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

    /**
     * Create brand
     *
     * @Route("/create", name="admin_brands_create", methods={"GET", "POST"})
     */
    public function create(EntityManagerInterface $em, Request $request): Response
    {
        $brand = new Brand();

        $form = $this->createForm(BrandType::class, $brand, [
            'action' => $this->generateUrl('admin_brands_create'),
            'method' => 'POST'
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brand = $form->getData();

            $em->persist($brand);
            $em->flush();

            $this->addFlash('success', 'admin.brands.flash.success.created');

            return $this->redirectToRoute('admin_brands');
        }

        return $this->render('admin/brand/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Edit and update a brand
     *
     * @Route("/{id}/edit", name="admin_brands_update", methods={"GET", "POST"})
     */
    public function update(Brand $brand, EntityManagerInterface $em, Request $request): Response
    {
        $form = $this->createForm(BrandType::class, $brand, [
            'action' => $this->generateUrl('admin_brands_update', ['id' => $brand->getId()]),
            'method' => 'POST'
        ]);;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brand = $form->getData();

            $em->persist($brand);
            $em->flush();

            $this->addFlash('success', 'admin.brands.flash.success.updated');

            return $this->redirectToRoute('admin_brands');
        }

        return $this->render('admin/brand/update.html.twig', [
            'form' => $form->createView(),
            'brand' => $brand
        ]);
    }

    /**
     * Delete Brand
     *
     * @Route("/{id}/delete", name="admin_brands_delete", methods={"POST"})
     */
    public function destroy(Brand $brand, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            $this->addFlash('error', 'messages.error.token_mismatch');

            return $this->redirectToRoute('admin_brands_update', ['id' => $brand->getId()]);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($brand);
        $em->flush();

        $this->addFlash('success', 'admin.brands.flash.success.deleted');

        return $this->redirectToRoute('admin_brands');
    }
}