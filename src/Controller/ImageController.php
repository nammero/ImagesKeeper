<?php

namespace App\Controller;

use App\Form\ImageType;
use App\Entity\Image;
use App\Repository\UserRepository;
use App\Service\ImageService;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ImageController.
 */
class ImageController extends AbstractController
{
    /**
     * @Route("/new_image", name="new_image")
     * @Method("GET")
     *
     * @param UserRepository $repository
     * @param Request        $request
     * @param ImageService   $imageService
     *
     * @return RedirectResponse|Response
     *
     * @throws NonUniqueResultException
     */
    public function new(UserRepository $repository, Request $request, ImageService $imageService)
    {
        $image = new Image();
        $user = $this->getUser();
        if (!$user) {
            $user = $repository->findUserById(1);
        }
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageService->SaveOrUpdateImage($image, $user);

            return $this->redirect($this->generateUrl('home'));
        }

        return $this->render('image/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing Image entity.
     *
     * @Route("/{id}/edit", name="image_edit")
     * @Method("GET")
     *
     * @param $id
     *
     * @return Response
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $image = $em->getRepository('App\Entity\Image')->find($id);

        if (!$image) {
            throw $this->createNotFoundException('Unable to find the Image entity.');
        }

        $editForm = $this->createEditForm($image);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('image/edit.html.twig', [
            'image' => $image,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Creates a form to edit an Image entity.
     *
     * @param Image $entity The entity
     *
     * @return FormInterface
     */
    private function createEditForm(Image $entity)
    {
        $form = $this->createForm(ImageType::class, $entity, [
            'action' => $this->generateUrl('image_update', ['id' => $entity->getId()]),
            'method' => 'PUT',
        ]);

        $form->add('submit',
            SubmitType::class,
            ['label' => 'Save',
                'attr' => ['class' => 'btn btn-primary'], ]);

        return $form;
    }

    /**
     * Edits an existing Image entity.
     *
     * @Route("/{id}", name="image_update")
     * @Method("PUT")
     *
     * @param UserRepository $repository
     * @param Request        $request
     * @param $id
     * @param ImageService $imageService
     *
     * @return RedirectResponse|Response
     *
     * @throws Exception
     */
    public function updateAction(UserRepository $repository, Request $request, $id, ImageService $imageService)
    {
        $em = $this->getDoctrine()->getManager();
        $image = $em->getRepository('App\Entity\Image')->find($id);
        $user = $this->getUser();
        if (!$user) {
            $user = $repository->findUserById(1);
        }

        if (!$image) {
            throw $this->createNotFoundException('Unable to find Image entity.');
        }
        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($image);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $imageService->SaveOrUpdateImage($image, $user);

            return $this->redirect($this->generateUrl('home'));
        }

        return $this->render('image/edit.html.twig', [
            'image' => $image,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes an Image entity.
     *
     * @Route("/{id}/delete", name="image_delete")
     *
     * @param Image $image
     *
     * @return RedirectResponse
     */
    public function deleteAction(Image $image)
    {
        $em = $this->getDoctrine()->getManager();
        $fileName = $image->getFileName();

        try {
            unlink($this->getParameter('images_directory').'/'.$fileName);
            unlink($this->getParameter('small_images_directory').'/'.$fileName);
        } catch (FileException $e) {
        }

        $em->remove($image);
        $em->flush();

        return $this->redirect($this->generateUrl('home'));
    }

    /**
     * Creates a form to delete an Image entity by id.
     *
     * @param int $id The entity id
     *
     * @return FormInterface
     */
    private function createDeleteForm(int $id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('image_delete', ['id' => $id]))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, [
                'label' => 'Delete',
                'attr' => ['class' => 'btn btn-danger pull-right'],
            ])
            ->getForm();
    }
}
