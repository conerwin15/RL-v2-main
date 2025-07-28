@extends('layouts.app')

@section('content')
<div class="dash-title container-fluid mt-2">
    <div>
        <a href="{{ url('/superadmin/faqs') }}"><b>{{ __('lang.faq') }} &gt;</b></a> 
        <span class="bradcrumb">{{ __('lang.add-faq')}}</span>
    </div>
</div>

    <form action="{{ url('/superadmin/faqs') }}" method="POST" enctype="multipart/form-data" id="add-faqs" class="white-wrapper">
    	@csrf
        <div class="row col-sm-8">
			<div class="col-xs-6 col-sm-6 col-md-6">
		        <div class="form-group">
		            <label>{{__('lang.FAQ-category')}}:</label>
					<select  name="category" class="form-control select mb-0" required>
						<option value="" disabled selected> {{__('lang.select-category')}} </option>
							@foreach ($faqCategories as $faqCategory)
							<option value="{{$faqCategory->id}}" >{{ ucfirst($faqCategory->faq_category) }}</option>
							@endforeach
                    </select>
		        </div>
		    </div>
		    <div class="col-xs-12 col-sm-12 col-md-12">
		        <div class="form-group">
		            <label>{{__('lang.question')}}:</label>
		            <input type="text" name="question" class="form-control" placeholder="{{__('lang.question')}}" value="{{ old('question') }}" required>
                     @if($errors->has('question'))
                        <div class="errorMsg" id="questionError">{{ $errors->first('question') }}</div>
                    @endif
		        </div>
		    </div>
		    <div class="col-xs-12 col-sm-12 col-md-12">
		        <div class="form-group">
		            <label>{{__('lang.answer')}}:</label>
		            <textarea class="form-control" style="height:150px" name="answer" placeholder="{{__('lang.answer')}}" value="{{ old('answer') }}" required></textarea>
                     @if($errors->has('answer'))
                        <div class="errorMsg" id="answerError">{{ $errors->first('answer') }}</div>
                    @endif
		        </div>
		    </div>
		    <div class="col-xs-12 col-sm-12 col-md-12">
		            <button type="submit" class="btn-theme">{{__('lang.submit')}}</button>
		    </div>
		</div>
  </form>
@endsection
