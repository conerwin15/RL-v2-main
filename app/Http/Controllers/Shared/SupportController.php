<?php

namespace App\Http\Controllers\Shared;

use DB;
use Config; 
use Auth;
use Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{ Role, ContactCategory, MailTemplateConfig };
use Illuminate\Support\Facades\Mail;
use Spatie\MailTemplates\Models\MailTemplate;
use Spatie\MailTemplates\TemplateMailable;
use App\Mail\SupportContact;

class SupportController extends Controller
{
    public function create()
    {
        $routeSlug = $this->getRouteSlug();
        $user = Auth::user();
        $role = $user->roles->first()->id;
        $contactCategories = ContactCategory::where('role_id', $role)->orwhereNull('role_id')->get();
        return view('shared.support-mail.create', compact('contactCategories', 'routeSlug'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $this->validate($request, [
            'id' => 'required',   
            'categoryName'  => 'required',
            'text'  => 'required|min:6', 
        ]);

        $sendTime = date('Y-m-d H:i:s');
        $supportName  = Config::get('constant.support_name');
        $supportEmail = ContactCategory::where('id', $request->id)->select('email')->first();
        
        $country[] = $user->country_id;
        array_push($country, "-1");

        $region[] = $user->region_id;
        array_push($region, "-1");

        $existMailTemplate = DB::table('user_mail_templates')
                            ->join('mail_template_config', 'user_mail_templates.id', '=', 'mail_template_config.template_id')
                            ->whereIN('mail_template_config.country_id', $country)
                            ->whereIN('mail_template_config.region_id', $region)
                            ->where('user_mail_templates.mailable', 'App\Mail\SupportContact')->first();

        if($existMailTemplate != null) {
            Mail::to(trim($supportEmail->email))
            ->send(new \App\Mail\SupportContact($supportName, $request->categoryName, $user->name, $user->email, $request->text, $existMailTemplate->template_id));
            return back()->with('success', \Lang::get('lang.mail-send')); 

        } else {
            return back()->with('error', \Lang::get('lang.mail-not-send')); 
        }
    }

    protected function getRouteSlug() {
        $user = Auth::user();
        return $user->getRoleNames()->first();
    }
}
