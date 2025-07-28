<?php

namespace App\Http\Controllers\Api\Shared;

use DB;
use Log;
use Config; 
use Auth;
use Setting;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\{ Role, ContactCategory };
use Illuminate\Support\Facades\Mail;
use Spatie\MailTemplates\Models\MailTemplate;
use Spatie\MailTemplates\TemplateMailable;
use App\Mail\SupportContact;

class SupportController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function getSupportCategories()
     {
        try{

            $user = Auth::user();
            $role = $user->roles->first()->id;
            $contactCategories = ContactCategory::select('id', 'category_name')->where('role_id', $role)->orwhereNull('role_id')->get();

            $response['categories'] = $contactCategories;    
        
            return $this->sendResponse($response, \Lang::get('lang.support-category-list'));  
        } catch (Exception $e) {
            Log::error($e);
            return $this->sendError($e, \Lang::get('lang.invalid-request'));
        }  
     }
     
    public function supportMail(Request $request)
    {   
        try {
                request()->validate([
                    'categoryId' => 'required|exists:contact_categories,id',
                    'text'  => 'required|min:6'
                ]);

                $user = Auth::user();
                $sendTime = date('Y-m-d H:i:s');
                $supportName  = Config::get('constant.support_name');
                $supportEmail = ContactCategory::where('id', $request->categoryId)->select('category_name', 'email')->first();
    
                $country[] = $user->country_id;
                array_push($country, "-1");

                $region[] = $user->region_id;
                array_push($region, "-1");
                $templateConfig = DB::table('user_mail_templates')
                                ->join('mail_template_config', 'user_mail_templates.id', '=', 'mail_template_config.template_id')
                                ->whereIn('mail_template_config.country_id', $country)
                                ->whereIn('mail_template_config.region_id', $region)
                                ->where('user_mail_templates.mailable', 'App\Mail\SupportContact')->first();

                if($templateConfig != null) {
                    Mail::to(trim($supportEmail->email))
                        ->send(new \App\Mail\SupportContact( $supportName, $supportEmail->category_name, $user->name, $user->email, $request->text, $templateConfig->template_id));

                } else {
                    Log::warning('Mail template HideReportCommentSuperadmin does not exist for country ' .  $superadmin->country_id);
                }

                return $this->sendResponse(true, \Lang::get('lang.mail-send'));                               
            } catch (Exception $e) {
                Log::error($e);
                return $this->sendError($e, \Lang::get('lang.invalid-request'));
            }  
                                            
    }
}
