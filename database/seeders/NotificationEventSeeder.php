<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;
use App\Models\NotificationEvent;

class NotificationEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('notification_events')->insert([
			[
	            'mailable_class' => 'App\Mail\AccountCreateEmail',
	            'event' => 'Create Account',
        	],

        	[
        		'mailable_class' => 'App\Mail\ResetPasswordEmail',
	            'event' => 'Reset Password',
        	],

            [
                'mailable_class' => 'App\Mail\SupportContact',
                'event' => 'Support Contact',
            ],

            [
                'mailable_class' => 'App\Mail\CertificateMail',
                'event' => 'Learning Path Certificate',
            ],

            [
                'mailable_class' => 'App\Mail\ResetPoint',
                'event' => 'Reset Point',
            ],

            [
                'mailable_class' => 'App\Mail\HideThread',
                'event' => 'Hide Thread',
            ],

            [
                'mailable_class' => 'App\Mail\DeleteHideThread',
                'event' => 'Delete Hidden Thread',
            ],

            [
                'mailable_class' => 'App\Mail\UserProgressEmail',
                'event' => 'User Progress Email',
            ],

            [
                'mailable_class' => 'App\Mail\HideComment',
                'event' => 'Hide Comment',
            ],

            [
                'mailable_class' => 'App\Mail\HideThreadSuperadmin',
                'event' => 'Hide Thread for Superadmin',
            ],

            [
                'mailable_class' => 'App\Mail\HideReportCommentSuperadmin',
                'event' => 'Report and Hide Comment for Superadmin',
            ],

            [
                'mailable_class' => 'App\Mail\ReportCommentSuperadmin',
                'event' => 'Report Comment for Superadmin',
            ],

            [
                'mailable_class' => 'App\Mail\ReportThreadSuperadmin',
                'event' => 'Report Thread for Superadmin',
            ],


        ]);
    }
}
