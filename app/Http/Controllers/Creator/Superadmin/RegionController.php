<?php

namespace App\Http\Controllers\Creator\Superadmin;

use Log;
use Exception;
use Validator;
use App\Models\{ Region, Country };
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

class RegionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $countries = Country::where('status', 1)->orderBy('name')->get(); 
        $search = trim($request->query('name'));
        $counrty = trim($request->query('country'));
        $selectedCountry = $counrty ? $counrty : -1;
      
        if ($request->ajax())
        {
            $query = Region::with('country');

            if (!empty($search)) {
                $query =  $query->where('name', 'like', '%' . $search . '%');
            } elseif($selectedCountry != -1) {
                $query = $query->where('country_id', $selectedCountry);
            } 
            $regions = $query->where('status', 1)->orderBy('name')->get();
            return Datatables::of($regions)
            ->addIndexColumn()
            ->editColumn('country', function($regions)
            {
               return $regions->country->name  ? ucfirst($regions->country->name ) : \Lang::get('lang.all');
            })
            ->addColumn('action', function($regions){
                $href = url('/superadmin/regions/'. $regions->id);
                $edithref = url('/superadmin/regions/'. $regions->id);
                $edit = \Lang::get('lang.edit');
                $delete = \Lang::get('lang.delete');

                $actionBtn = "<button class='editRegion' type='submit' data-backdrop='static' data-keyboard='false' 
                data-id='$regions->id' data-region='$regions->name' data-country='$regions->country_id' data-href='$edithref'>
                <i class='fa fa-pencil' aria-hidden='true'></i>  $edit </button> <button type='button' class='text-danger delete-user' data-href='$href' data-role='superadmin'><i class='fa fa-trash' aria-hidden='true'></i> $delete </button>";
               return $actionBtn;
            })
            ->editColumn('created_at', function($regions)
            {
               return (date('d M Y', strtotime($regions->created_at)));
            })
            ->rawColumns(['action'])
            ->make(true);
        }

        return view('creator.superadmin.region.index', compact('countries'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countries = Country::all();
        return view('creator.superadmin.region.create', compact('countries'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            
            $validator = Validator::make($request->all(), [
                'name' => 'required|min:4',
                'country' => 'required|exists:countries,id'
            ]);
            
            if (!$validator->passes()) {
                return response()->json(['success' => false, "message" => $validator->errors()], 200);
            } 

            //check region for same country
            $where = ['country_id' => $request->country, 'name' => $request->name, 'status' => 1];
            $regionExist = Region::where($where)->first();
           
            if(!(is_null($regionExist))){
                return response()->json(['success' => false, "message" => \Lang::get('lang.region-exist')], 200);
                
            } else {
                $region = new Region();
                $region->name = $request->name;
                $region->country_id = $request->country;
                $region->save();
                return response()->json(['success' => true, "message" => "{{ __('lang.region-added') }}"], 200);
            }

        }  catch (Exception $e) {
            Log::error($e);
            return response()->json(['success' => false, "message"=> \Lang::get('lang.generic-error')], 200);
        }      
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $region = Region::with('country')->findOrfail($id);

        return view('creator.superadmin.region.show', compact('region'));
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
        try 
        {
                $validator = Validator::make($request->all(), [
                    'id' => 'required|exists:regions,id',
                    'name' => 'required|min:4',
                ]);
            
                if (!$validator->passes()) {
                    return response()->json(['success' => false, "message" => $validator->errors()], 200);
                } 
               
                //check region for same country
                $where = ['country_id' => $request->country, 'name' => $request->name];
                $regionExist = Region::where($where)->first();
                
                if(!(is_null($regionExist))){
                    return response()->json(['success' => false, "message" => \Lang::get('lang.region-exist')], 200);
                    
                } else {
                    $update = [
                                'name' => $request->name,
                                'id' => $request->id,
                                'country_id' => $request->country
                    ];

                    $isUpdated = Region::where('id', $request->id)
                                        ->update($update);

                    if($isUpdated == 0)
                    {
                        return response()->json(['success' => false, "message" => "{{ __('lang.region-not-updated') }}"], 500);
                    }
                
                    return response()->json(['success' => true, "message" => "{{ __('lang.region-updated') }}"], 200);
                }    
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['success' => false, "message"=> \Lang::get('lang.generic-error')], 200);
        }  
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $region = Region::with(['users' => function($q){
                         $q->where('status', 1);
        }])->findOrfail($id);

        if(count($region->users) > 0)
        {
           return redirect()->back()->with('error',  \Lang::get('lang.already-assigned'));  

        }
        $update = [
                    'status' => 0,
                    'deleted_at' => date("Y-m-d h:i:s")
        ];

        $isdeleted = Region::where('id', $id)
                           ->update($update);


        if($isdeleted == 0) {
            throw new \Exception(\Lang::get('lang.unable-to-delete'));  
        } 

        return back()->with('success', \Lang::get('lang.region') .' '. \Lang::get('lang.deleted-successfully')); 
    }

    public function getRegionsByCountry ($id) 
    {
        if($id == -1){
            $region = Region::select('id', 'name')->where('status', 1)->orderBy('name')->get();
        } else {     
            $region = Region::whereIn('country_id', explode(',', $id))->select('id', 'name')->where('status', 1)->orderBy('name')->get();
        }
        return $region;
    }
}
