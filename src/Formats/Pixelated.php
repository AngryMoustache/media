<?php

namespace AngryMoustache\Media\Formats;

use Spatie\Image\Image;

class Pixelated extends Format
{
    public static function render(Image $image)
    {
        return $image->crop('crop-center', 400, 400)
            ->pixelate(20)
            ->greyscale();
    }
}
