<?php

namespace App\Http\Controllers\Creator\Superadmin;

use PDF;
use App;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{ Certificate };
use Yajra\DataTables\DataTables;

class CertificateController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()) {
            $searchByName = trim($request->query('name'));
            $query = Certificate::orderBy('name', 'asc');
            if (!empty($searchByName)) {

                $query = $query->where('name', 'like', '%' . $searchByName . '%');
            }
        
            $certificates = $query->get();

            return Datatables::of($certificates, $request)
            ->addIndexColumn()
            ->addColumn('action', function($certificates){
                $href = 'certificate/'. $certificates->id . '/preview';
                $editHref = 'certificates/'. $certificates->id . '/edit';
                $edit = \Lang::get('lang.edit');
                $preview = \Lang::get('lang.preview');
                $actionBtn = "<a href='$href'><i class='fa fa-eye' aria-hidden='true'></i> &nbsp;$preview </a><a href='$editHref'><i class='fa fa-pencil' aria-hidden='true'></i> &nbsp;$edit </a>";
                return $actionBtn;
            })
            ->rawColumns(['action'])
            ->make(true);
        }
        return view( 'creator.superadmin.certificate.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $showVaraibales  = array("{{learnername}}", "{{pathname}}" );
        return view('creator.superadmin.certificate.create',compact("showVaraibales"));
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
                'name'    => 'required|min:6',
                'content' => 'required'
        ]);

        $certificate          = new Certificate;
        $certificate->name    = $request->name;
        $certificate->content = $request->content;
        $certificate->save();

        return redirect("superadmin/certificates")->with('success',\Lang::get('lang.certificate').' '.\Lang::get('lang.created-successfully'));
          
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $certificate = Certificate::where('id',$id)->first();
        $showVaraibales  = array("{{learnername}}", "{{pathname}}");
        return view('creator.superadmin.certificate.edit', compact('certificate','showVaraibales'));
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
            'id'      => 'required|exists:certificates,id', 
            'name'    => 'required|min:6',
            'content' => 'required'
        ]);

        $update = [
                 'name' => $request->name,
                 'content' => $request->content
        ];

        $isUpdated = Certificate::where('id', $request->id)
                                ->update($update);
        if($isUpdated == 0)
        {
             return redirect()->back()->with("error",  \Lang::get('lang.unable-to-update') ); 
        }

        return redirect("superadmin/certificates")->with("success",  \Lang::get('lang.certificate') .' '. \Lang::get('lang.updated-successfully')); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $certificate = Certificate::findOrfail($id);
        $certificate->delete();
        return redirect()->back()->with("success",  \Lang::get('lang.certificate') .' '. \Lang::get('lang.deleted-successfully')); 
    }


    /**
    * Download PDF
    */

    public function previewPDF($id)
    {
        $user = Auth::user();
        $certificate = Certificate::findOrfail($id);
        if($certificate->is_master == true) {
            $pdf = PDF::loadView('creator.superadmin.certificate.master', ['learnername' => 'Learner Name', 'pathname' => 'Learning Path Name'])->setPaper('letter', 'landscape');
        } else {
            $pdf = PDF::loadView('creator.superadmin.certificate.pdf', compact('certificate'));
        }
        return $pdf->stream('certificate.pdf'); 
    }
}
