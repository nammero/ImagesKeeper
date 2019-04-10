<?php

namespace App\Controller;

use App\Repository\ImageRepository;
use App\Service\ImageService;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @Method("GET")
     *
     * @param ImageRepository    $repository
     * @param Request            $request
     * @param PaginatorInterface $paginator
     *
     * @return Response
     */
    public function index(ImageRepository $repository, Request $request, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $repository->getWithSearchQueryBuilder();
        $uploadsSize = ImageService::folderSize('uploads');

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1), /*page number*/
            8 /*limit per page*/
        );

        return $this->render('default/index.html.twig', [
            'pagination' => $pagination,
            'uploadsSize' => $uploadsSize,
        ]);
    }
}
