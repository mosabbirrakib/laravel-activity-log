<?php

namespace AlMosabbirRakib\ActivityLog\Console\Commands;

use AlMosabbirRakib\ActivityLog\Models\ActivityLog;
use Illuminate\Console\Command;

class CleanActivityLogsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activity-log:clean 
                            {--days= : Number of days to keep logs (default: from config)}
                            {--force : Force the operation without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean old activity logs based on retention policy';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $days = $this->option('days') ?? config('activity-log.retention_days');

        if (!$days) {
            $this->error('No retention days specified. Set it in config or use --days option.');
            return 1;
        }

        $date = now()->subDays($days);
        
        $count = ActivityLog::where('created_at', '<', $date)->count();

        if ($count === 0) {
            $this->info('No activity logs to clean.');
            return 0;
        }

        $this->info("Found {$count} activity logs older than {$days} days.");

        if (!$this->option('force')) {
            if (!$this->confirm('Do you want to delete these logs?')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        $deleted = ActivityLog::where('created_at', '<', $date)->delete();

        $this->info("Successfully deleted {$deleted} activity logs.");

        return 0;
    }
}

