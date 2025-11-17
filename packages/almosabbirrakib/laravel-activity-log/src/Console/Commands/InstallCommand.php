<?php

namespace AlMosabbirRakib\ActivityLog\Console\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activity-log:install 
                            {--force : Overwrite existing files}
                            {--views : Publish views only}
                            {--config : Publish config only}
                            {--migrations : Publish migrations only}
                            {--components : Publish Vue components only}
                            {--all : Publish all assets}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Activity Log package';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Installing Activity Log Package...');
        $this->newLine();

        $force = $this->option('force');
        $publishAll = $this->option('all') || (!$this->option('views') && !$this->option('config') && !$this->option('migrations') && !$this->option('components'));

        // Publish config
        if ($publishAll || $this->option('config')) {
            $this->comment('Publishing configuration...');
            $this->call('vendor:publish', [
                '--tag' => 'activity-log-config',
                '--force' => $force,
            ]);
        }

        // Publish migrations
        if ($publishAll || $this->option('migrations')) {
            $this->comment('Publishing migrations...');
            $this->call('vendor:publish', [
                '--tag' => 'activity-log-migrations',
                '--force' => $force,
            ]);
        }

        // Publish views
        if ($publishAll || $this->option('views')) {
            $this->comment('Publishing views...');
            $this->call('vendor:publish', [
                '--tag' => 'activity-log-views',
                '--force' => $force,
            ]);
        }

        // Publish Vue components
        if ($publishAll || $this->option('components')) {
            $this->comment('Publishing Vue components...');
            $this->call('vendor:publish', [
                '--tag' => 'activity-log-components',
                '--force' => $force,
            ]);
        }

        // Publish assets
        if ($publishAll) {
            $this->comment('Publishing assets...');
            $this->call('vendor:publish', [
                '--tag' => 'activity-log-assets',
                '--force' => $force,
            ]);
        }

        $this->newLine();
        $this->info('Activity Log package installed successfully!');
        $this->newLine();

        $this->comment('Next steps:');
        $this->line('1. Run migrations: php artisan migrate');
        $this->line('2. Configure the package in config/activity-log.php');
        $this->line('3. Add the LogsActivity trait to your models');
        $this->line('4. Visit /activity-logs to view the logs');
        $this->newLine();

        return 0;
    }
}

