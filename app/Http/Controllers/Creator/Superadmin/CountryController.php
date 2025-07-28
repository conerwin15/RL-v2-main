<?php

namespace App\Http\Controllers\Creator\Superadmin;

use Auth;
use Log;
use Validator;
use Exception;
use App\Models\ {Country, Region, Quiz};
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;


class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax())
        {
            $searchByName = trim($request->query('name'));
            $query = Country::with(['createdBy'])->withCount('regions')->where('status', 1);
            if (!empty($searchByName)) {

                $query = $query->where('name', 'like', '%' . $searchByName . '%');
            }

            $countries = $query->orderBy('name')->get();
            return Datatables::of($countries)
            ->addIndexColumn()
            ->addColumn('action', function($countries){
                $href = url('/superadmin/countries/'. $countries->id);
                $edithref = url('/superadmin/countries/'. $countries->id);
                $view = \Lang::get('lang.view');
                $edit = \Lang::get('lang.edit');
                $delete = \Lang::get('lang.delete');

                $actionBtn = "<a href='$href'><i class='fa fa-eye' aria-hidden='true'></i> $view </a>
                <button class='editCountry' type='submit' data-backdrop='static' data-keyboard='false'
                data-id='$countries->id' data-country='$countries->name' data-href='$edithref'>
                <i class='fa fa-pencil' aria-hidden='true'></i>  $edit </button> <button type='button' class='text-danger delete-user' data-href='$href' data-role='superadmin'><i class='fa fa-trash' aria-hidden='true'></i> $delete </button>";
               return $actionBtn;
            })
            ->editColumn('created_at', function($countries)
            {
               return (date('d M Y', strtotime($countries->created_at)));
            })
            ->rawColumns(['action'])
            ->make(true);
        }

        return view('creator.superadmin.country.index');
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
                'name' => 'required|min:2'
            ]);

            if (!$validator->passes()) {
                return response()->json(['success' => false, "messsage" => $validator->errors()], 200);
            }

            $countryExist = Country::where('name', $request->name)->first();

            // check if country exist with status 1
            if(!empty($countryExist) && ($countryExist['status'] == 1)) {

                return response()->json(['success' => false, "messsage" => \Lang::get('lang.name-already-exist')], 200);

            // check if country exist with status 0
            } else if(!empty($countryExist) && $countryExist['status'] == 0) {

                $statusUpdat = Country::where('id', $countryExist['id'])
                                       ->update(['status' => 1]);
                return response()->json(['success' => true, "messsage" => "{{ __('lang.country-added') }}"], 200);
            } else {
                $country = new Country();
                $country->name = $request->name;
                $country->save();
                return response()->json(['success' => true, "messsage" => "{{ __('lang.country-added') }}"], 200);
            }
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['success' => false, "messsage"=> \Lang::get('lang.generic-error')], 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $countryId)
    {
        $country = Country::findOrfail($countryId);
        $where = [
                    'country_id' => $countryId,
                    'status' => 1
                ];
        $regions = Region::where($where)->orderBy('name')->get();

        if ($request->ajax())
        {
            return Datatables::of($regions)
            ->addIndexColumn()
            ->editColumn('name', function($regions)
            {
               return $regions->name;
            })
            ->editColumn('created_by', function($regions)
            {
               return $regions->createdBy->name;
            })
            ->editColumn('created_on', function($regions)
            {
                return  date('d M Y', strtotime($regions->created_at));
            })
            ->addColumn('action', function($regions){
                $href = url('/superadmin/regions/'. $regions->id);
                $edithref = url('/superadmin/regions/'. $regions->id);
                $view = \Lang::get('lang.view');
                $delete = \Lang::get('lang.delete');

                $actionBtn = "<button class='editRegion' type='submit' data-backdrop='static' data-keyboard='false' data-id='$regions->id' data-region='$regions->name' data-href='$edithref'>
                <i class='fa fa-pencil' aria-hidden='true'></i>  Edit </button> <button type='button' class='text-danger delete-user' data-href='$href' data-role='superadmin'><i class='fa fa-trash' aria-hidden='true'></i> $delete </button>";
               return $actionBtn;
            })
            ->editColumn('created_at', function($regions)
            {
               return (date('d M Y', strtotime($regions->created_at)));
            })
            ->rawColumns(['action'])
            ->make(true);
        }
        return view('creator.superadmin.country.show', compact('country', 'countryId' ));
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
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:countries,id',
                'name' => 'required|min:2',
            ]);

            if (!$validator->passes()) {
                return response()->json(['success' => false, "messsage" => $validator->errors()], 200);
            }

            $countryExist = Country::where('name', $request->name)->first();

            if(!empty($countryExist) && $countryExist['status'] == 1) {
                return response()->json(['success' => false, "messsage" => \Lang::get('lang.name-already-exist')], 200);

            } else if(!empty($countryExist) && $countryExist['status'] == 0) {

                // delete existing country with status 0
                $isdeleted = Country::where('id', $countryExist['id'])->delete();

                $isUpdated = Country::where('id', $request->id)
                                     ->update(['name' => $request->name]);
                return response()->json(['success' => true, "messsage" => "{{ __('lang.country-updated') }}"], 200);

            } else {

                $isUpdated = Country::where('id', $request->id)
                                    ->update(['name' => $request->name]);
                if($isUpdated == 0)
                {
                    return response()->json(['success' => true, "messsage" => "{{ __('lang.country-not-updated') }}"], 500);
                }

                return response()->json(['success' => true, "messsage" => "{{ __('lang.country-updated') }}"], 200);
            }
        }  catch (Exception $e) {
            Log::error($e);
            return response()->json(['success' => false, "messsage"=> \Lang::get('lang.generic-error')], 200);
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
        $country = Country::with(['users' => function ($q) {
                          $q->where('status', 1) ;
           }])->with('regions')->findOrfail($id);

        if(count($country->users) > 0)
        {
           return redirect()->back()->with('error',  \Lang::get('lang.already-assigned'));

        }

        $update = [
            'status' => 0,
            'deleted_at' => date("Y-m-d h:i:s")
        ];

        if(count($country->regions) > 0) {

            foreach($country->regions as $region) {

                $isRegionDeleted = Region::where('id', $region->id)
                                         ->update($update);
            }


        }

        $isCountryDeleted = Country::where('id', $id)
                                ->update($update);

        // update quiz with null country
        $updateQuiz = Quiz::where('country_id', $id)->update(['country_id' => -1]);

        return redirect()->back()->with("success",  \Lang::get('lang.country') .' '. \Lang::get('lang.deleted-successfully'));
    }

}
