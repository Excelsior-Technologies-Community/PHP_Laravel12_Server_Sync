<?php


namespace App\Services;


use File;


class FileSyncService
{


    public function sync(
        $delete = false
    ) {


        $source =
            config(
                'server-sync.files.source'
            );



        $destination =
            config(
                'server-sync.files.destination'
            );



        if ($delete) {

            File::cleanDirectory(
                $destination
            );
        }



        File::copyDirectory(

            $source,

            $destination

        );



        return true;
    }
}
