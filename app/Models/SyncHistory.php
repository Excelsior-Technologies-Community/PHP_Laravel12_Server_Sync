<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncHistory extends Model
{
    protected $fillable = [
        'database_synced',
        'files_synced',
        'files_count'
    ];
}