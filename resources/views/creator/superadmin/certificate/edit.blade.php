@extends('layouts.app')


@section('content')
<div class="dash-title container-fluid mt-2">
    <div>
        <a href="{{ url('/superadmin/certificates') }}"><b>{{ __('lang.certificate') }} &gt;</b></a>
        <span class="bradcrumb">{{ __('lang.edit_certificate') }}</span>
    </div>
</div>
<form action="{{ url('superadmin/certificates/' . $certificate->id) }}" method="POST"
    id="edit_certificate">
    @csrf
    @method('PUT')
    <div class="container-fluid">
        <div class="white-wrapper ">
            <strong class="color">{{ __('lang.hint') }} :</strong> <br>
            <span class="text-gray">{{ __('lang.certificate-hint-text') }}</span>
            <br><br>

            <ol>
                @foreach($showVaraibales as $variable)
                    <li class="text-gray">{{ $variable }}</li>
                @endforeach

            </ol>
        </div>
    </div>
        <div class="container-fluid">
            <div class="white-wrapper ">
            

            <div class="row">
                <input type="hidden" name="id" value="{{ $certificate->id }}">
                <div class="col-xs-12 col-md-7">
                    <div class="form-group">
                        <label>{{ __('lang.name') }}:</label>
                        <input type="text" name="name" class="form-control" id="name"
                            placeholder="{{ __('lang.name') }}" value="{{ $certificate->name }}"
                            required>
                        @if($errors->has('name'))
                            <div class="errorMsg" id="nameError">{{ $errors->first('name') }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>{{ __('lang.content') }}:</strong>
                        <textarea class="form-control" name="content" value="{{ $certificate->content }}"
                            id="description" required>{!! $certificate->content !!}</textarea>
                        @if($errors->has('content'))
                            <div class="errorMsg" id="prescriptionError">
                                {{ $errors->first('content') }}</div>
                        @endif
                    </div>
                </div>


                <div class="col-xs-12 col-sm-12 col-md-12 text-right">
                    <button type="submit" class="btn-theme">{{ __('lang.submit') }}</button>
                    <button type="reset" name="reset"  class="btn-theme-border">{{ __('lang.cancel') }}</button>
                </div>
            </div>
        </div>
    </div>

</form>


@section('scripts')
<script src="//cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        CKEDITOR.replace( 'description', {
            removeButtons: 'PasteFromWord',
            removePlugins: 'image, sourcearea, specialchar, horizontalrule, pastetext, pastefromword, blockquote, link'  
        });
    });

</script>

@endsection
@endsection
