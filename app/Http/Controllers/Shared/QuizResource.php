<?php

namespace App\Http\Controllers\Shared;

use Auth;
use Config;
use Setting;
use Illuminate\Http\Request;
use App\Events\PointUpdateEvent;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Models\{ Quiz, Country, QuizQuestion,  QuizSubmission, QuestionOption, UserPointHistory, UserQuiz };

class QuizResource extends Controller
{

    public $optionViewPath;
    public $viewStoragePath;
    public $imageFormat;

    public function __construct()
    {
        $this->viewStoragePath = Config::get('constant.QUIZ_QUESTION_STORAGE_PATH');
        $this->imageFormat = Config::get('constant.SUPPORTED_IMAGE_FORAMTS');
        $this->optionViewPath =  Config::get('constant.QUIZ_OPTION_STORAGE_PATH');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
       /* $country = Auth::user()->country_id;
        $region = Auth::user()->region_id;
        $search = trim($request->query('name'));
        $routeSlug = $this->getRouteSlug();
        $query = Quiz::select('*')->where( function ($q) use ($country, $region) {
                $q->whereRaw("find_in_set( $region, region_id)");
                $q->whereRaw("find_in_set( $country, country_id)")
                    ->orWhereRaw("find_in_set( -1, country_id)");
                })->with(['userQuizzes' => function($s) {
                        $s->select('score', 'quiz_id', 'created_at')->where('user_id', Auth::user()->id);
                }])->orderBy('name');

        if(!empty($search))
        {
            $query->where('name', 'like', '%' . $search . '%');
        }*/

        $country = Auth::user()->country_id ?? -1;
$region = Auth::user()->region_id ?? -1;
$search = trim($request->query('name'));
$routeSlug = $this->getRouteSlug();

$query = Quiz::select('*')
    ->where(function ($q) use ($country, $region) {
        $q->whereRaw("FIND_IN_SET(?, region_id)", [$region])
          ->where(function ($sub) use ($country) {
              $sub->whereRaw("FIND_IN_SET(?, country_id)", [$country])
                  ->orWhereRaw("FIND_IN_SET(-1, country_id)");
          });
    })
    ->with(['userQuizzes' => function ($s) {
        $s->select('score', 'quiz_id', 'created_at')
          ->where('user_id', Auth::id());
    }])
    ->orderBy('name');

if (!empty($search)) {
    $query->where('name', 'like', '%' . $search . '%');
}
        $quizzes = $query->where('status', 1)->where('is_active', 1)->get();
        if($request->ajax()){
            if($request->is('api/*')){
                $quizList = [];
                foreach($quizzes as $quiz) {

                    $quizList[] = $quiz;
                    if( count($quiz->userQuizzes) > 0){
                        $quiz->is_attempted = true;
                    } else {
                        $quiz->is_attempted = false;
                    }
                }
                return response()->json(['success' => true, 'data' => $quizList, 'message' => \Lang::get('lang.quiz-list')]);
            } else {
                return Datatables::of($quizzes, $routeSlug, $request)
            ->addIndexColumn()

            ->editColumn('description', function ($quizzes)
            {
                return $quizzes->description;
            })

            ->editColumn('score', function ($quizzes){
               return count($quizzes->userQuizzes) > 0 ? $quizzes->userQuizzes[0]->score : 0;
            })
            ->editColumn('created_on', function($quizzes)
            {
                return  date('d M Y', strtotime($quizzes->created_at));
            })
            ->addColumn('action', function($quizzes){
                $routeSlug = Auth::user()->getRoleNames()->first() == "dealer" ? 'dealer' : 'staff';
                $href = url($routeSlug .'/quiz/'. $quizzes->id);
                $response = url($routeSlug .'/quiz/'. $quizzes->id . '/response');
                $quizCompleted = \Lang::get('lang.quiz-completed');
                $quizResponse = \Lang::get('lang.submitted-response');
                $quizOpen = \Lang::get('lang.open-quiz');

                $actionBtn = '';
                if(count($quizzes->userQuizzes) > 0) {
                    $actionBtn = "<a href='$href' style='pointer-events: none'>";
                    $actionBtn = $actionBtn . "<span class='text-success'> $quizCompleted </span> &nbsp; &nbsp;";
                    $actionBtn = $actionBtn . "<a href='$response'><i class='fa fa-eye' aria-hidden='true'></i> &nbsp; $quizResponse </a>";
                } else {
                    $actionBtn = "<a href='$href'><i class='fa fa-eye' aria-hidden='true'></i> &nbsp; $quizOpen </a>";
                }
                return $actionBtn;
            })
            ->rawColumns(['action'])
            ->make(true);
            }
        }
            return view( 'shared.quiz.index', compact('quizzes', 'routeSlug'));
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $quizId, $selections = null, $previusQuestionId = null, $lastQuestionId = null, $firstQuestionId = null)
    {

        $viewStoragePath = $this->viewStoragePath;
        $imageFormat = $this->imageFormat;
        $optionViewPath =  $this->optionViewPath;

        $routeSlug = $this->getRouteSlug();

        $where = [
                    'user_id' => Auth::user()->id,
                    'quiz_id' => $quizId
        ];

        //check if quiz completed or not
        $quizSubmitted = UserQuiz::where($where)->first();
        $quiz = Quiz::findOrFail($quizId);
        $question = QuizQuestion::select('id', 'media', 'media_type','question_text', 'quiz_id')->where('quiz_id', $quizId)
                                ->with('quizSubmissions', function($q){
                                    $q->where('user_id', Auth::user()->id)->select('id', 'user_id', 'quiz_id', 'question_id', 'selections');
                                })->with('options', function($q){
                                    $q->select('id', 'option_text', 'question_id', 'media_type', 'media');
                                })->orderBy('id', 'asc')->first();

        if(!$quiz->is_active || !$question || $quizSubmitted != null)
        {

            if($request->ajax()){
                return response()->json(['success' => false,  'message' => \Lang::get('lang.invalid-quiz')]);
            }
            return redirect($routeSlug . '/quiz')->with('error', \Lang::get('lang.invalid-quiz'));
        }

        $selections = [];
        if($question) {
            $selections = (count($question->quizSubmissions) > 0) ? $selections = explode(',', $question->quizSubmissions[0]->selections) : null;
        }


        $lastQuestionId = $this->getLatQuestionId($quizId);

        if($request->ajax()){
            $questionMediaPath = asset('storage/quiz-questions/files/');
            $optionMediaPath = asset('storage/quiz-options/files/');

            $action = "";
            if($question->id == $lastQuestionId) {
                $action = \Lang::get('lang.submit');
            } else if($question->id < $lastQuestionId) {
                $action = \Lang::get('lang.save & next');
            }
            return response()->json(['success' => true,  'question' =>$question, 'already-selected-options' => $selections, 'previusQuestionId' => $previusQuestionId, 'firstQuestionId' =>$question->id, 'lastQuestionId' => $lastQuestionId, 'action' => $action, 'questionMediaPath' => $questionMediaPath, 'optionMediaPath' => $optionMediaPath, 'message' => \Lang::get('lang.quiz')]);
        }


        return view('shared.quiz.show', compact('question', 'routeSlug', 'quizId', 'selections', 'previusQuestionId', 'lastQuestionId', 'firstQuestionId', 'viewStoragePath', 'imageFormat', 'optionViewPath'));
    }

