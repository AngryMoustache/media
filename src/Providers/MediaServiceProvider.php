<?php

namespace AngryMoustache\Media\Providers;

use Illuminate\Support\ServiceProvider;

class MediaServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        $this->publishes([
            __DIR__ . '/../../database/migrations/' => database_path('migrations')
        ], 'angry-moustache/media');

        $this->mergeConfigFrom(__DIR__ . '/../../config/media.php', 'media');
        config(['filesystems.disks.attachments' => config('media.disk', [])]);
    }
}
