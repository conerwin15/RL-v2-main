@extends('layouts.app')

@section('content')
<div class="dash-title container-fluid">
    <b>{{__('lang.assign-new-learning-path')}}</b>
</div>

<div class="container-fluid">

    <div class="white-wrapper">

        <div class="d-lg-flex justify-content-between align-items-right" >
            <form class="d-lg-flex justify-content-end align-items-center col-sm-12 pr-0" method="GET">
                @if(isset($_GET['search']))
                    <input type="text"  placeholder="{{__('lang.search-by-name-placeholder')}}" class="form-controller form-control mb-0" id="search" name="search" value="{{$_GET['search']}}">
                @else
                    <input type="text"  placeholder="{{__('lang.search-by-name-placeholder')}}" class="form-controller form-control mb-0" id="search" name="search">
                @endif

                <button type="submit" class="btn-theme ml-2">{{__('lang.search')}}</button>
            </form>
        </div>
        <form action="{{ url('superadmin/package/add/learning-path') }}" method="POST" class="container-fluid">
            @csrf
                <h6 class="mb-3"><b> {{__('lang.learning-paths')}} </b></h6>
                <input type="hidden" name="package_id" value="{{$packageId}}">
                <input type="hidden" name="assignAll" id="assign" value="">
                <div class="table">
                    <table>
                        <thead>
                            <tr>
                                <th width="7%" >
                                    {{__('lang.select-learning-paths')}}
                                </th>
                                <th>{{__('lang.id')}}</th>
                                <th>{{__('lang.image')}}</th>
                                <th>{{__('lang.name')}}</th>
                                <th>{{__('lang.description')}}</th>
                                <th>{{__('lang.created-by')}}</th>
                                <th>{{__('lang.created-on')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($learningPaths)>0)
                            @foreach ($learningPaths as $learningPath)
                                <tr>
                                    <td class="ceck-kbox">
                                        <input type='checkbox' name='assign_learning_paths[]' class='check_learner' value="{{ $learningPath->id }}"> 
                                    </td>
                                    <td>{{++ $index}}</td>
                                    <td><img src="{{ asset('storage' . Config::get('constant.LEARNING_PATH_STORAGE')).'/'.$learningPath->featured_image }}" ></td>
                                    <td>{{$learningPath->name}}</td>
                                    <td>{!! $learningPath->description !!}</td>
                                    <td>{{ucfirst($learningPath->createdBy->name)}}</td>
                                    <td>{{date('d M Y', strtotime($learningPath->created_at))}}</td>
                                </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="11" style="text-align: center;">{{__('lang.no-record')}} </td>
                            </tr>

                        @endif
                        </tbody>
                    </table>
                </div>
                    <div class="text-center">
                        <button type="submit"  id="assignAll" class="btn-theme">{{__('lang.assign-all')}}</button>
                        <button type="submit" class="btn-theme">{{__('lang.assign-selected-learning-paths')}}</button>
                    </div>
            </div>
        </form>
    </div>
</div>
    {{ $learningPaths->appends(request()->query())->links('vendor.pagination.bootstrap-4') }}

@endsection
