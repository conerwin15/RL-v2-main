@extends('layouts.app')


@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>{{__('lang.create-user')}}</h2>
        </div>
        <div class="pull-right">
            <a class="btn btn-primary" href="{{ route('users.index') }}"> {{__('lang.back')}}</a>
        </div>
    </div>
</div>
<br>

<form action="{{ route('users.store') }}" method="POST" id='add_user' >
    @csrf
    <div class="row">

    <div class="row col-xs-12 col-sm-12 col-md-12">
             
                <div class="form-group">
                      <strong>{{__('lang.select-country')}}:</strong>
                      <div>
                       <select  name="country_id" id="country" onchange="getRegion()"  required>
                        
                        <option value="0"> {{__('lang.select-country')}} </option>
                      @foreach ($countries as $country)
                      <option value="{{$country->id}}">{{ $country->name }}</option>
                     @endforeach
                      </select>
                @if($errors->has('country_id'))
                    <div class="errorMsg" id="country_idError">{{ $errors->first('country_id') }}</div>
                @endif
                      </div>
                </div>
    </div>  
    
     <div class="row col-xs-12 col-sm-12 col-md-12">
         <div class="form-group">
            <strong>{{__('lang.region')}}:</strong>
            <div>
            <select  name="region_id" id="region"  required>
              <option>{{__('lang.select-region')}}</option>
            </select>
                @if($errors->has('region_id'))
                    <div class="errorMsg" id="region_idError">{{ $errors->first('region_id') }}</div>
                @endif
            </div>
                
         </div>   
    </div>   
    <div class="row col-xs-12 col-sm-12 col-md-12">
             
        <div class="form-group">
                      <strong>{{__('lang.select-dealer')}}:</strong>
                      <div>
                       <select  name="dealer" id="dealer"  required>
                        
                        <option value="0"> {{__('lang.select-dealer')}} </option>
                          @foreach ($dealers as $dealer)
                          <option value="{{$dealer->id}}">{{ $dealer->name }}</option>
                         @endforeach
                      </select>
                      </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>{{__('lang.name')}}:</strong>
            <input type="text" name="name" class="form-control" placeholder="{{__('lang.name')}}" value="{{ old('name') }}" required>
            @if($errors->has('name'))
                    <div class="errorMsg" id="nameError">{{ $errors->first('name') }}</div>
            @endif
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>{{__('lang.email')}}:</strong>
             <input type="email" name="email" class="form-control" placeholder="{{__('lang.email')}}" value="{{ old('email') }}" required>
            @if($errors->has('email'))
                    <div class="errorMsg" id="emailError">{{ $errors->first('email') }}</div>
            @endif
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>{{__('lang.password')}}:</strong>
             <input type="password" name="password" class="form-control" placeholder="{{__('lang.password')}}" value="{{ old('password') }}" required>
         
            @if($errors->has('password'))
                    <div class="errorMsg" id="passwordError">{{ $errors->first('password') }}</div>
            @endif
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>{{__('lang.confirm-password')}}:</strong>
             <input type="password" name="confirm-password" class="form-control" placeholder="{{__('lang.confirm-password')}}" value="{{ old('confirm-password') }}" required>
           
            @if($errors->has('confirm-password'))
                    <div class="errorMsg" id="confirm-passwordError">{{ $errors->first('confirm-password') }}</div>
            @endif
        </div>
    </div>
   
     <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
           <strong>{{__('lang.job-role')}}:</strong>
                <div>
                    <select id="jobrole" name="job_role_id">
                    @foreach ($jobRoles as $jobRole)
                    <option value="{{$jobRole->id}}">{{ $jobRole->name }}</option>
                    @endforeach
                    </select>
                </div>
    </div>

    <div class="col-xs-12 col-sm-12 col-md-12 text-center">
        <button type="submit" class="btn btn-primary">{{__('lang.submit')}}</button>
    </div>
</div>
</form>



@endsection