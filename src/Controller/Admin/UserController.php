<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Controller used to manage users
 *
 * @Route("/admin/users")
 */
class UserController extends AbstractController
{
    /**
     * List all users
     *
     * @Route("", name="admin_users", methods={"GET"})
     */
    public function index(): Response
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        return $this->render('admin/user/index.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/create", name="admin_users_create", methods={"GET", "POST"})
     */
    public function create(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder): Response
    {
        $user = new User();
        $form = $this->userForm($user, 'admin_users_create', UserType::ALL);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleSaveOrUpdateUser(
                $user, $request, $form, $encoder, 'admin.users.flash.success.created'
            );

            return $this->redirectToRoute('admin_users');
        }

        return $this->render('admin/user/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Edit and Update user
     *
     * @Route("/{id}/edit", name="admin_users_update", methods={"GET","POST"})
     * @Route("/{id}/password", name="admin_users_update_password", methods={"POST"})
     */
    public function update(User $user, Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $infoForm = $this->userForm($user, 'admin_users_update', UserType::INFO_ONLY);
        $passwordForm = $this->userForm($user, 'admin_users_update_password', UserType::PASSWORD_ONLY);

        // User Info Update submitted
        if ($request->attributes->get('_route') == 'admin_users_update') {
            $infoForm->handleRequest($request);

            if ($infoForm->isSubmitted() && $infoForm->isValid()) {
                return $this->handleSaveOrUpdateUser(
                    $user, $request, $infoForm, $encoder, 'admin.users.flash.success.updated'
                );
            }
        }

        // User Password Update Submitted
        if ($request->attributes->get('_route') == 'admin_users_update_password') {
            $passwordForm->handleRequest($request);

            if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
                return $this->handleSaveOrUpdateUser(
                    $user, $request, $passwordForm, $encoder, 'admin.users.flash.success.updated'
                );
            }
        }

        return $this->render('admin/user/update.html.twig', [
            'form' => $infoForm->createView(),
            'passwordForm' => $passwordForm->createView(),
            'user' => $user
        ]);
    }

    /**
     * @Route("/{id}/delete", name="admin_users_delete", methods={"POST"})
     */
    public function destroy(User $user, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            $this->addFlash('error', 'messages.error.token_mismatch');

            return $this->redirectToRoute('admin_users_delete', ['id' => $user->getId()]);
        }

        $authId = $this->getUser()->getId();
        $userId = $user->getId();

        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        if ($authId == $userId) {
            $this->get('session')->clear();
            $session = new Session();
            $session->invalidate();

            return $this->redirectToRoute('app_login');
        }

        $this->addFlash('success', 'admin.users.flash.success.deleted');

        return $this->redirectToRoute('admin_users');
    }

    /**
     * Update User info
     *
     * @param User $user
     * @param Request $request
     * @param FormInterface $form
     * @param UserPasswordEncoderInterface $encoder
     * @param string $successMsg
     * @return RedirectResponse
     */
    protected function handleSaveOrUpdateUser(User $user,
                                              Request $request,
                                              FormInterface $form,
                                              UserPasswordEncoderInterface $encoder,
                                              $successMsg)
    {
        $mode = $form->getconfig()->getOption('mode');
        $em = $this->getDoctrine()->getManager();

        if($mode == UserType::INFO_ONLY || $mode == UserType::ALL) {
            $user->setName($request->request->get('user')['name']);
            $user->setEmail($request->request->get('user')['email']);
        }

        if($mode == UserType::PASSWORD_ONLY || $mode == UserType::ALL) {
            $user->setPassword($encoder->encodePassword($user, $request->request->get('user')['password']['first']));
        }

        $em->persist($user);
        $em->flush();

        $this->addFlash('success', $successMsg);

        return $this->redirectToRoute('admin_users');
    }

    /**
     * Create User info form
     *
     * @param User $user
     * @param string $mode
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function userForm(User $user, $route, $mode = UserType::INFO_ONLY)
    {
        $submitLabel = $mode == UserType::PASSWORD_ONLY ? 'admin.users.button.update_password' : 'admin.users.button.save';

        return $this->createForm(UserType::class, $user, [
            'action' => $this->generateUrl($route, ['id' => $user->getId()]),
            'method' => 'POST',
            'mode' => $mode,
            'submit_label' => $submitLabel
        ]);
    }
}