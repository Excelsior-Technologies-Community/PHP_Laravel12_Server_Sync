<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;

use App\Services\ServerSyncService;



class SyncPullCommand extends Command
{


    protected $signature = '

sync:pull

{--skip-db}

{--skip-files}

{--delete}

';


    protected $description =

    'Sync database and files from remote server';



    public function handle(
        ServerSyncService $service
    ) {


        $this->info(
            "Starting Server Sync..."
        );



        $result = $service->sync(

            $this->options()

        );



        if ($result['database']) {

            $this->info(
                "Database sync completed"
            );
        } else {

            $this->warn(
                "Database skipped"
            );
        }



        if ($result['files']) {

            $this->info(
                "Files sync completed"
            );
        } else {

            $this->warn(
                "Files skipped"
            );
        }



        $this->info(
            "Sync completed successfully"
        );



        return Command::SUCCESS;
    }
}
