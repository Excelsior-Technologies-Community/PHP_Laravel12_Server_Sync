<?php


namespace App\Services;


use File;


class DatabaseSyncService
{


    public function sync()
    {


        $source =
            config(
                'server-sync.database.backup'
            );



        $destination =
            config(
                'server-sync.database.dump_path'
            );



        if (!File::exists($destination)) {

            File::makeDirectory(
                $destination,
                0755,
                true
            );
        }



        File::copy(

            $source,

            $destination . '/backup.sql'

        );



        return true;
    }
}
