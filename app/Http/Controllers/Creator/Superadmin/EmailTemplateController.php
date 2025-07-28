<?php

namespace App\Http\Controllers\Creator\Superadmin;

use Mail;
use Auth;
use App\Models\{ NotificationEvent, Country, UserMailTemplate, MailTemplateConfig, Region };
use Spatie\MailTemplates\Models\MailTemplate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\AccountCreateEmail;
use Illuminate\Support\Facades\Log;
use Spatie\MailTemplates\TemplateMailable;
use Yajra\DataTables\DataTables;


class EmailTemplateController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){
            $search = trim($request->query('search'));
            $query = UserMailTemplate::select('id', 'subject', 'html_template', 'created_at');

            if (!empty($search)) {
                $query = $query->where('subject', 'like', '%' . $search . '%');
            }

            $userTemplates = $query->orderBy('subject')->get();
            return Datatables::of($userTemplates, $request)
            ->addIndexColumn()
            ->editColumn('subject', function ($userTemplates){
                return '<div class="template"><svg role="img"  viewBox="0 0 512 512"><path fill="currentColor" d="M464 64H48C21.49 64 0 85.49 0 112v288c0 26.51 21.49 48 48 48h416c26.51 0 48-21.49 48-48V112c0-26.51-21.49-48-48-48zm0 48v40.805c-22.422 18.259-58.168 46.651-134.587 106.49-16.841 13.247-50.201 45.072-73.413 44.701-23.208.375-56.579-31.459-73.413-44.701C106.18 199.465 70.425 171.067 48 152.805V112h416zM48 400V214.398c22.914 18.251 55.409 43.862 104.938 82.646 21.857 17.205 60.134 55.186 103.062 54.955 42.717.231 80.509-37.199 103.053-54.947 49.528-38.783 82.032-64.401 104.947-82.653V400H48z"></path></svg> <div> <h5>' . $userTemplates->subject .'</h5>'. $userTemplates->html_template.'</div></div>';
           })
            ->addColumn('action', function($userTemplates){
                $href = 'email-templates/'. $userTemplates->id;
                $editHref = 'email-templates/'. $userTemplates->id . '/edit';
                $edit = \Lang::get('lang.edit');
                $delete = \Lang::get('lang.delete');
                $actionBtn = "<a href='$editHref'><i class='fa fa-pencil' aria-hidden='true'></i> &nbsp;$edit </a><button type='button' class='text-danger delete-user' data-href='$href' data-role='superadmin'><i class='fa fa-trash' aria-hidden='true'></i> $delete </button>";
                return $actionBtn;
            })
            ->rawColumns(['action'])
            ->escapeColumns([])
            ->make(true);
        }
        return view('creator.superadmin.template-data.index'); 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $events = NotificationEvent::orderBy('event')->get();
        $countries = Country::select('id', 'name')->where('status', 1)->orderBy('name')->get();
        $variables = AccountCreateEmail::getVariables(); 
        $showVaraibale = "{{variable}}";
      
        return view('creator.superadmin.template-data.create', compact(['events', 'countries', 'variables', 'showVaraibale']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        request()->validate([
            'event_id'         => 'required|exists:notification_events,id',
            'country'          => 'required',
            'template_layout'  => 'required',
            'subject'          => 'required'
            ]);

            try
            {
                if ($request->country[0] != -1)
                {
                    $this->validate($request, [
                        'region' => 'required'
                    ]);
                }

                $mailable = NotificationEvent::where('id', $request->event_id)->select('mailable_class')->first();
                $existTemplates = $this->mailTemplateData ($request, $mailable);
                if($existTemplates == true)
                {
                    return redirect()->back()->with('error',\Lang::get('lang.email-template-alreday-exist-for-selected-region-or-country'));
                }

                $userMailTemplate                = new UserMailTemplate;
                $userMailTemplate->mailable      = $mailable->mailable_class;
                $userMailTemplate->subject       = $request->subject;
                $userMailTemplate->country_id    = 1;
                $userMailTemplate->html_template = $request->template_layout;
                $userMailTemplate->save();

                foreach ($request->country as $country)
                {
                    if(isset($request->region) && ($request->region[0] != -1))
                    {
                        $countryRegions = Region::where('country_id', $country)->pluck('id')->toArray();
                        foreach ($request->region as $region)
                        {
                            if(in_array($region, $countryRegions)) {
                                $mailTemplateConfig = new MailTemplateConfig;
                                $mailTemplateConfig->country_id = $country;
                                $mailTemplateConfig->region_id = $region;
                                $mailTemplateConfig->template_id = $userMailTemplate->id;
                                $mailTemplateConfig->save();
                            }
                        }

                    } else {
                        $mailTemplateConfig = new MailTemplateConfig;
                        $mailTemplateConfig->country_id = $country;
                        $mailTemplateConfig->region_id = -1;
                        $mailTemplateConfig->template_id = $userMailTemplate->id;
                        $mailTemplateConfig->save();
                    }
                }
        }
        catch (Exception $ex){
            Log::error($ex);
        }
        return redirect('/superadmin/email-templates')->with('success',\Lang::get('lang.email-template').' '.\Lang::get('lang.created-successfully'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $templateData  = UserMailTemplate::find($id);
        $events        = NotificationEvent::orderBy('event')->get();
        $countries     = Country::select('id', 'name')->where('status', 1)->orderBy('name')->get();
        $variables     = $templateData->mailable::getVariables();
        $showVaraibale = "{{variable}}";
        $selectedRegion = 0;

        $oldMailTemplate = MailTemplateConfig::where('template_id', $id)->get();

        if(count($oldMailTemplate) > 0)
        {
            $templateCountry = MailTemplateConfig::where('template_id', $id)->pluck('country_id')->toArray();
            $templateRegion  = MailTemplateConfig::where('template_id', $id)->pluck('region_id')->toArray();

            $regions = Region::where('status', 1)->whereIN('country_id', $templateCountry)->orderBy('name')->get();

            $displayCountry = (count(array_unique($templateCountry)) > 1) ? count(array_unique($templateCountry)) . ' Selected' : ($templateCountry[0] == -1 ? \Lang::get('lang.all') : Country::where('id', (int)$templateCountry[0])->pluck('name')->first());

            // display region text
            if(count($templateRegion) > 1)
            {
                $dispalyRegion = (array_unique($templateRegion) === array('-1')) ? \Lang::get('lang.all') : count($templateRegion). ' Selected';

            } else if($templateRegion[0] == -1) {
                $dispalyRegion = \Lang::get('lang.all');
            } else {
               $dispalyRegion = Region::where('id', (int)$templateRegion[0])->pluck('name')->first();
            }

        } else {
            $regions = Region::where('status', 1)->where('country_id', $templateData->country_id)->get();
            $displayCountry = Country::where('id',  $templateData->country_id)->pluck('name')->first();
            $templateCountry = Country::where('id',  $templateData->country_id)->pluck('id')->toArray();
            $templateRegion = [];
            $dispalyRegion = \Lang::get('lang.select-region');
        }

        return view('creator.superadmin.template-data.edit', compact(['events', 'templateData', 'countries', 'variables', 'showVaraibale', 'templateCountry', 'regions', 'templateRegion', 'displayCountry', 'dispalyRegion']));    
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        request()->validate([
            'id'               => 'required|exists:user_mail_templates,id',
            'event_id'         => 'required|exists:notification_events,id',
            'country'          => 'required',
            'template_layout'  => 'required',
            'subject'          => 'required'
        ]);

        try
        {
            // delete existing record
            MailTemplateConfig::where('template_id', $id)->delete();

            $mailable = NotificationEvent::where('id', $request->event_id)->select('mailable_class')->first();
            $existTemplates = $this->mailTemplateData ($request, $mailable);
            if($existTemplates == true)
            {
                return redirect()->back()->with('error',\Lang::get('lang.email-template-alreday-exist-for-selected-region-or-country'));
            }

            foreach ($request->country as $country)
            {
                if(isset($request->region) && ($request->region[0] != -1))
                {
                    $countryRegions = Region::where('country_id', $country)->pluck('id')->toArray();
                    foreach ($request->region as $region)
                    {
                        if(in_array($region, $countryRegions)) {
                            $mailTemplateConfig = new MailTemplateConfig;
                            $mailTemplateConfig->country_id = $country;
                            $mailTemplateConfig->region_id = $region;
                            $mailTemplateConfig->template_id = $id;
                            $mailTemplateConfig->save();
                        }
                    }

                } else {
                    $mailTemplateConfig = new MailTemplateConfig;
                    $mailTemplateConfig->country_id = $country;
                    $mailTemplateConfig->region_id = -1;
                    $mailTemplateConfig->template_id = $id;
                    $mailTemplateConfig->save();
                }
            }

            $update = [
                'mailable'      => $mailable->mailable_class,
                'subject'       => $request->subject,
                'html_template' => $request->template_layout
            ];

            UserMailTemplate::where('id', $id)->update($update);
        } catch (Exception $ex){
            Log::error($ex);
        }
        return redirect('/superadmin/email-templates')->with('success',\Lang::get('lang.email-template').' '.\Lang::get('lang.updated-successfully'));
    }

    /**
     * Remove the specified resource
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $userMailTemplate = UserMailTemplate::find($id);  
        $isdeleted = $userMailTemplate->delete();

        return redirect()->back()
                         ->with('success', \Lang::get('lang.email-template').' '.\Lang::get('lang.deleted-successfully')); 

    }

    /**
     * get varaible list acording to event in mail_template table.
     *
     * @param  int  $event
     * @return \Illuminate\Http\Response
     */
    public function getVaraibles ($event)
    {
        $mailable = NotificationEvent::where('id', $event)->first();
        $variables = $mailable->mailable_class::getVariables();   
        return $variables;
    }

    public function mailTemplateData (Request $request, $mailable )
    {
        if(isset($request->region))
        {
            $region = $request->region;
        } else {
            $region[] = -1;
        }

        // $region = $request->region ? $request->region : -1 ;
        $templates = UserMailTemplate::where('mailable', $mailable->mailable_class)->pluck('id');

        // check if template exist for all country and region
        $where = ['country_id' => -1, 'region_id' => -1];
        $allCountryTemplate = MailTemplateConfig::whereIN('template_id', $templates)->where($where)->get();

        if(count($allCountryTemplate) > 0)
        {
           return true;
        }

        // check if requested region is -1
        $templatesRecord = MailTemplateConfig::whereIN('template_id', $templates)->whereIn('country_id', $request->country)->pluck('region_id')->toArray();
        if((count($templatesRecord) > 0) && (in_array('-1', $region)))
        {
            return true;
        }

        // check if template already have seleted region
        $templatesRecord = MailTemplateConfig::whereIN('template_id', $templates)->whereIn('country_id', $request->country)->where(function ($query) use ($region) {
                            $query->whereIn('region_id', $region)
                                ->orWhere('region_id', '-1');
                          })->pluck('region_id')->toArray();

        if(count($templatesRecord) > 0) {
            return true;
        } else {
            return $templatesRecord;
        }

    }
}
