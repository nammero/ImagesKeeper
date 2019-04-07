<?php

namespace App\Service;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;

class ImageHandlerService
{
    public static $width = 275;
    public static $height = 183;

    public static function ImageResize($from, $to)
    {
        $imagine = new Imagine();
        $options = [
            'jpeg_quality' => 30,
            'png_compression_level' => 9,
        ];

        $imagine->open($from)->resize(new Box(self::$width, self::$height))->save($to, $options);
    }
}