    public function quizQuestion(Request $request)
    {

        $viewStoragePath = $this->viewStoragePath;
        $imageFormat = $this->imageFormat;
        $optionViewPath =  $this->optionViewPath;

        $pointsPerCorrectAnswer = (int)Setting::get('correct_answer_points');

        request()->validate([
            'questionId' => 'required|exists:quiz_questions,id',
            'quizId'  => 'required|exists:quizzes,id',
        ]);

        $question = '';
        $selections = '';
        $quizId = $request->quizId;
        $action = $request->input('action');

        $where = [
                'user_id' => Auth::user()->id,
                'quiz_id' => $request->quizId,
                'question_id' => $request->questionId,
        ];

        $routeSlug = $this->getRouteSlug();
        $lastQuestionId = $this->getLatQuestionId($quizId);
        $firstQuestionId = $this->getFirstQuestionId($quizId);

        if ($action == "pre")
        {
            $question = QuizQuestion::select('id', 'media', 'question_text', 'quiz_id', 'media_type')->where('id', '<', $request->questionId)->where('quiz_id', $quizId)
                        ->with('quizSubmissions', function($q){
                            $q->where('user_id', Auth::user()->id)->select('id', 'user_id', 'quiz_id', 'question_id', 'selections');
                        })->with('options', function($q){
                            $q->select('id', 'option_text', 'question_id', 'media_type', 'media');
                        })->orderBy('id', 'desc')->first();
            $selections = explode(',', $question->quizSubmissions[0]->selections);
            $previusQuestionId = $this->getPreviousQuestionId($request->questionId, $quizId);


        } else {

            // Next button
            request()->validate([
                'option' => 'required',
            ]);

            // calculate earned points for question
            $earned_points = 0;
            $selectedOptions = implode(',', $request->option);

            $options = QuestionOption::whereIn('id', explode(',', $selectedOptions))->get();
            foreach($options as $option)
            {
                if($option->is_correct) {
                    $earned_points = $earned_points + $pointsPerCorrectAnswer;
                }
            }

            $isSubmit = QuizSubmission::where($where)->first(); // update quiz submission
            if(!empty($isSubmit)) {

                $update = [
                    'selections' => implode(',', $request->option),
                    'earned_points' => $earned_points,
                ];

                $isUpdate = QuizSubmission::where($where)->update($update);

            } else {

                $quizSubmission = new  QuizSubmission();
                $quizSubmission->user_id = Auth::user()->id;
                $quizSubmission->quiz_id = $request->quizId;
                $quizSubmission->question_id = $request->questionId;
                $quizSubmission->selections = $selectedOptions;
                $quizSubmission->earned_points = $earned_points;
                $quizSubmission->save();  // insert new quiz submission

                // add points
                if($action == 'submit') {
                    $score = 0;
                    $eventType = Config::get('constant.QUIZ_SCORE');
                    $where = [
                        'user_id' => Auth::user()->id,
                        'quiz_id' => $request->quizId,
                    ];

                    $submissions = QuizSubmission::select('selections')->where($where)->get();

                    foreach($submissions as $submission)
                    {
                        $options = QuestionOption::whereIn('id', explode(',', $submission->selections))->get();

                        foreach($options as $option)
                        {
                            if($option->is_correct) {
                                $score = $score + $pointsPerCorrectAnswer;
                            }
                        }
                    }

                    event(new PointUpdateEvent($eventType, Auth::user(), $score));

                    // add score in user_quiz
                    $input['user_id'] = Auth::user()->id;
                    $input['quiz_id'] = $request->quizId;
                    $input['score']   = $score;
                    UserQuiz::create($input);

                    if($request->ajax()){

                        return response()->json(['success' => true,  'message' => \Lang::get('lang.quiz-completed')]);
                    }
                    return redirect($routeSlug. '/quiz')->with('success', \Lang::get('lang.quiz-completed'));
                }

            }

            $question = QuizQuestion::where('id', '>', $request->questionId)->where('quiz_id', $request->quizId)
                                    ->with('quizSubmissions', function($q){
                                        $q->where('user_id', Auth::user()->id);
                                    })->with('options')->orderBy('id', 'asc')->first();

            if( !empty($question) && (count($question->quizSubmissions) > 0)) {
                $selections = explode(',', $question->quizSubmissions[0]->selections);
            } else{
                $selections = "";
            }

            if($question != null)
            {
                $previusQuestionId = $this->getPreviousQuestionId($question->id, $quizId);
            } else {
                $previusQuestionId = $this->getPreviousQuestionId($request->questionId, $quizId);
            }

        }

        if($request->ajax()){
            $questionMediaPath = asset('storage/quiz-questions/files/');
            $optionMediaPath = asset('storage/quiz-options/files/');
            return response()->json(['success' => true,  'action' => $action, 'question' => $question, 'already-selected' => $selections, 'lastQuestionId' => $lastQuestionId, 'previusQuestionId' => $previusQuestionId, 'firstQuestionId' => $firstQuestionId, 'questionMediaPath' => $questionMediaPath, 'optionMediaPath' => $optionMediaPath ,'message' => \Lang::get('lang.quiz')]);
        }

        return view('shared.quiz.show', compact('action', 'quizId', 'routeSlug', 'question', 'selections', 'lastQuestionId', 'previusQuestionId', 'firstQuestionId', 'viewStoragePath', 'imageFormat', 'optionViewPath'));

    }

