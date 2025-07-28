
@extends('layouts.app')
@section('content')

<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>{{__('lang.show-region')}}</h2>
        </div>
        <div class="pull-right">
            <a class="btn-theme" href="{{ url('/superadmin/regions/') }}"> {{__('lang.back')}}</a>
        </div>
    </div>
</div>
<br>

<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>{{__('lang.country')}}:</strong>
            {{ $region->country->name }}
        </div>
    </div>

    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>{{__('lang.region')}}:</strong>
            {{ $region->name }}
        </div>
    </div>

</div>
@endsection
