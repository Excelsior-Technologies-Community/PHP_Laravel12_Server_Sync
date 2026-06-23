<?php

namespace App\Services;

use App\Models\SyncHistory;

class ServerSyncService
{
    public function __construct(
        protected DatabaseSyncService $database,
        protected FileSyncService $files
    ) {}

    public function sync(array $options)
    {
        $response = [
            'database' => false,
            'files' => false,
            'files_count' => 0,
        ];

        if (!($options['skip-db'] ?? false)) {
            $response['database'] =
                $this->database->sync();
        }

        if (!($options['skip-files'] ?? false)) {

            $response['files_count'] =
                $this->files->sync(
                    $options['delete'] ?? false
                );

            $response['files'] = true;
        }

        SyncHistory::create([
            'database_synced' => $response['database'],
            'files_synced' => $response['files'],
            'files_count' => $response['files_count'],
        ]);

        return $response;
    }
}