    protected function getRouteSlug() {
        $user = Auth::user();
        return $user->getRoleNames()->first();
    }

    // get last question Id
    public function getLatQuestionId($quizId)
    {
        $lastQuestion = QuizQuestion::select('id')->where('quiz_id', $quizId)->latest()->first();
        return $lastQuestion ? $lastQuestion->id : null;
    }

    public function getPreviousQuestionId($currentQuestionId, $quizId)
    {
        $previousQuestion = QuizQuestion::select('id')->where('quiz_id', $quizId)->where('id', '<', $currentQuestionId)->latest()->get();
        if(count($previousQuestion) > 0)
        {
            return $previousQuestion[0]->id;
        } else
        {
            return null;
        }
    }

    public function getFirstQuestionId($quizId)
    {
        $firstQuestion = QuizQuestion::select('id')->where('quiz_id', $quizId)->first();
        return $firstQuestion ? $firstQuestion->id : null;
    }


    public function quizResponse(Request $request, $id)
    {
        $viewStoragePath = $this->viewStoragePath;
        $imageFormat = $this->imageFormat;
        $optionViewPath =  $this->optionViewPath;
        $quiz = Quiz::findOrfail($id);
        $selections = [];
        $submissions = [];
        $questions = QuizQuestion::with('options')
                    ->with('quizSubmissions', function($q){
                        $q->where('user_id', Auth::user()->id);
                    })->where('quiz_id', $id)->get();

        foreach($questions as $question)
        {
            if(count($question->quizSubmissions) == 0) {
                array_push($selections, "");
            } else {
                $quizSelections = explode(",", $question->quizSubmissions[0]->selections);
                array_push($selections, $quizSelections);
            }
        }

        // get selections into one array
        foreach ((array)$selections as $selection)
        {
            foreach ((array)$selection as $option)
            {
                array_push($submissions, $option);
            }
        }

        $routeSlug = $this->getRouteSlug();
        if($request->ajax()){
            $questionMediaPath = asset('storage/quiz-questions/files/');
            $optionMediaPath = asset('storage/quiz-options/files/');

            return response()->json(['success' => true, 'questions' => $questions, 'questionMediaPath' => $questionMediaPath, 'optionMediaPath' => $optionMediaPath ,'message' => \Lang::get('lang.quiz-response')]);
        }
        return view('shared.quiz.user-response', compact('quiz', 'questions', 'viewStoragePath', 'imageFormat', 'optionViewPath', 'routeSlug', 'submissions'));
    }

