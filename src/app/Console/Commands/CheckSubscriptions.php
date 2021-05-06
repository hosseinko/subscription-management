<?php

namespace App\Console\Commands;

use App\Enums\OsTypes;
use App\Libs\Services\SubscriptionService;
use Illuminate\Console\Command;

/**
 * Class CheckSubscriptions
 * @package App\Console\Commands
 */
class CheckSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check {os}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check subscriptions of apps of given os';

    private $subscriptionService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(SubscriptionService $subscriptionService)
    {
        parent::__construct();

        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $os = $this->argument('os');
        if (!in_array($os, OsTypes::toArray())) {
            $this->error('Invalid os');
            exit(0);
        }

        $lockFiles = [];
        if (!$this->lock($os, $lockFiles)) {
            $this->info('Another instance is running');
            exit(0);
        }

        try {
            $this->subscriptionService->checkSubscriptions($os);

            $this->line('Subscriptions updated successfully');
        } catch (\Throwable $exception) {
            $this->error($exception->getMessage());
        }

        $this->lock($os, $lockFiles, false);

        return 0;
    }

    /**
     * @param $action
     * @param $fp
     * @param bool $lock
     * @return bool
     */
    private function lock($action, &$fp, bool $lock = true): bool
    {
        $lockFilePath = storage_path('locks');
        if (!file_exists($lockFilePath)) {
            mkdir($lockFilePath, 0777, true);
            chown($lockFilePath, 1000);
            chgrp($lockFilePath, 1000);
        }

        $filename    = storage_path("locks/$action.lock");
        $fp[$action] = fopen($filename, 'w');

        if (!$lock) {
            fclose($fp[$action]);
            unset($fp[$action]);

            return true;
        }

        return flock($fp[$action], LOCK_EX | LOCK_NB);
    }
}
