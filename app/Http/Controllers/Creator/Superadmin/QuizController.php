<?php

namespace App\Http\Controllers\Creator\Superadmin;

use Config;
use PDF;
use App;
use Auth;
use \stdClass;
use Illuminate\Http\Request;
use App\Exports\{ QuizScore, QuizScoreWithAnswer };
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Yajra\DataTables\DataTables;
use App\Models\{ Quiz, Country, QuizQuestion, JobRole, Group, Region, UserQuiz};

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
  
    public $viewStoragePath;
    public $imageFormat;

    public function __construct()
    {
       $this->imageFormat = Config::get('constant.SUPPORTED_IMAGE_FORAMTS'); 
       $this->viewStoragePath =  Config::get('constant.QUIZ_QUESTION_STORAGE_PATH'); 
    }

    public function index(Request $request)
    {

        $export_countries     =  empty($request->query('filter_country')) ? 0 : implode(',', $request->query('filter_country'));
        $export_regions       =  empty($request->query('filter_region')) ? 0 : implode(',', $request->query('filter_region'));
        $export_groups        =  empty($request->query('filter_group')) ? 0 : implode(',', $request->query('filter_group'));
        $export_jobRoles      =  empty($request->query('filter_jobRole')) ? 0 : implode(',', $request->query('filter_jobRole'));

        $countries = Country::select('id', 'name')->orderBy('name','asc')->where('status', 1)->get();
        $regions = [];
        if($request->query('filter_country') && count($request->query('filter_country')) > 0) {
            $regions = Region::whereIn('country_id',$request->query('filter_country'))->orderBy('name','asc')->get();
        } else {
            $regions = Region::orderBy('name','asc')->get();
        }

        $jobRoles = JobRole::where('status', 1)->orderBy('name','asc')->get();
        $groups   = Group::where('status', 1)->orderBy('name','asc')->get(); 

        if ($request->ajax())
        {
            $quizzes = Quiz::quizQuery($request)->orderBy('name')->get();
            return Datatables::of($quizzes)
            ->addIndexColumn()
            ->editColumn('country', function($quizzes){
                $countries = '';
                    foreach($quizzes->quizCountries() as $country){
                        $countries .= $country->name . ',';
                    }
                return ($countries !="") ? rtrim($countries, ',') : 'N/A';
            })
            ->editColumn('region', function($quizzes){
                $regions = '';
                    foreach($quizzes->quizRegions() as $region){
                        $regions .= $region->name . ',';
                    }
               return ($regions !="") ? rtrim($regions, ',') : 'N/A';
            })

            ->editColumn('jobRole', function($quizzes){
                $jobRoles = '';
                    foreach($quizzes->quizJobRoles() as $jobRole){
                        $jobRole .= $jobRole->name . ',';
                    }
                return ($jobRoles !="") ? rtrim($jobRoles, ',') : 'N/A';
            })

            ->editColumn('group', function($quizzes){
                $groups = '';
                    foreach($quizzes->quizGroups() as $group){
                        $groups .= $group->name . ',';
                    }
                return ($groups !="") ? rtrim($groups, ',') : 'N/A';
            })
            ->editColumn('status', function ($quizzes){
                $activated = \Lang::get('lang.activated');
                $deactivated = \Lang::get('lang.deactivated');
                return $quizzes->is_active == 1 ? $activated : $deactivated;
            })

            ->editColumn('created_at', function($quizzes)
            {
                return  date('d M Y', strtotime($quizzes->created_at));
            })

            ->editColumn('updated_at', function($quizzes)
            {
                return  date('d M Y', strtotime($quizzes->created_at));
            })

            ->addColumn('action', function($quizzes){
                $href = url('/superadmin/quizzes/' . $quizzes->id);
                $responses = url('/superadmin/quiz/responses/' . $quizzes->id);
                $view = \Lang::get('lang.view');
                $edit = \Lang::get('lang.edit');
                $manageQuestions = \Lang::get('lang.manage-questions');
                $response = \Lang::get('lang.view-response');
                $delete = \Lang::get('lang.delete');
                $actionBtn = "<a href='$href'><i class='fa fa-eye' aria-hidden='true'></i> $manageQuestions </a>";
                $actionBtn = $actionBtn."<a href='$responses'><i class='fa fa-eye' aria-hidden='true'></i> $response </a><a href='$href/edit'><i class='fa fa-pencil' aria-hidden='true'></i> $edit </a>"; 
                $actionBtn = $actionBtn . "<button type='button' class='text-danger delete-user' data-href='$href' data-role='superman'><i class='fa fa-trash' aria-hidden='true'></i> $delete </button>";
                return $actionBtn;
            })
            ->rawColumns(['action'])
            ->make(true);
        }
        
        return view( 'creator.superadmin.quiz.index', compact('countries', 'regions', 'jobRoles', 'groups', 'export_countries', 'export_regions', 'export_groups', 'export_jobRoles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countries = Country::select('id', 'name')->orderBy('name')->where('status', 1)->get();
        $jobRoles = JobRole::where('status', 1)->orderBy('name')->get();
        $groups = Group::where('status', 1)->orderBy('name')->get();
         
        return view('creator.superadmin.quiz.create', compact(['countries', 'jobRoles', 'groups']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name'        => 'required|min:3',
            'country'     => 'required',
            'description' => 'required|min:2'
        ]);

        if((count($request->country) == 1)  && $request->country[0] != -1)
        {
            $this->validate($request, [
                'region' => 'required'
            ]);
        }

        $quiz               = new Quiz;
        $quiz->name         = $request->name;
        $quiz->country_id   = $request->country == -1 ? $request->country : implode(",", $request->country);
        $quiz->region_id    = $request->region ? implode(',', $request->region) : null; 
        $quiz->job_role_id  = $request->jobRole ? implode(',', $request->jobRole) : null; 
        $quiz->group_id     = $request->group ? implode(',', $request->group) : null; 
        $quiz->description  = $request->description;
        $quiz->save();

        return redirect("superadmin/quizzes")->with('success',\Lang::get('lang.quiz').' '.\Lang::get('lang.created-successfully')); 
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $quiz = Quiz::where('id',$id)->first();
        $countries = Country::select('id', 'name')->orderBy('name')->where('status', 1)->get();
        $regions = Region::where('status', 1)->orderBy('name')->get();
        $jobRoles = JobRole::where('status', 1)->orderBy('name')->get();
        $groups = Group::where('status', 1)->orderBy('name')->get();
        $quizRegions = ($quiz->region_id != null) ? explode(',', $quiz->region_id) : '';

        $displayCountry = '';
        $dispalyRegion = '';
        $dispalyJobRole= '';
        $dispalyGroup = '';

        $displayCountry = (count(explode(',', $quiz->country_id)) > 1) ? count(explode(',', $quiz->country_id)) . ' Selected' : ($quiz->country_id == -1 ? \Lang::get('lang.all') : Country::where('id', (int)$quiz->country_id)->pluck('name')->first());
        $dispalyRegion  = ($quiz->region_id == null) ? \Lang::get('lang.all') : ((count(explode(',', $quiz->region_id)) > 1) ? count(explode(',', $quiz->region_id)) . ' Selected' : (Region::where('id', (int)$quiz->region_id)->pluck('name')->first()));
        $dispalyJobRole = (empty($quiz->job_role_id)) ? \Lang::get('lang.select-job-role') : ((count(explode(',', $quiz->job_role_id)) > 1) ? count(explode(',', $quiz->job_role_id)) . ' Selected' : (JobRole::where('id', (int)$quiz->job_role_id)->pluck('name')->first()));
        $dispalyGroup   = (empty($quiz->group_id)) ? \Lang::get('lang.select-group') : ((count(explode(',', $quiz->group_id)) > 1) ? count(explode(',', $quiz->group_id)) . ' Selected' : (Group::where('id', (int)$quiz->group_id)->pluck('name')->first()));
        
        return view('creator.superadmin.quiz.edit', compact('quiz', 'countries', 'jobRoles', 'groups', 'regions', 'quizRegions', 'displayCountry', 'dispalyRegion', 'dispalyJobRole', 'dispalyGroup'));
    }

    public function show(Request $request, $id) 
    {
        $search = trim($request->query('name'));
        $page = $request->page ? $request->page : 1;
        $limit = 10;

        $quiz = Quiz::where('id', $id)->first();
        $countries = Country::select('id', 'name')->get();

        $query = QuizQuestion::where('quiz_id', $quiz->id);
        if (!empty($search)) {

            $query = $query->where('question_text', 'like', '%' . $search . '%');
     
        } 
        $questions = $query->orderBy('question_text')->get();

        if ($request->ajax())
        {
            return Datatables::of($questions)
            ->addIndexColumn()
            ->editColumn('media', function($questions)
            {
                $imageFormat =  $this->imageFormat;
                $viewStoragePath =  $this->viewStoragePath;
                if($questions->media != NULL){
                    $ext = explode('.', $questions->media);

                    if(in_array(strtolower($ext[1]), $imageFormat)) {
                        return "image?" . asset('storage' . $viewStoragePath . $questions->media);
                    } else {
                       return "video?" . asset('storage' . $viewStoragePath . $questions->media);
                    }
                } else {}
            })
            ->addColumn('action', function($questions){
                $href = url('/superadmin/quiz-questions/' . $questions->id);
                $options = url('/superadmin/quiz-question/option/add/' . $questions->id);
                $edit = \Lang::get('lang.edit');
                $manageOptions = \Lang::get('lang.manage-option');
                $response = \Lang::get('lang.view-response');
                $delete = \Lang::get('lang.delete');
                $actionBtn = "<a href='$options'><i class='fa fa-eye' aria-hidden='true'></i> $manageOptions </a>";
                $actionBtn = $actionBtn."<a href='$href/edit'><i class='fa fa-pencil' aria-hidden='true'></i> $edit </a>"; 
                $actionBtn = $actionBtn . "<button type='button' class='text-danger delete-user' data-href='$href' data-role='superman'><i class='fa fa-trash' aria-hidden='true'></i> $delete </button>";
                return $actionBtn;
            })
            ->rawColumns(['action'])
            ->make(true);
        }
      
        return view('creator.superadmin.quiz.show', compact('quiz', 'countries', 'id'));
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
            'id'          => 'required|exists:quizzes,id', 
            'name'        => 'required|min:3',
            'description' => 'required|min:2'
        ]);

        $quiz = Quiz::where('id', $request->id)->first();
     
        $update = [ 
                'name'        => $request->name,
                'country_id'   => empty($request->country) ? $quiz->country_id : ltrim($quiz->country_id.','.implode(',', $request->country), ','),
                'region_id'    => empty($request->region) ? $quiz->region_id : ltrim($quiz->region_id.','.implode(',', $request->region), ','),
                'job_role_id'  => empty($request->job_role) ? $quiz->job_role_id : ltrim($quiz->job_role_id.','.implode(',', $request->job_role), ',') ,
                'group_id'     => empty($request->group_name) ? $quiz->group_id : ltrim($quiz->group_id.','.implode(',', $request->group_name), ',') ,
                'description' => $request->description,
        ];

        $isUpdated = Quiz::where('id', $request->id)->update($update);
        if($isUpdated == 0)
        {
             return redirect()->back()->with("error",  \Lang::get('lang.unable-to-update') ); 
        }

        return redirect("superadmin/quizzes")->with("success",  \Lang::get('lang.quiz') .' '. \Lang::get('lang.updated-successfully')); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $quiz = Quiz::findOrfail($id);
        $update = ['status' => 0];
        $isDeleted = Quiz::where('id', $id)->update($update);
        
        if($isDeleted == 0)
        {
            return back()->with('error', \Lang::get('lang. unable-to-delete'));  
        } 
        
        return redirect()->back()->with("success",  \Lang::get('lang.quiz') .' '. \Lang::get('lang.deleted-successfully')); 
    }  
    
    public function changeQuizStatus (Request $request)
    {
        request()->validate([
            'quiz_id' => 'required|exists:quizzes,id', 
            'status'  => 'required',
        ]);

        $update = Quiz::where('id', $request->quiz_id)->update(['is_active' => $request->status]);
        if($request->status == 1)
        {
            return redirect()->back()->with("success",  \Lang::get('lang.quiz-actiavte'));
        } else {
            return redirect()->back()->with("success",  \Lang::get('lang.quiz-deactiavte')); 
        }
    }

    public function viewResponse (Request $request, $quizID) {
        
        $search = trim($request->query('search'));
        $page = $request->page ? $request->page : 1;
        $limit = 10;
        $query = '';
        $userCount = 0;

        $export_countries     =  empty($request->query('filter_country')) ? 0 : implode(',', $request->query('filter_country'));
        $export_regions       =  empty($request->query('filter_region')) ? 0 : implode(',', $request->query('filter_region'));
        $export_groups        =  empty($request->query('filter_group')) ? 0 : implode(',', $request->query('filter_group'));
        $export_jobRoles      =  empty($request->query('filter_jobRole')) ? 0 : implode(',', $request->query('filter_jobRole'));

        $countries = Country::select('id', 'name')->orderBy('name')->where('status', 1)->get();
        $regions = Region::where('status', 1)->orderBy('name')->get();

        $quiz = Quiz::findOrfail($quizID);
        $groupWithNa = [];

        $object = new stdClass();
        $object->id = -1;
        $object->name = 'N/A';
        array_push($groupWithNa, $object);

        foreach ($quiz->quizGroups() as $group) {
            $object = new stdClass();
            $object->id = $group->id;
            $object->name = $group->name;
            array_push($groupWithNa, $object);
        }


        $jobRoleWithNa = [];

        $object = new stdClass();
        $object->id = -1;
        $object->name = 'N/A';
        array_push($jobRoleWithNa, $object);

        foreach ($quiz->quizJobRoles() as $jobRole) {
            $object = new stdClass();
            $object->id = $jobRole->id;
            $object->name = $jobRole->name;
            array_push($jobRoleWithNa, $object);
        }

        $query = UserQuiz::where('quiz_id', $quizID);

        $query = $query->with([ 'user' => function($q) use ($request, $search){
                           
                            $q->with('country', 'region', 'jobRole', 'group');

                                if(!empty($request->filter_country)  && $request->filter_country != null) {
                                    $q->whereIn('country_id', $request->filter_country);
                                }

                                if(!empty($request->filter_region)  && $request->filter_region != null){
                                    $q->whereIn('region_id', $request->filter_region);
                                }

                                if(!empty($request->filter_jobRole)  && (! in_array(null, $request->filter_jobRole)) && (! in_array(-1, $request->filter_jobRole))){
                                
                                    $q->whereIn('job_role_id', $request->filter_jobRole);
                                }

                                if((!empty($request->filter_group))  && (! in_array(null, $request->filter_group)) && (! in_array(-1, $request->filter_group))){
                                
                                    $q->whereIn('group_id', $request->filter_group);
                                }

                                if(!empty($search)) {
                                    $q->where('users.name', 'like', '%' . $search . '%');
                                }
                        }]) ;

        $userQuizzes = $query->paginate($limit); 
               
        return view('creator.superadmin.quiz.response', compact('userQuizzes', 'quiz', 'export_countries', 'export_regions', 'export_groups', 'export_jobRoles', 'jobRoleWithNa', 'groupWithNa', 'userCount', 'countries', 'regions'))
                ->with('index', ($page- 1) * $limit);
    }

    // export with score only
    public function exportScores(Request $request, $quizId) 
    {
        return Excel::download(new QuizScore($request->country, $request->region, $request->group, $request->jobRole, $quizId), 'scores.xls');
    }

    // export with score and answer
    public function exportScoresWithAnswer(Request $request, $quizId) 
    {
        return Excel::download(new QuizScoreWithAnswer($request->country, $request->region, $request->group, $request->jobRole, $quizId), 'scores.xls');
    }
}
