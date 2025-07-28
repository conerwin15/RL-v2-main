@extends('layouts.app')

@section('content')

<div class="dash-title container-fluid max-width">
    <b>{{__('lang.manage-point-setting')}}</b>
</div>  

<div class="clearfix mb-4"></div>

<form action="{{ url('superadmin/settings/') }}" method="POST" id='add_setting'  class="container-fluid max-width white-wrapper" >
    @csrf
   
        <div class="row col-sm-7">
       
            <div class="col-sm-12 mb-3">
                <label>{{__('lang.points_per_activity')}}: <span class="form-required">*</span></label>
                <input type="number" name="points_per_activity" class="form-control" placeholder="{{__('lang.points_per_activity')}}" value="{{ Setting::get('points_per_activity', '0') }}" required/>
                @if($errors->has('points_per_activity'))
                    <div class="errorMsg" id="points_per_activityError">{{ $errors->first('points_per_activity') }}</div>
                @endif
            </div>

            <div class="col-sm-12 mb-3">
                <label>{{__('lang.correct_answer_points')}}: <span class="form-required">*</span></label>
                <input type="number" name="correct_answer_points" class="form-control" placeholder="{{__('lang.correct_answer_points')}}" value="{{ Setting::get('correct_answer_points', '0') }}" required/>
                @if($errors->has('correct_answer_points'))
                    <div class="errorMsg" id="correct_answer_pointsError">{{ $errors->first('correct_answer_points') }}</div>
                @endif
            </div>

        </div>    
           

    <div class="col-xs-12 col-sm-12 col-md-12">
        <button type="submit" class="btn-theme">{{__('lang.submit')}}</button>
    </div>
    </div>
</div>
</form>


@endsection