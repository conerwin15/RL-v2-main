@extends('layouts.app')

@section('content')
<div class="dash-title container-fluid mt-2">
    <div>
        <a href="{{ url('/superadmin/faqs') }}"><b>{{ __('lang.faq') }} &gt;</b></a> 
        <span class="bradcrumb">{{ __('lang.edit-faq')}}</span>
    </div>
</div>

    <form action="{{ url('/superadmin/faqs/'.$faq->id) }}" method="POST" class="white-wrapper" enctype="multipart/form-data" id="edit-faqs">
    	@csrf
        @method('PUT')

        <input type="hidden" name="id" value="{{$faq->id}}">
        <div class="row col-sm-8">
			<div class="col-xs-6 col-sm-6 col-md-6">
				<div class="form-group d-flex">
					<label class="col mt-2">{{__('lang.category')}}:</label>
						<select  name="category" class="form-control select mb-0" required>
							<option value="" disabled selected> {{__('lang.select-category')}} </option>
								@foreach ($faqCategories as $faqCategory)
								<option value="{{$faqCategory->id}}" {{($faq->faq_category == $faqCategory->id) ? 'selected' : '' }}>{{ ucfirst($faqCategory->faq_category) }}</option>
								@endforeach
						</select>
				</div>			
		    </div>
		    <div class="col-xs-12 col-sm-12 col-md-12">
		        <div class="form-group d-flex">
		            <lable class="col mt-2">{{__('lang.question')}}:</lable>
		            <input type="text" name="question" style="border:0;border-bottom:1px solid #ccc" class="form-control" placeholder="{{__('lang.question')}}" value="{{ $faq->question }}" required>
                     @if($errors->has('question'))
                        <div class="errorMsg" id="questionError">{{ $errors->first('question') }}</div>
                    @endif
		        </div>
		    </div>
		    <div class = "col-xs-12 col-sm-12 col-md-12">
		        <div class="form-group  d-flex">
		            <lable class="col mt-2">{{__('lang.answer')}}:</lable>
		            <textarea style="border:0;border-bottom:1px solid #ccc"  class="ml-2 form-control" rows="4" name="answer" placeholder = "{{__('lang.answer')}}" required>{{ $faq->answer }}</textarea>
                     @if($errors->has('answer'))
                        <div class = "errorMsg" id = "answerError">{{ $errors->first('answer') }}</div>
                    @endif
		        </div>
		    </div>
		    <div class="col-xs-12 col-sm-12 col-md-12">
		            <button type="submit" class="btn-theme">{{__('lang.submit')}}</button>
		    </div>
		</div>
  </form>
@endsection
