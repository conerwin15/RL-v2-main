
 @extends('layouts.app')

 @section('content')

 <main>
 <div class="dash-title container-fluid max-width">
 <b>{{ __('lang.mark-as-featured') }}</b>
</div>
 <form action="{{ url('/admin/mark-featured') }}" method="POST" class="container-fluid max-width">

            <div class="white-wrapper">
                @csrf
                <input type="hidden" name="userId" value="{{$userid}}">
                <div class="col-sm-12 mb-3">
                        <label>{{ __('lang.mark-as-featured') }}:</label>
                        <textarea class="form-control" rows="4" name="featured_text" placeholder="{{ __('lang.featured-trainee-text') }}" maxlength = "1200" required> {{old('featured_text')}}</textarea>
                        @if($errors->has('description'))
                            <div class="errorMsg" id="featuredTextError">{{ $errors->first('featured_text') }}</div>
                        @endif
                        <span style="color:red">* {{ __('lang.max-char-lenth-mark-featured') }} </span>
                </div>
                <div class="col-sm-12">
                    <button type="submit" class="btn-theme" id="submit">{{ __('lang.mark-as-featured') }}</button>
                    <button type="reset" class="btn-theme">{{ __('lang.cancel') }}</button>
                </div>
            </div>
 </form>
