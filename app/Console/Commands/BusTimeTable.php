<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helper\Tools;
class BusTimeTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:bus-time-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate XML to DB';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $tools = new Tools();

    }
}
