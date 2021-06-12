<?php

namespace AngryMoustache\Media\Commands;

use AngryMoustache\Media\Models\Attachment;
use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Console\Helper\ProgressBar;

class GetRandomPexelImages extends Command
{
    protected $signature = 'media:random {type} {amount?}';

    protected $description = 'Import images from Pexel';

    public function handle(Client $client)
    {
        $askedImageSize = $this->choice(
            'Image size? [small (4MP) / medium (12MP) / large (24MP)]',
            ['small', 'medium', 'large'],
            0
        );

        $this->info(
            'Importing ' . $this->argument('amount', 10) . ' ' .
            $this->argument('type', 10) . ' images from Pexel.'
        );

        $this->importImages(
            $this->fetchImagesFromPexel($client),
            $askedImageSize
        );

        $this->info('');
        $this->info('Done importing!');
    }

    protected function importImages(array $images, string $askedImageSize)
    {
        $bar = new ProgressBar($this->output, count($images));
        $bar->start();

        foreach ($images as $image) {
            $url = $image->src->{$askedImageSize};
            $fileInfo = getimagesize($url);
            $name = substr($image->src->original, strrpos($image->src->original, '/') + 1);
            $extension = Str::beforeLast(Str::afterLast($url, '.'), '?');

            $attachment = Attachment::firstOrCreate([
                'original_name' => $name,
                'alt_name' => $name,
                'disk' => config('media.default-disk', 'public'),
                'width' => $image->width,
                'height' => $image->height,
                'mime_type' => $fileInfo['mime'],
                'size' => get_headers($url, 1)['Content-Length'] ?? 0,
                'extension' => $extension,
            ]);

            Storage::putFileAs("public/attachments/{$attachment->id}/", $url, $name);

            $bar->advance();
        }

        $bar->finish();
    }

    protected function fetchImagesFromPexel(Client $client): array
    {
        $response = $client->get('https://api.pexels.com/v1/search', [
            'headers' => ['Authorization' => env('PEXEL_KEY')],
            'query' => [
                'query' => $this->argument('type'),
                'per_page' => $this->argument('amount', 10),
            ],
        ]);

        return json_decode($response->getBody()->getContents())->photos;
    }
}
