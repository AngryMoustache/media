<?php

namespace AngryMoustache\Media\Models;

use Exception;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Image\Image;

class Attachment extends EloquentModel
{
    protected $fillable = [
        'original_name',
        'alt_name',
        'disk',
        'height',
        'width',
        'size',
        'mime_type',
        'extension',
        'folder_location',
        'crops',
    ];

    public $casts = [
        'crops' => 'json',
    ];

    public function getPath($format = null)
    {
        $url = $this->id . '/' . ($format ? $format . '-' : '') . $this->original_name;
        return optional(Storage::disk($this->disk))->path($url);
    }

    public function path()
    {
        return optional(Storage::disk($this->disk))
            ->url($this->id . '/' . $this->original_name);
    }

    public function format($format)
    {
        $path = $this->getPath();
        $formatPath = $this->getPath($format);
        $formatClassApp = 'App\\Formats\\' . ucfirst($format);
        $formatClass = 'AngryMoustache\\Media\\Formats\\' . ucfirst($format);

        if (in_array(Str::afterLast($path, '.'), ['svg', 'gif'])) {
            $url = $this->id . '/' . $this->original_name;
            return optional(Storage::disk($this->disk))->url($url);
        }

        if (class_exists($formatClassApp)) {
            $formatClass = $formatClassApp;
        } elseif (class_exists($formatClass)) {
            $formatClass = $formatClass;
        } else {
            throw new Exception('Image Format not found, please create one in App\\Formats');
        }

        if (! is_file($formatPath)) {
            $image = Image::load($path);
            $formatClass::render($image);
            $image->save($formatPath);
        }

        $refresh = $formatClass::$alwaysRefresh;
        $url = $this->id . '/' . ($format ? $format . '-' : '') . $this->original_name;
        $url = optional(Storage::disk($this->disk))->url($url);
        return $url . ($refresh ? '?r=' . rand(1, 1000) : '');
    }

    public static function livewireUpload($file)
    {
        if (! is_file($file->getRealPath())) {
            return null;
        }

        $original = $file->getClientOriginalName();
        $fileInfo = getimagesize($file->getRealPath());

        $attachment = self::firstOrCreate([
            'original_name' => $original,
            'alt_name' => $original,
            'disk' => config('media.default-disk', 'public'),
            'width' => $fileInfo[0],
            'height' => $fileInfo[1],
            'mime_type' => $fileInfo['mime'],
            'size' => filesize($file->getRealPath()),
            'extension' => $file->guessExtension()
        ]);

        Storage::putFileAs(
            "public/attachments/{$attachment->id}/",
            $file->getRealPath(),
            $file->getClientOriginalName()
        );

        return $attachment;
    }
}
