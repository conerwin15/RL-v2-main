
@extends('layouts.app')
@section('content')

<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>{{__('lang.show-faq')}}</h2>
        </div>
        <div class="pull-right">
            <a class="btn btn-primary" href="{{ route('faqs.index') }}"> {{__('lang.back')}}</a>
        </div>
    </div>
</div>
<br>

<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>{{__('lang.question')}}:</strong>
            {{ $faq->question }}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>{{__('lang.answer')}}:</strong>
            {{ $faq->answer }}
        </div>
    </div>
</div>
@endsection