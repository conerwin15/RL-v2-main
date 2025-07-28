<?php

namespace App\Console\Commands;

use Log;
use DB;
use Illuminate\Console\Command;
use App\Models\User;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Spatie\MailTemplates\Models\MailTemplate;
use Spatie\MailTemplates\TemplateMailable;
use App\Mail\UserProgressEmail;

class UserProgressEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'userprogressemail:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send User Course Progress Email';

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
        Log::debug('Running User Course Progress Email' . Carbon::now()->month);

        try {

            // // assigned 2 weeks ago 
            $eligibleLearningPaths = DB::select('select u.id, GROUP_CONCAT(lp.name SEPARATOR "<>") as learning_path, u.name, u.country_id, u.email from user_learning_paths ulp 
                                                join learning_paths lp on ulp.learning_path_id = lp.id 
                                                join users u on ulp.user_id = u.id 
                                                where date(ulp.created_at) < curdate() and (ulp.progress_percentage < 100 or ulp.progress_percentage is null) group by u.id, u.name, u.email');

            foreach($eligibleLearningPaths as $r) {
                try {
                    $learningPaths = explode("<>", $r->learning_path);
                    $names = "<ul>";
                    foreach($learningPaths as $learningPath) {
                        $names = $names . "<li>" . $learningPath . "</li>";
                    }
                    $names = $names . "</ul>";
                    Mail::to($r->email)
                        ->send(new \App\Mail\UserProgressEmail(ucfirst($r->name), $names, $r->country_id));
                    Log::debug('Mail Sent successfully to user >>> ' . $r->email);
                } catch(Exception $ex) {
                    Log::error($ex);
                }
            }
            
        } catch(Exception $ex) {
            Log::error($ex);
        }

        return 0;
    }
}
