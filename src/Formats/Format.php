<?php

namespace AngryMoustache\Media\Formats;

use Spatie\Image\Image;

abstract class Format
{
    abstract static function render(Image $image);
}
