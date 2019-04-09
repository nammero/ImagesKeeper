<?php

namespace App\Service;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;

class ImageHandlerService
{
    const WIDTH = 275;
    const HEIGHT = 183;

    public static function ImageResize($fileName, $ImageDir, $smallImageDir)
    {
        $imagine = new Imagine();
        $options = [
            'jpeg_quality' => 30,
            'png_compression_level' => 9,
        ];

        $imagine->open($ImageDir.'/'.$fileName)
            ->resize(new Box(ImageHandlerService::WIDTH, ImageHandlerService::HEIGHT))
            ->save($smallImageDir.'/'.$fileName,
                $options);
    }
}
