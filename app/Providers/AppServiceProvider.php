<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
   /* public function boot()
    {

        if (class_exists('Swift_Preferences')) {
            \Swift_Preferences::getInstance()->setTempDir(storage_path().'/tmp');
        } else {
            \Log::warning('Class Swift_Preferences does not exists');
        }
    }*/

 public function boot()
{
    $tmpPath = storage_path('tmp');

    // Ensure the directory exists
    if (!file_exists($tmpPath)) {
        mkdir($tmpPath, 0777, true); // recursively create folder with write permissions
    }

    if (class_exists('Swift_Preferences')) {
        \Swift_Preferences::getInstance()->setTempDir($tmpPath);
    } else {
        \Log::warning('Class Swift_Preferences does not exist');
    }
}
}
