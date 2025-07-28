<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Spatie\MailTemplates\TemplateMailable;
use App\Models\TemplateData;


class ResetPasswordEmail extends TemplateMailable
{
    use Queueable, SerializesModels;
    protected static $templateModelClass = UserMailTemplate::class;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $name;
    public $url;
    protected $mailTemplateId;

    public function __construct($name, $url, $mailTemplateId)
    {
        $this->name = $name;
        $this->url = $url;
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
