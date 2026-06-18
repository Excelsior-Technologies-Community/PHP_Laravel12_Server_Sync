<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;

class SyncPullCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_sync_command_runs_successfully()
    {
        File::ensureDirectoryExists(
            storage_path('sync/remote/database')
        );

        File::put(
            storage_path('sync/remote/database/backup.sql'),
            'CREATE TABLE test(id INT);'
        );

        $this->artisan(
            'sync:pull',
            [
                '--skip-files' => true
            ]
        )->assertExitCode(0);

        $this->assertFileExists(
            storage_path('sync/dumps/backup.sql')
        );
    }

    public function test_skip_database_option()
    {
        $this->artisan(
            'sync:pull',
            [
                '--skip-db' => true
            ]
        )->assertExitCode(0);
    }

    public function test_skip_files_option()
    {
        $this->artisan(
            'sync:pull',
            [
                '--skip-files' => true
            ]
        )->assertExitCode(0);
    }
}
