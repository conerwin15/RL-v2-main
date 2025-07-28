<?php

namespace App\Http\Controllers\Creator\Superadmin;

use File;
use Config;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{ Quiz, QuestionOption, QuizQuestion };

class QuizQuestionResource extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public $storagePath;
    public $viewStoragePath;
    public $imageFormat;
    public $videoFormat;
    public $optionStoragePath;
    public $optionViewPath;

    public function __construct()
    {
       $this->storagePath = storage_path('app/public' . Config::get('constant.QUIZ_QUESTION_STORAGE_PATH'));
       $this->optionStoragePath = storage_path('app/public' . Config::get('constant.QUIZ_OPTION_STORAGE_PATH'));
       $this->imageFormat = Config::get('constant.SUPPORTED_IMAGE_FORAMTS'); 
       $this->videoFormat = Config::get('constant.SUPPORTED_VIDEO_FORAMTS');
       $this->viewStoragePath =  Config::get('constant.QUIZ_QUESTION_STORAGE_PATH'); 
       $this->optionViewPath =  Config::get('constant.QUIZ_OPTION_STORAGE_PATH');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createQuestions($quiz_id)
    {
        return view('creator.superadmin.quiz-questions.create', compact('quiz_id'));
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
            'quiz_id' => 'required|exists:quizzes,id',
            'text' => 'required|min:2'
        ]); 

        $quizQuestion = new QuizQuestion;
        $quizQuestion->question_text = $request->text;
        $quizQuestion->quiz_id = $request->quiz_id;


        // file upload
        $mediaFormats = implode(',', Config::get("constant.QUESTION_MEDIA_FROMATS"));

        if($request->hasFile('media')) {
            request()->validate([
                'media' => 'mimes:' . $mediaFormats . '|max:1048576' // 1048 mb
            ]);

            $file = $request->file('media');

            $mediaType = $this->getMediaType($file);

            $extension = $file->getClientOriginalExtension();

            // check directory exist or not 
            $path = $this->storagePath;
       
            if(!is_dir($path)) {
                File::makeDirectory($path, $mode = 0775, true, true);
            }
           
            $fileName = md5(microtime()) .'.'. $extension;
            $file->move($this->storagePath, $fileName);
            $quizQuestion->media_type = $mediaType;
            $quizQuestion->media = $fileName;
            
        } 

        $quizQuestion->save();
        $redirect = "/superadmin/quizzes/".$request->quiz_id;
        return redirect($redirect)->with('success',\Lang::get('lang.quiz-question').' '.\Lang::get('lang.created-successfully'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $imageFormat =  $this->imageFormat;
        $videoFormat =  $this->videoFormat;
        $viewStoragePath =  $this->viewStoragePath;
        
        $quizQuestion = QuizQuestion::where('id',$id)->first();
        return view('creator.superadmin.quiz-questions.edit', compact('quizQuestion', 'imageFormat', 'videoFormat', 'viewStoragePath'));
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

            'id'      => 'required|exists:quiz_questions,id', 
            'text'    => 'required|min:2'
        ]);

        $quizQuestion = QuizQuestion::findOrFail($id);
      
        $update = [ 
                    'question_text' => $request->text
        ];    

        if($request->hasFile('media')) {
            //delete file from storage
            if($quizQuestion->media != null && $quizQuestion->media != '') {
                $mediaPath = $this->storagePath . $quizQuestion->media;
                if(file_exists($mediaPath)) {
                    unlink($mediaPath);
                }
               
            }    

            $mediaFormats = implode(',', Config::get("constant.QUESTION_MEDIA_FROMATS"));
            request()->validate([
                'media' => 'mimes:' . $mediaFormats . '|max:1048576' // 1048 mb
            ]);

            $file = $request->file('media');
          
            $mediaType = $this->getMediaType($file);
            
            $extension = $file->getClientOriginalExtension();
            $fileName = md5(microtime()) .'.'. $extension;
            $file->move($this->storagePath, $fileName);
            $quizQuestion->media = $fileName;

                
            $update = [   
                'media_type' => $mediaType,
                'media' =>  $fileName,
            ];

        }

        $isUpdated = QuizQuestion::where('id', $request->id)->update($update); 

        if($isUpdated == 0)
        {
            return redirect()->back()->with('error', \Lang::get('lang.unable-to-update'));
        }  

        $redirect = "/superadmin/quizzes/".$quizQuestion->quiz_id;
        return redirect($redirect)->with('success',\Lang::get('lang.quiz-question').' '.\Lang::get('lang.updated-successfully'));         
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $quizQuestion = QuizQuestion::find($id);

        //delete file from storage
        if($quizQuestion->media != null && $quizQuestion->media != '') {
            $mediaPath = $this->storagePath . $quizQuestion->media;
            if(file_exists($mediaPath)) {
                unlink($this->storagePath . $quizQuestion->media);
            }
        }
        $isDeleted = $quizQuestion->delete();

        if($isDeleted == 0)
        {
            return response()->json(['success' => false, "messsage" =>  \Lang::get('lang.unable-to-delete')], 200);
        } 

        return response()->json(['success' => true, "messsage" =>  \Lang::get('lang.quiz-question-delete')], 200);
    }

    /**
     * get image type
     */
    public function getMediaType ($file)
    {
        $mime = $file->getMimeType();
        if(strstr($mime, "video/")){
            $mediaType = 'video';
        }else if(strstr($mime, "image/")){
            $mediaType = 'image';
        }

        return $mediaType;
    }

    public function addOption ($id)
    {
        $imageFormat =  $this->imageFormat;
        $optionViewPath =  $this->optionViewPath;
        $options = QuestionOption::select('id', 'option_text', 'is_correct', 'media', 'media_type')->where('question_id', $id)->get();
        return view('creator.superadmin.quiz-questions.add-option', compact('id', 'options', 'optionViewPath', 'imageFormat'));
    }

    public function storeOption (Request $request)
    {
        $request->validate([
            'question_id'  => 'required|exists:quiz_questions,id', 
            'text'    => 'required|min:2',
            'status' => 'required',
        ]);

        $questionOption = new QuestionOption;
        $questionOption->question_id = $request->question_id;
        $questionOption->option_text = $request->text;
        $questionOption->is_correct = $request->status;

         // file upload
         $mediaFormats = implode(',', Config::get("constant.QUESTION_MEDIA_FROMATS"));

         if($request->hasFile('media')) {
             request()->validate([
                 'media' => 'mimes:' . $mediaFormats . '|max:1048576' // 1048 mb
             ]);
 
             $file = $request->file('media');
 
             $mediaType = $this->getMediaType($file);
 
             $extension = $file->getClientOriginalExtension();
 
             // check directory exist or not 
             $path = $this->optionStoragePath;
        
             if(!is_dir($path)) {
                 File::makeDirectory($path, $mode = 0775, true, true);
             }
            
             $fileName = md5(microtime()) .'.'. $extension;
             $file->move($this->optionStoragePath, $fileName);
             $questionOption->media_type = $mediaType;
             $questionOption->media = $fileName;
             
         }

        $questionOption->save();

        return back()->with('success',\Lang::get('lang.quiz-question-option').' '.\Lang::get('lang.created-successfully'));         
    }

    public function deleteOption (Request $request)
    {
        $request->validate([
            'option_id'  => 'required|exists:question_options,id', 
        ]);

        $quizOptioon = QuestionOption::find($request->option_id);
       
        //delete file from storage
        if($quizOptioon->media != null && $quizOptioon->media != '') {
            $mediaPath = $this->optionStoragePath . $quizOptioon->media;
            if(file_exists($mediaPath)) {
                unlink($this->optionStoragePath . $quizOptioon->media);
            }
        }

        $isDeleted = QuestionOption::where('id', $request->option_id)->delete();
        if($isDeleted == 0)
        {
            return back()->with('error', \Lang::get('lang.unable-to-delete'));
        } 

        return back()->with('success',\Lang::get('lang.quiz-question-option-deleted'));
    }

    public function editOption ($questionId, $optionId) {
       
        $imageFormat =  $this->imageFormat;
        $optionViewPath =  $this->optionViewPath;
        $option = QuestionOption::findOrFail($optionId);
        return view('creator.superadmin.quiz-questions.edit-option', compact('questionId', 'option', 'imageFormat', 'optionViewPath'));
        
    }

    public function updateOption (Request $request) {
        
        $request->validate([
            'question_id' => 'required|exists:quiz_questions,id', 
            'option_id'   => 'required|exists:question_options,id', 
            'text'        => 'required|min:2',
            'status'      => 'required',
        ]);

        $quizOption = QuestionOption::findOrFail($request->option_id);
    
        $update = [
                    'question_id' => $request->question_id,
                    'option_text' => $request->text,
                    'is_correct'  => $request->status,
                  ];

        if($request->hasFile('media')) {
            //delete file from storage
            if($quizOption->media != null && $quizOption->media != '') {
                $mediaPath = $this->optionStoragePath . $quizOption->media;
                if(file_exists($mediaPath)) {
                    unlink($mediaPath);
                }
                
            }    

            $mediaFormats = implode(',', Config::get("constant.QUESTION_MEDIA_FROMATS"));
            request()->validate([
                'media' => 'mimes:' . $mediaFormats . '|max:1048576' // 1048 mb
            ]);

            $file = $request->file('media');
            
            $mediaType = $this->getMediaType($file);
            
            $extension = $file->getClientOriginalExtension();
            $fileName = md5(microtime()) .'.'. $extension;
            $file->move($this->optionStoragePath, $fileName);
            $quizOption->media = $fileName;

                
            $update_media = [   
                'media_type' => $mediaType,
                'media' =>  $fileName,
            ];

            $update = array_merge($update, $update_media);

        }    

        $isUpdated =  QuestionOption::where('id', $request->option_id)->update($update);
        
        if($isUpdated == 0)
        {
             return redirect()->back()->with("error",  \Lang::get('lang.unable-to-update') ); 
        }

        return redirect("superadmin/quiz-question/option/add/" .$request->question_id)->with("success",  \Lang::get('lang.question-option') .' '. \Lang::get('lang.updated-successfully'));        
    }
}
