<?php

namespace AngryMoustache\Media\Formats;

use Spatie\Image\Image;

class Thumb extends Format
{
    // Force re-caching of images (cropper)
    public static $alwaysRefresh = true;

    public static function render(Image $image)
    {
        return $image->crop('crop-center', 400, 400);
    }

    public static function cropperOptions()
    {
        return [
            'aspectRatio' => 1,
        ];
    }
}
