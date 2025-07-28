<?php

namespace App\Mail;

use Illuminate\Database\Eloquent\Builder;
use App\Models\{ Country, MailTemplateConfig };
use Spatie\MailTemplates\Models\MailTemplate;
use Illuminate\Contracts\Mail\Mailable;
use Spatie\MailTemplates\Interfaces\MailTemplateInterface;

class UserMailTemplate extends MailTemplate implements MailTemplateInterface {

    public function scopeForMailable(Builder $query, Mailable $mailable): Builder
    {
        return $query->where('id', $mailable->getTemplateId());
    }

    public function getHtmlLayout(): string
    {
        return '{{ { body }}}';
    }

}
