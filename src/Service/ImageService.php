<?php

namespace App\Service;

use App\Entity\Image;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ImageService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var ParameterBagInterface
     */
    private $bag;

    public function __construct(EntityManagerInterface $em, ParameterBagInterface $bag)
    {
        $this->em = $em;
        $this->bag = $bag;
    }

    public function SaveOrUpdateImage(Image $image)
    {
        $file = $image->getFile();
        $imageDir = $this->bag->get('images_directory');
        $smallImageDir = $this->bag->get('small_images_directory');

        if (!$image->getLoadDate()) {
            $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();
            $image->setFileName($fileName);
        } else {
            $fileName = $image->getFileName();
        }

        if ($file) {
            try {
                $file->move($imageDir, $fileName);
                ImageHandlerService::ImageResize($fileName, $imageDir, $smallImageDir);
            } catch (FileException $e) {
            }
        }

        $date = new DateTime('now');

        $image->setUserId(1);
        $image->setLoadDate($date);

        $em = $this->em;
        $em->persist($image);

        $em->flush();
    }

    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        return md5(uniqid());
    }
}
