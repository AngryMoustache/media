<?php

namespace AngryMoustache\Media\Formats;

use Spatie\Image\Image;

abstract class Format
{
    public static $alwaysRefresh = false;

    abstract static function render(Image $image);
}
