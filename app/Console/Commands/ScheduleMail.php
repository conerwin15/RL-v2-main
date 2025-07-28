<?php

namespace App\Console\Commands;

use DB;
use Mail;
use Exception;
use Carbon\Carbon;
use App\Models\ScheduledEmailJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;

class ScheduleMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scheduledemail:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'schedule mails for users';

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
        Log::debug('Running schedule mails for once or repeat ' . Carbon::now()->format('Y-m-d H:i:s'));
        try {

            $current_date = date('Y-m-d H:i:s');
            $scheduleMails = ScheduledEmailJob::with(['scheduleUsers' => function ($q){
                                $q->with('users');
                             }])->where('is_processed', false)->where('next_run_at', '<=', $current_date)->get();

            foreach ($scheduleMails as $scheduleMail ) {

                $subject = $scheduleMail->subject;
                $content = $scheduleMail->description;
                foreach ($scheduleMail->scheduleUsers as $learner) {

                    $userEmail = $learner->users->email;
                    Mail::raw($content, function ($m)  use ($userEmail, $subject) {
                        $m->to($userEmail);
                        $m->subject($subject);
                    });
                }

                if($scheduleMail->frequency == 'once')
                {
                    $update = [
                                'is_processed' => true,
                              ];
                } else {

                    $newNextRunAt = date('Y-m-d H:i:s', strtotime($scheduleMail->next_run_at. ' + '.$scheduleMail->frequency_amount.' ' .$scheduleMail->frequency_unit));

                    if($newNextRunAt > $scheduleMail->end_date)
                    {
                        $update = [
                                    'is_processed' => true,
                                ];
                    } else {
                        $update = [
                                    'next_run_at' => $newNextRunAt,
                                ];
                    }

                }

                $update = ScheduledEmailJob::where('id', $scheduleMail->id)->update($update);
            }


        } catch(Exception $ex) {
            Log::error($ex);
        }

        return 0;
    }
}