    // only for admin

    public function adminQuiz(Request $request)
    {
        $country = Auth::user()->country_id;
        $regions = explode(',', Auth::user()->region_id);
        $search = trim($request->query('name'));
        $query = Quiz::where('status', 1);
        if (!empty($search)) {

            $query = $query->where('name', 'like', '%' . $search . '%')
                     ->where( function ($q) use ($country, $regions){
                        foreach ($regions as $region) {
                            $q->orWhereRaw("find_in_set( $region, region_id)");
                        }
                            $q->whereRaw("find_in_set( $country, country_id)")
                              ->orWhereRaw("find_in_set( -1, country_id)");

                        })->orderBy('name');

        } else {

            $query = $query->where( function ($q)  use ($country, $regions) {
                        foreach ($regions as $region) {
                            $q->orWhereRaw("find_in_set( $region, region_id)");
                        }
                         $q->whereRaw("find_in_set( $country, country_id)")
                           ->orWhereRaw("find_in_set( -1, country_id)");

                    })->orderBy('name');
        }

        $quizzes = $query->orderBy('name')->get();
        if ($request->ajax())
        {
            return Datatables::of($quizzes)
            ->addIndexColumn()
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
                $href = url('/admin/quiz/' . $quizzes->id);
                $openQuiz = \Lang::get('lang.open-quiz');
                $actionBtn = "<a href='$href'><i class='fa fa-eye' aria-hidden='true'></i>&nbsp; $openQuiz </a>";
                return $actionBtn;
            })
            ->rawColumns(['action'])
            ->make(true);
        }

        return view( 'shared.quiz.admin-index', compact('quizzes'));
    }

    public function showAdminQuiz ($id)
    {
        $viewStoragePath = $this->viewStoragePath;
        $imageFormat = $this->imageFormat;
        $optionViewPath =  $this->optionViewPath;
        $quiz = Quiz::findOrfail($id);
        $questions = QuizQuestion::with('options')->where('quiz_id', $id)->get();

        return view('shared.quiz.admin-show', compact('quiz', 'questions', 'viewStoragePath', 'imageFormat', 'optionViewPath'));
    }
}
