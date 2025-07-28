 
 @extends('layouts.app')

 @section('content')
 <main>
 <div class="dash-title container-fluid">
 <b>{{ __('lang.mark-as-featured') }}</b>
</div>

 <form action="{{ url('/superadmin/mark-featured/content') }}" method="POST">

            <div class="white-wrapper">
                @csrf
                <input type="hidden" name="userId" value="{{$id}}">
                <div class="col-sm-12 mb-3">
                        <label>{{ __('lang.mark-as-featured') }}:</label>
                        <textarea class="form-control" rows="10" name="featured_text" placeholder="{{ __('lang.featured-trainee-text') }}" maxlength = "1200" required>{{ $trainee->featured_text }}</textarea>
                            @if($errors->has('description'))
                                <div class="errorMsg" id="featuredTextError">{{ $errors->first('featured_text') }}</div>
                            @endif
                </div>
                <div class="col-sm-12">
                    <button type="submit" class="btn-theme" id="submit">{{ __('lang.update-featured-content') }}</button>
                </div> 
            </div>    
 </form>

 
</main>