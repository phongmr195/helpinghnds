<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Api\UserService;

class SetOfflineWorkerWithoutJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'worker:set-offline-status-without-job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set offline status for worker when work more than hours without job!';

    protected $userService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(UserService $userService)
    {
        parent::__construct();
        $this->userService = $userService;
    }

    /**
     * Execute the console command.
     *
     * @return string
     */
    public function handle()
    {
        $this->userService->setOfflineStatusWorkerWithoutJob();
        $this->info('Command worker:set-offline-status-without-job run successfully!');
    }
}
