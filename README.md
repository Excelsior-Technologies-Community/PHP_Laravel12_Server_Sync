# PHP_Laravel12_Server_Sync

## Introduction

PHP_Laravel12_Server_Sync is a Laravel 12 based synchronization system that helps developers synchronize database backups and storage files from a simulated remote environment into a local Laravel application. The project demonstrates how synchronization workflows can be implemented using Laravel Artisan Commands, Service Classes, and configuration-driven architecture.

Since local development environments using XAMPP typically do not have access to production servers through SSH, this project uses a local remote-server simulation folder to mimic real-world synchronization behavior. It provides a practical example of database backup synchronization, file synchronization, and automated testing while following Laravel 12 best practices.

---

## Features


### Database Sync

- Database backup copy

- Database restore support

- Skip database option


### File Sync

- Storage file synchronization

- Images/documents sync

- Delete missing files option


### Artisan Command

Normal:

```bash
php artisan sync:pull
```

### Skip Database:

```bash
php artisan sync:pull --skip-db
```

### Skip Files:

```bash
php artisan sync:pull --skip-files
```

### Delete Files:


```bash
php artisan sync:pull --delete
```

---

## Requirements

- PHP 8.3+
- Laravel 12
- Composer
- XAMPP
- MySQL

---

# Installation

## Step 1: Create Laravel 12 Project

```bash
composer create-project laravel/laravel PHP_Laravel12_Server_Sync "12.*"
```

Go inside project:

```bash
cd PHP_Laravel12_Server_Sync
```

Generate key:

```bash
php artisan key:generate
```

---

## Step 2: Configure Database

Open .env

Update:

```.env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel12_server_sync
DB_USERNAME=root
DB_PASSWORD=
```

Run migration:

```bash
php artisan migrate
```

---

## Step 3: Create Command

Create command:

```bash
php artisan make:command SyncPullCommand
```

File: app/Console/Commands/SyncPullCommand.php

```php
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
```

---

## Step 4: Create services folder

File: app/Services

Create:

```bash
ServerSyncService.php

DatabaseSyncService.php

FileSyncService.php
```

### ServerSyncService.php


File: app/Services/ServerSyncService.php

```php
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
```

### DatabaseSyncService.php

File: app/Services/DatabaseSyncService.php


```php
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
```

### FileSyncService.php

File: app/Services/FileSyncService.php

```php
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
```

---

## Step 5: Create Provider

Create:

File: app/Providers/ServerSyncServiceProvider.php

```php
<?php


namespace App\Providers;


use Illuminate\Support\ServiceProvider;



class ServerSyncServiceProvider

extends ServiceProvider
{


    public function register() {}



    public function boot()
    {


        $this->commands([

            \App\Console\Commands\SyncPullCommand::class

        ]);
    }
}
```

---

## Step 6: Remote Folder Setup

Create:

File: storage/sync/remote


Structure:

```text
storage
└── sync
    └── remote
        ├── database
        │   └── backup.sql
        └── files
            ├── images
                └── demo.jpg
```

This folder acts as a simulated Production Server.


### Create Sample Database Backup

Create file:

```text
storage/sync/remote/database/backup.sql
```

Add sample content:

```sql
CREATE TABLE demo_sync (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100)
);

INSERT INTO demo_sync(name)
VALUES ('Laravel Server Sync Demo');
```

This file simulates a production database backup.

### Create Sample File

Create file:

```text
storage/sync/remote/files/images/demo.jpg
```

You can place any image file inside this folder.

Final structure:

```text
storage
└── sync
    └── remote
        ├── database
        │   └── backup.sql
        └── files
            └── images
                └── demo.jpg
```

The database backup and image file will be used as source data during the synchronization process.

---

## Step 7: Testing and Verification

### PHPUnit Test

This project includes unit tests similar to the original Laravel Server Sync package.

### Test Location

```text
tests/Unit/SyncPullCommandTest.php
```

### Create Test

```bash
php artisan make:test SyncPullCommandTest --unit
```

### File

```text
tests/Unit/SyncPullCommandTest.php
```

### Code

```php
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
```

---

## Step 8: Run Synchronization Command

Execute the synchronization command to copy the database backup and storage files from the simulated remote server into the local Laravel application.

### Run Command

```bash
php artisan sync:pull
```

### Expected Output

```text
Starting Server Sync...
Database sync completed
Files sync completed
Sync completed successfully
```

### Verify Database Synchronization

Check that the backup file has been copied successfully:

```text
storage/sync/dumps/backup.sql
```

### Verify File Synchronization

Check that the image file has been copied successfully:

```text
storage/app/images/demo.jpg
```

### Run PHPUnit Tests

Execute:

```bash
php artisan test
```

### Expected Output

```text
PASS  Tests\Unit\SyncPullCommandTest

✓ sync command runs successfully
✓ skip database option
✓ skip files option
```

### Additional Command Options

Skip Database Synchronization:

```bash
php artisan sync:pull --skip-db
```

Skip File Synchronization:

```bash
php artisan sync:pull --skip-files
```

Delete Existing Files Before Sync:

```bash
php artisan sync:pull --delete
```

---

## Screenshots

<img width="1915" height="936" alt="Screenshot 2026-06-18 112943" src="https://github.com/user-attachments/assets/2312deff-a8bf-4a54-ac04-db910738635c" />

---

## Project Structure 

```text
PHP_Laravel12_Server_Sync
├── app
│   ├── Console
│   │   └── Commands
│   │       └── SyncPullCommand.php
│   ├── Services
│   │   ├── ServerSyncService.php
│   │   ├── DatabaseSyncService.php
│   │   └── FileSyncService.php
│   └── Providers
│       └── ServerSyncServiceProvider.php
├── config
│   └── server-sync.php
├── storage
│   └── sync
│       ├── remote
│       │   ├── database
│       │   │   └── backup.sql
│       │   └── files
│       │       └── images
│       │           └── demo.jpg
│       └── dumps
│           └── backup.sql
├── tests
│   └── Unit
│       └── SyncPullCommandTest.php
├── .env
├── artisan
├── composer.json
├── composer.lock
├── package.json
├── phpunit.xml
└── README.md
```

---

## Conclusion

PHP_Laravel12_Server_Sync successfully demonstrates a simple and practical synchronization workflow in Laravel 12, showcasing Artisan Commands, Service Layer Architecture, File Operations, Configuration Management, and PHPUnit Testing within a local development environment.


