<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Spatie\MailTemplates\TemplateMailable;


class SupportContact extends TemplateMailable
{
    use Queueable, SerializesModels;
    protected static $templateModelClass = UserMailTemplate::class;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $team;
    public $category; 
    public $customer_name;
    public $customer_email;
    public $customer_query;
    protected $mailTemplateId;

    public function __construct($team, $category, $customer_name, $customer_email, $customer_query, $mailTemplateId)
    {
        $this->team           = $team;
        $this->category       = $category;
        $this->customer_name  = $customer_name;
        $this->customer_email = $customer_email;
        $this->customer_query = $customer_query;
        $this->mailTemplateId = $mailTemplateId;
    }
    /**
     * Build the message.
     *
     * @return $this
     */

    public function getHtmlLayout(): string
    {
        /** 
         * In your application you might want to fetch the layout from an external file or Blade view.
         * 
         * External file: `return file_get_contents(storage_path('mail-layouts/main.html'));`
         * 
         * Blade view: `return view('mailLayouts.main', $data)->render();`
         */
        
        return '{{{ body }}}';
    }

    public function getTemplateId(): int
    {
        return $this->mailTemplateId;
    }

}
