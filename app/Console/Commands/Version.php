<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Version extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:version';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Версия проекта';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('RealTracker version ' . getAppVersion());
    }
}
