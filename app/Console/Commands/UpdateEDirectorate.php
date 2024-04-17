<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Jobs\UpdateEDirectorateJob;
use Illuminate\Support\Facades\Log;

class UpdateEDirectorate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-e-directorate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tell eDirectorate API to update its data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $schools_update_date = DB::table('last_update_schools')->first();
        $teachers_update_date = DB::table('last_update_teachers')->first();
        if(DB::table('last_update_edirectorate')->first() == null or DB::table('last_update_edirectorate')->first()<max($schools_update_date,$teachers_update_date)){
            dispatch(new UpdateEDirectorateJob('Artisan'));
            $output = "eDirectorate update added to queue";
        }
        else{
            $output = "eDirectorate already up to date";
        }
        Log::channel('commands_executed')->info("Cron Job: ".$output);
    }
}
