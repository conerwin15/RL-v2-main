<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Exception;
use Carbon\Carbon;
use App\Models\FeaturedTrainee;
use Illuminate\Support\Facades\Log;

class FeaturedTraineeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'featuredtrainee:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Featured Trainee for the month';

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
        Log::debug('Running Featured Trainee for month ' . Carbon::now()->month);
        try {

            $featuredTrainee = DB::select(DB::raw('select user_id, sum(points) as points from user_point_history where 
                                    month(created_at)=MONTH(CURRENT_TIMESTAMP) and year(created_at)=YEAR(CURRENT_TIMESTAMP) group by user_id
                                    order by points desc limit 1'));

            if(count($featuredTrainee) > 0) {
                FeaturedTrainee::create([
                    "user_id" => $featuredTrainee[0]->user_id,
                    "points" => $featuredTrainee[0]->points,
                    "month" => Carbon::now()->month,
                    "year" => Carbon::now()->year
                ]);
            }
           
            Log::debug('Featured Trainee Assigned to user id :: ' . $featuredTrainee['user_id']);

        } catch(Exception $ex) {
            Log::error($ex);
        }
      

        Log::info('Featured Trainee is not Assigned.');
        return 0;
    }
}
