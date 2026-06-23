<?php


namespace App\Providers;


use Illuminate\Support\ServiceProvider;



class ServerSyncServiceProvider extends ServiceProvider
{


    public function register()
    {
    }



    public function boot()
    {
        $this->commands([
            \App\Console\Commands\SyncPullCommand::class,
            \App\Console\Commands\SyncStatusCommand::class,
        ]);
    }
}
