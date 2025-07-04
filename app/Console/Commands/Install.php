<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:install {--with-keys : Generate application key and JWT secret} {--full-install : Run full installation including migrations, seeding, storage link, and API documentation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the application';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->call('optimize:clear');

        if ($this->option('with-keys')) {
            $this->call('key:generate');

            $this->call('jwt:secret', ['--force' => true]);
        }

        if ($this->option('full-install')) {

            $this->call('migrate', ['--force' => true]);
            $this->call('db:seed');
            $this->call('storage:link');
            $this->call('scribe:generate');

            $this->call('optimize');
            $this->call('optimize:clear');
        } else {

            $this->call('migrate:fresh');
            $this->call('db:seed');
            $this->call('scribe:generate');
            
            $this->call('optimize:clear');
            $this->call('optimize');
        }

        $this->info('Installation completed successfully!');
    }
}