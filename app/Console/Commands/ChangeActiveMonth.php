<?php

namespace App\Console\Commands;

use Throwable;
use App\Models\Month;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ChangeActiveMonth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'change-active-month';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change Active Month';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        try{
            $current_active_month = Month::where('active',1)->first();
            $current_active_month->active=0;
            $current_active_month->save();
            $get_month = Carbon::now()->month;
            $new_active_month = Month::where('number',$get_month)->first();
            $new_active_month->active=1;
            $new_active_month->save();
            $output = "Active month updated successfully.";
            Log::info($output);
        }
        catch(Throwable $e){
            $output = "Active month not updated!";
            Log::error($output);      
        }
        session()->flash('command_output', $output);
    }
}
