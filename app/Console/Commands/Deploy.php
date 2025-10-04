<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Deploy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:deploy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the necessary artisan commands following the application deployment';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        // Run the database migrations
        $this->call('migrate');

        // Generate API documentation
        $this->call('scribe:generate');

        // Disable cache on local environment
        if (config('app.env') !== 'local') {

            // Remove the cached bootstrap files
            $this->call('optimize:clear');

            // Cache the framework bootstrap files
            $this->call('optimize');
        }

        // Seed permissions
        $this->call('db:seed', ['--class' => 'PermissionSeeder']);
    }
}
