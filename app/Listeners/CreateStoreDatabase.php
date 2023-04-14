<?php

namespace App\Listeners;

use App\Events\StoreCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class CreateStoreDatabase
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(StoreCreated $event): void
    {
        $store = $event->store;
        $db = "tenancy_store_{$store->id}";
        $store->database_options = [
            'dbname' => $db,
        ];
        $store->save();
        DB::statement("CREATE DATABASE `$db`");
        Config::set('database.connections.tenant.database', $db);
        //config(['database.connections.mysql.database' => $db]);
        DB::purge('tenant');
        DB::reconnect('tenant');
        $dir = new \DirectoryIterator(database_path('migrations/tenants'));
        foreach ($dir as $file) {
            if ($file->isFile()) {
                Artisan::call('migrate',[
                    '--database' => 'tenant',
                    '--path' => 'database/migrations/tenants' . '/' . $file->getFilename(),
                    '--force' => true,
                ]);
            }
        }
    }
}
