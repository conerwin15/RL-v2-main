<?php
  
namespace App\Models;
  
use Auth;  
use DB;
use Log;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Models\{ UserPointHistory, UserLearningPath };
use Illuminate\Support\Facades\Mail;
use Spatie\MailTemplates\Models\MailTemplate;
use Spatie\MailTemplates\TemplateMailable;
use Illuminate\Support\Arr;
  
class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, HasRoles;
    protected $guarded = [];
  
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'job_role_id',
        'country_id',
        'region_id',
        'group_id',
        'dealer_id',
        'created_by',
        'updated_by',
        'image',
        'remarks',
        'google_id'
    ];
  
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
  
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier() {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims() {
        return [];
    }  
    public function cart(){
        return $this->hasmany('App\Models\Cart','package_id');
    }

    public function model() {
        return $this->hasOne('App\Models\ModelHasRole','model_id');
    }

    public function jobRole() {

        return $this->belongsTo('App\Models\JobRole');
    }

    public function group() {

        return $this->belongsTo('App\Models\Group');
    }

    public function country() {
        return $this->belongsTo('App\Models\Country', 'country_id');
    }

    public function region() {
        return $this->belongsTo('App\Models\Region', 'region_id');
    }

    public function createdBy() {
         return $this->belongsTo('App\Models\User', 'created_by');
    }

    public function updatedBy() {
         return $this->belongsTo('App\Models\User', 'updated_by');
    }

    public function dealer() 
    {
       return $this->belongsTo('App\Models\User', 'dealer_id');
    }

    /*
    * All users with dealer id same as user id
    */
    public function dealerCustomers() 
    {
       return $this->hasMany('App\Models\User', 'dealer_id');
    }

    public function learningPaths()
    {
        return $this->belongsToMany(LearningPath::class, 'user_learning_paths', 'user_id', 'learning_path_id')
            ->withPivot('assign_by', 'created_at');
    }

    public function userLearningPaths() {
        return $this->hasMany(UserLearningPath::class, 'user_id', 'id');
    }

    public function newsPromotions()
    {
        return $this->belongsToMany(NewsPromotion::class);
    }

    public function salesTips()
    {
        return $this->belongsToMany(SalesTip::class);
    }
    
    public function getNameById($id) {
        $userType = $this->where('id', $id)->first();
        return $userType->name ?? null;
    }

    public function threadLikes(){
        return $this->hasMany('App\Models\ThreadLike', 'user_id');
    }

    public function totalPoints() {
        return $this->hasMany('App\Models\UserPointHistory', 'user_id')
                ->selectRaw('user_point_history.user_id, SUM(user_point_history.points) as totalPoints')
                ->groupBy('user_point_history.user_id');
    }

    public function scheduledUsers() {
        return $this->hasMany(ScheduledEmailUser::class, 'user_id', 'id');
    }

    public static function buildQuery($request, $roleNamesToExclude = []) {
        $userQuery = User::where('status', 1);
        if(count($roleNamesToExclude) > 0) {
            $userQuery->whereHas("roles", function($query) use($roleNamesToExclude) { 
                $query->whereNotIn('name', $roleNamesToExclude); 
            });
        }

        if(!empty($request->filter_region) && $request->filter_region != -1) {
            $userQuery = $userQuery->whereRaw("FIND_IN_SET($request->filter_region, region_id)");
        } 
        if(!empty($request->filter_country) && $request->filter_country != -1) {
            $userQuery = $userQuery->where('country_id', $request->filter_country); 
        }

        if(!empty($request->filter_jobrole) && $request->filter_jobrole != -1) {
            $userQuery->where(function($query) use($request)
            {
                $query->where('job_role_id', $request->filter_jobrole)
                ->orWhereNull('job_role_id');
            });
        }

        if(!empty($request->filter_group) && $request->filter_group != -1) {
            $userQuery = $userQuery->where(function($query) use($request)
            {
                $query->where('group_id', $request->filter_group)
                ->orWhereNull('group_id');
            });
        }

        if(!empty($request->filter_role) && $request->filter_role != -1) {
            $role = $request->filter_role;
            $userQuery = $userQuery->whereHas("roles", function($query) use($role)
                        { 
                            $query->where("id", $role); 
                        });
        } 

        if (!$request->ajax() && !empty($request->search)) {

            $userQuery = $userQuery->where('name', 'like', '%' . trim($request->search) . '%');
        }

        if ($request->ajax() && !empty($request->name)) {
            $userQuery = $userQuery->where('name', 'like', '%' . trim($request->name) . '%');
        }

        return $userQuery;
    }

    public static function buildQueryDashboard($request, $roleNamesToExclude = []) {
        $searchByName = trim($request->get('name'));

        $userQuery = User::with('userLearningPaths');
        if(count($roleNamesToExclude) > 0) {
            $userQuery->whereHas("roles", function($query) use($roleNamesToExclude) { 
                $query->whereNotIn('name', $roleNamesToExclude); 
            });
        }

        if(!empty($request->filter_country)  && $request->filter_country != null && $request->filter_country != -1) {
            if(is_array($request->filter_country)) {
                $userQuery = $userQuery->whereIn('country_id', $request->filter_country); 
            } else {
                $userQuery = $userQuery->where('country_id', $request->filter_country); 
            }
        }

        if(!empty($request->filter_region) && $request->filter_region != null && $request->filter_region != -1) {
            if(is_array($request->filter_region)) {
                $userQuery = $userQuery->whereIn('region_id', $request->filter_region); 
            } else {
                $userQuery = $userQuery->where('region_id', $request->filter_region); 
            }
        } 
    
        if(!empty($request->filter_dealer) && $request->filter_dealer != -1) {
            if(is_array($request->filter_dealer)) {
                $userQuery = $userQuery->whereIn('dealer_id', $request->filter_dealer); 
            } else {
                $userQuery = $userQuery->where('dealer_id', $request->filter_dealer); 
            }
        }

        if(!empty($request->filter_jobRole) && $request->filter_jobRole != null) {
            if(is_array($request->filter_jobRole)) {
                $jobrole = implode(', ', $request->filter_jobRole);
                $userQuery = $userQuery->whereIn('job_role_id', $request->filter_jobRole); 

                if(in_array(-1, $request->filter_jobRole)) {
                    $userQuery = $userQuery->orWhere('job_role_id', null);
                }

            } else {
                $userQuery = $userQuery->where('job_role_id', $request->filter_jobRole); 
            }
        }

        if(!empty($request->filter_group) && $request->filter_group != null) {
            if(is_array($request->filter_group)) {
                $group = implode(', ', $request->filter_group);
                $userQuery = $userQuery->whereIn('group_id', $request->filter_group); 

                if(in_array(-1, $request->filter_group)) {
                    $userQuery = $userQuery->orWhere('group_id', null);
                }

            } else if($request->filter_group == -1) {
                $userQuery = $userQuery->whereNull('group_id'); 
            }  else {
                $userQuery = $userQuery->where('group_id', $request->filter_group); 
            }
            
        }

            if (!empty($searchByName)) {
                $userQuery = $userQuery->where('name', 'like', '%' . $searchByName . '%');
            }

        if(!empty($request->filter_role) && $request->filter_role != -1) {
            $role = $request->filter_role;
            $userQuery = $userQuery->whereHas("roles", function($query) use($role)
                    { 
                        $query->where("id", $role); 
                    });
        } 

        if(!empty($request->filter_learningPath)) {
            $learningPaths = $request->filter_learningPath;
            $userQuery->whereHas("userLearningPaths", function($query) use($learningPaths) { 
                $query->whereIn('learning_path_id', $learningPaths); 
            });
 
        }

        $userQuery->where('status', 1);
        return $userQuery;
    }



    public static function boot() {

        parent::boot();

        static::creating(function($model)
        {
            $user = Auth::user();   
            if($user) {
                $model->created_by = $user->id;
                $model->updated_by = $user->id;
            }        
           
        });

        static::updating(function($model)
        {
            $user = Auth::user();
            if($user) {
                $model->updated_by = $user->id;
            }
        }); 
    }

    public function adminRegions() { // only for admin
        $regions = Region::whereIn('id', explode(',', $this->region_id))->orderBy('name')->get();
        return $regions;
    }

    public function sendPasswordResetNotification($token)
    {
        $email = request()->email;
        $user = User::where('email', $email)->first();
        $country[] = $user->country_id;
        array_push($country, "-1");

        if($user->roles->pluck('name')[0] == 'admin')
        {
            $region = explode(',', $user->region_id);
        } else {
            $region[] = $user->region_id;
        }

        array_push($region, "-1");

        // find country with email and send 

        $url = url(route('password.reset', [
                    'token' => $token,
                    'email' => $user->email,
            ], false));

        // send mail
        $templateConfig = DB::table('user_mail_templates')
                         ->join('mail_template_config', 'user_mail_templates.id', '=', 'mail_template_config.template_id')
                         ->whereIN('mail_template_config.country_id', $country)
                         ->whereIN('mail_template_config.region_id', $region)
                         ->where('user_mail_templates.mailable', 'App\Mail\ResetPasswordEmail')->first();

        if($templateConfig != null) {
            Mail::to($user->email)
            ->send(new \App\Mail\ResetPasswordEmail($user->name, $url, $templateConfig->template_id));

            return redirect('/auth/login')->with('success', \Lang::get('lang.reset-password-link-msg'));

        } else {
            Log::warning('Mail template HideComment does not exist for country ' .  $user->country_id);
            return redirect('/auth/login')->with('error', \Lang::get('lang.mail-not-send'));
        }

    }
}