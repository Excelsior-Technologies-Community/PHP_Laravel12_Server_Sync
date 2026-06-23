<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SyncHistory;

class SyncStatusCommand extends Command
{
    protected $signature = 'sync:status';

    protected $description =
        'Display latest sync status';

    public function handle()
    {
        $sync = SyncHistory::latest()->first();

        if (!$sync) {

            $this->error(
                'No sync history found.'
            );

            return Command::FAILURE;
        }

        $this->table(
            ['Database', 'Files', 'File Count', 'Date'],
            [[
                $sync->database_synced ? 'Yes' : 'No',
                $sync->files_synced ? 'Yes' : 'No',
                $sync->files_count,
                $sync->created_at
            ]]
        );

        return Command::SUCCESS;
    }
}