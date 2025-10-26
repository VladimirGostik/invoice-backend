<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:install {--skip-seed : Skip database seeding}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the application (migrate, seed, generate docs)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting installation...');

        // Clear all caches
        $this->info('🧹 Clearing caches...');
        $this->call('optimize:clear');

        // Run migrations
        $this->info('📊 Running migrations...');
        try {
            // ✅ Pridaný --force flag pre non-interactive mode v Dockeri
            $this->call('migrate:fresh', ['--force' => true]);
        } catch (\Exception $e) {
            $this->error('❌ Migration failed: ' . $e->getMessage());
            $this->info('🔄 Trying regular migrate...');
            $this->call('migrate', ['--force' => true]);
        }

        // Run seeders
        if (!$this->option('skip-seed')) {
            $this->info('🌱 Seeding database...');
            $this->call('db:seed', ['--force' => true]);
        }

        // Generate API documentation
        $this->info('📚 Generating API documentation...');
        try {
            $this->call('scribe:generate');
        } catch (\Exception $e) {
            $this->error('⚠️  Documentation generation failed: ' . $e->getMessage());
            $this->warn('Continuing installation...');
        }

        // Optimize application
        $this->info('⚡ Optimizing application...');
        $this->call('optimize');

        $this->info('✅ Installation completed!');

        return 0;
    }
}
