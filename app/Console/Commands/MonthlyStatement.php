<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MonthlyStatement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monthly:statement';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate user monthly statement of account';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        return 0;
    }
}
