<?php


namespace App\Services;



class ServerSyncService
{


    public function __construct(

        protected DatabaseSyncService $database,

        protected FileSyncService $files

    ) {}




    public function sync(
        array $options
    ) {


        $response = [

            'database' => false,

            'files' => false

        ];



        if (
            !($options['skip-db'] ?? false)
        ) {


            $response['database'] =

                $this->database->sync();
        }



        if (
            !($options['skip-files'] ?? false)
        ) {


            $response['files'] =

                $this->files->sync(

                    $options['delete'] ?? false

                );
        }



        return $response;
    }
}
