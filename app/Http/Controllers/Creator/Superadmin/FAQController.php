<?php


namespace App\Http\Controllers\Creator\Superadmin;

use Auth;
use Exception;
use Validator;
use App\Models\{ Faq, FAQCategory };
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Yajra\DataTables\DataTables;

class FaqController extends Controller
{
     
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $faqCategories = FAQCategory::orderBy('faq_category')->get();
        if ($request->ajax())
        {
            $search = trim($request->query('name'));
            $query = Faq::where('status',1);
            if (!empty($search)) {
                $query->where('question', 'like', '%' . $search . '%');
            }
            if (!empty($request->category) && ($request->category != -1)) {
                $query->where('faq_category', 'like', '%' . $request->category . '%');
            }

            $faqs = $query->orderBy('question')->get();
            return Datatables::of($faqs)
            ->addIndexColumn()
            ->addColumn('action', function($faqs){
                $href = url('/superadmin/faqs/'. $faqs->id);
                $edithref = url('/superadmin/faqs/'. $faqs->id. '/edit');
                $edit = \Lang::get('lang.edit');
                $delete = \Lang::get('lang.delete');

                $actionBtn = "<a href='$edithref'><i class='fa fa-pencil' aria-hidden='true'></i> $edit </a>
                <button type='button' class='text-danger delete-user' data-href='$href' data-role='superadmin'><i class='fa fa-trash' aria-hidden='true'></i> $delete </button>";
               return $actionBtn;
            })
            ->rawColumns(['action'])
            ->make(true);
        }
        return view('creator.superadmin.faqs.index', compact('faqCategories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $faqCategories = FAQCategory::orderBy('faq_category')->get();
        return view('creator.superadmin.faqs.create', compact('faqCategories'));
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
            'category' => 'required|exists:faq_categories,id',
            'question' => 'required|min:6',
            'answer' => 'required|min:6',
        ]); 

        $faq           = new Faq;
        $faq->faq_category = $request->category;
        $faq->question = $request->question;
        $faq->answer   = $request->answer;
        $faq->save();

        if($request->ajax()) {
                $response = [
                'success' => true,
                'message' => \Lang::get('lang.faq').' '.\Lang::get('lang.created-successfully')
            ];
                return response()->json($response, 200);

        } else {

            return redirect('superadmin/faqs')
                            ->with('success',\Lang::get('lang.faq').' '.\Lang::get('lang.created-successfully')); 
        } 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id = null)
    {
        $faq = Faq::findOrFail($id);
        return view('creator.superadmin.faqs.show',compact('faq'));    
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Faq $faq)
    {
        $faqCategories = FAQCategory::orderBy('faq_category')->get();
        return view('creator.superadmin.faqs.edit', compact('faq', 'faqCategories'));
    }
  
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id = null)
    {
        request()->validate([
            'id'       => 'required|exists:faqs,id',
            'category' => 'required|exists:faq_categories,id',
            'question' => 'required|min:6',
            'answer'   => 'required|min:6'
        ]);

        $update = [ 'faq_category' => $request->category,
                    'question' => $request->question,
                    'answer'   =>  $request->answer,
                    ];
        Faq::where('id', $request->id)
            ->update($update);

            if($request->ajax()) {
            $response = [
                'success' => true,
                'message' => \Lang::get('lang.faq').' '.\Lang::get('lang.updated-successfully'),
            ];
                return response()->json($response, 200);

        } else {
            return redirect('superadmin/faqs')
                            ->with('success',\Lang::get('lang.faq').' '.\Lang::get('lang.updated-successfully'));    
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Faq $faq)
    {
        $isDeleted = $faq->delete();
       
        if($isDeleted == 0)
        {
            return redirect()->back()->with('error', \Lang::get('lang.unable-to-delete'));
        }  

        return redirect()->back()
                ->with('success',\Lang::get('lang.faq').' '.\Lang::get('lang.deleted-successfully'));
    }

    /**
     * Show the form for creating a new faq category.
     *
     * @return \Illuminate\Http\Response
     */

    public function faqCategories(Request $request)
    {
        if ($request->ajax())
        {
            $searchByName = trim($request->query('name'));
            $query = FAQCategory::where('status', 1);
            if (!empty($searchByName)) {
                $query = $query->where('faq_category', 'like', '%' . $searchByName . '%');
            }

            $faqCategories = $query->orderBy('faq_category')->get();
            return Datatables::of($faqCategories)
            ->addIndexColumn()
            ->addColumn('action', function($faqCategories){
                $href = url('/superadmin/faq/categories/delete/'. $faqCategories->id);
                $edithref = url('/superadmin/faq/categories/update/'. $faqCategories->id);
                $edit = \Lang::get('lang.edit');
                $delete = \Lang::get('lang.delete');

                $actionBtn = "<button class='editFAQCategory' type='submit' data-backdrop='static' data-keyboard='false' 
                data-id='$faqCategories->id' data-name='$faqCategories->faq_category' data-href='$edithref' >
                <i class='fa fa-pencil' aria-hidden='true'></i>  $edit </button> <button type='button' class='text-danger delete-user' data-href='$href' data-role='superadmin'><i class='fa fa-trash' aria-hidden='true'></i> $delete </button>";
               return $actionBtn;
            })
            ->editColumn('created_at', function($faqCategories)
            {
               return (date('d M Y', strtotime($faqCategories->created_at)));
            })
            ->rawColumns(['action'])
            ->make(true);
        }
        return view('creator.superadmin.faqs.faq-category');
    } 


    public function addFAQCategory(Request $request)
    {
        try 
        {
            $validator = Validator::make($request->all(), [
                'name' => 'required|min:4',
            ]); 

            if (!$validator->passes()) {
                return response()->json(['success' => false, "messsage" => $validator->errors()], 200);
            } 

            $faqCategory = new FAQCategory;
            $faqCategory->faq_category = $request->name;
            $faqCategory->save();

            return response()->json(['success' => true, "messsage" => "{{ __('lang.FAQ-category') }}"], 200);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['success' => false, "messsage"=> \Lang::get('lang.generic-error')], 200);
        }
    }

    public function updateFAQCategory(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:faq_categories,id',
                'name' => 'required|min:4'
            ]);
            
            if (!$validator->passes()) {
                return response()->json(['success' => false, "messsage" => $validator->errors()], 200);
            } 

            $isUpdated = FAQCategory::where('id', $request->id)
                                ->update(['faq_category' => $request->name]);

            if($isUpdated == 0)
            {
                return response()->json(['success' => true, "messsage" => "{{ __('lang.FAQ-category-not-updated') }}"], 500);
            }
        
            return response()->json(['success' => true, "messsage" => "{{ __('lang.FAQ-category-updated') }}"], 200);
        }  catch (Exception $e) {
            Log::error($e);
            return response()->json(['success' => false, "messsage"=> \Lang::get('lang.generic-error')], 200);
        }   
    }

    public function deleteFAQCategory(Request $request, $id)
    {

        // delete faq list
        $faqList = Faq::where('faq_category', $id)->delete();
    
        // delete faq category
        $faqCategory = FAQCategory::findOrfail($id);
        $isDeleted = $faqCategory->delete();
        
        if($isDeleted == 0)
        {
            return redirect()->back()->with('error', \Lang::get('lang.unable-to-delete'));
        } 

        return redirect()->back()
                ->with('success',\Lang::get('lang.FAQ-category').' '.\Lang::get('lang.deleted-successfully'));
    }
}