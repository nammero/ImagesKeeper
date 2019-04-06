<?php

namespace App\Controller;

use App\Form\ImageType;
use App\Entity\Image;
use DateTime;
use Exception;
//use Imagine\Imagick\Image;
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
 * Class ImageController
 * @package App\Controller
 */
class ImageController extends AbstractController
{

    /**
     * @Route("/new_image", name="new_image")
     * @Method("GET")
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function new(Request $request)
    {
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $image->getFile();

            $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();
            $image->setFileName($fileName);

            try {
                $file->move(
                    $this->getParameter('images_directory'),
                    $fileName
                );
            } catch (FileException $e) {
            }

            $date = new DateTime('now');

            $image->setUserId(1);
            $image->setLoadDate($date);
            $image->setIsActive(1);

            $em = $this->getDoctrine()->getManager();
            $em->persist($image);

            $em->flush();

            return $this->redirect($this->generateUrl('home'));
        }

        return $this->render('image/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }


    /**
     * Displays a form to edit an existing Image entity.
     *
     * @Route("/{id}/edit", name="image_edit")
     * @Method("GET")
     * @param $id
     * @return Response
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('App\Entity\Image')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find the Image entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('image/edit.html.twig', [
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView()
        ]);
    }

    /**
     * Creates a form to edit an Image entity.
     * @param Image $entity The entity
     * @return FormInterface
     */
    private function createEditForm(Image $entity)
    {
        $form = $this->createForm(ImageType::class, $entity, [
            'action' => $this->generateUrl('image_update', ['id' => $entity->getId()]),
            'method' => 'PUT'
        ]);

        $form->add('submit',
            SubmitType::class,
            ['label' => 'Save',
                'attr' => ['class' => 'btn btn-primary']]);

        return $form;
    }

    /**
     * Edits an existing Image entity.
     *
     * @Route("/{id}", name="image_update")
     * @Method("PUT")
     * @param Request $request
     * @param $id
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('App\Entity\Image')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Image entity.');
        }
        $fileName = $entity->getFileName();
        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $file = $entity->getFile();

            try {
                $file->move(
                    $this->getParameter('images_directory'),
                    $fileName
                );
            } catch (FileException $e) {
            }

            $date = new DateTime('now');

            $entity->setUserId(1);
            $entity->setLoadDate($date);
            $entity->setIsActive(1);

            $em->persist($entity);


            $em->flush();

            return $this->redirect($this->generateUrl('home'));
        }

        return $this->render('image/edit.html.twig', [
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView()
        ]);
    }


    /**
     * Deletes an Image entity.
     *
     * @Route("/{id}/delete", name="image_delete")
     * @param Image $image
     * @return RedirectResponse
     */
    public function deleteAction(Image $image)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($image);
        $em->flush();

        return $this->redirect($this->generateUrl('home'));
    }

    /**
     * Creates a form to delete an Image entity by id.
     *
     * @param mixed $id The entity id
     * @return FormInterface
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('image_delete', ['id' => $id]))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, [
                'label' => 'Delete',
                'attr' => ['class' => 'btn btn-danger pull-right']
            ])
            ->getForm();
    }
}
