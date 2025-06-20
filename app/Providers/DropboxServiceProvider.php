<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use Spatie\Dropbox\Client as DropboxClient;
use Spatie\FlysystemDropbox\DropboxAdapter;
use Stevenmaguire\OAuth2\Client\Provider\Dropbox;


class DropboxServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Storage::extend('dropbox', function (Application $app, array $config) {
        //     $adapter = new DropboxAdapter(new DropboxClient(
        //         $config['authorization_token']
        //     ));
   
        //     return new FilesystemAdapter(
        //         new Filesystem($adapter, $config),
        //         $adapter,
        //         $config
        //     );
        // });
        $this->app->singleton(Dropbox::class, function ($app) {
            return new Dropbox([
                'clientId' => config('filesystems.disks.dropbox.key'),
                'clientSecret' => config('filesystems.disks.dropbox.secret'),
                'redirectUri' => config('filesystems.disks.dropbox.redirect_url'),
            ]);
        });
        
    }
}
