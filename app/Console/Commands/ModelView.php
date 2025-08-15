<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\User;

class ModelView extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:model-view';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        foreach (User::all() as $user) {

            $this->info($user->name . ' - ' . $user->email);
        }
    }
}
