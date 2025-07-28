@extends('layouts.app')

@section('content')

<div class="dash-title container-fluid mt-2">
    <div>
        <a href="{{ url('/superadmin/packages') }}"><b>{{ __('lang.packages') }} &gt;</b></a>
        <span class="bradcrumb">{{ __('lang.create-package') }}</span>
    </div>
</div>

<form action="{{ url('superadmin/packages') }}" method="POST" enctype="multipart/form-data" >
    @csrf

        <div class="container-fluid">
            <div class="white-wrapper ">

                <div class="row">
                    <div class="col-sm-2">
                        <label>{{__('lang.category')}}: </label>
                        <select  name="category" id="category" class="form-control select" >
                        <option  disabled selected> {{ __('lang.select') }}  {{ __('lang.category') }} </option>
                            @foreach ($categories as $category)
                                <option value="{{$category->id}}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <label>{{ __('lang.sub-category') }}:</label>
                        <select name="sub_category" id="sub_category" class="select form-control"  required>
                        <option  disabled selected> {{ __('lang.select') }}  {{ __('lang.sub-category') }} </option>
                            @foreach($subCategories as $subCategory)
                                <option value="{{ $subCategory->id }}" id="jobRoleId">
                                    {{ $subCategory->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <br>
                <div class="col-xs-12 col-sm-7">
                    <div class="form-group">
                        <label>{{ __('lang.name') }}:</label>
                        <input type="text" name="name" class="form-control" id="name"
                            placeholder="{{ __('lang.name') }}"
                            value="{{ old('name') }}" required>
                        @if($errors->has('name'))
                            <div class="errorMsg" id="nameError">{{ $errors->first('name') }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-12 col-sm-7">
                    <div class="form-group">
                        <label>{{ __('lang.description') }}:</label>
                        <textarea class="form-control descriptionText" rows="4" name="description" placeholder="{{ __('lang.description') }}" maxlength = "1200" required> {{old('description')}}</textarea>
                        @if($errors->has('description'))
                            <div class="errorMsg" id="quizTextError">{{ $errors->first('description') }}</div>
                        @endif
                    </div>
                </div>

                <div class="form-group col-sm-8">
                    <label> <strong>{{__('lang.upload-picture')}}:</strong></label>
                    <div class="featuredimg">
                        <div>
                            <svg  width="30" height="30" viewBox="0 0 30 30"><g transform="translate(-1 -1)"><path d="M27.92,1H4.08A3.08,3.08,0,0,0,1,4.08V27.92A3.08,3.08,0,0,0,4.08,31H13V29H4.08A1.08,1.08,0,0,1,3,27.92V4.08A1.08,1.08,0,0,1,4.08,3H27.92A1.08,1.08,0,0,1,29,4.08V27.92A1.08,1.08,0,0,1,27.92,29H19v2h8.92A3.08,3.08,0,0,0,31,27.92V4.08A3.08,3.08,0,0,0,27.92,1Z" fill="#0097c4"/><path d="M22.29,15.71a1,1,0,1,0,1.42-1.42L16,6.59l-7.71,7.7a1,1,0,0,0,1.42,1.42L15,10.41V31h2V10.41Z" fill="#0097c4"/></g></svg>
                            <img src="" class="preview">
                            <input type="file" id="file" name="image"  accept="image/x-png,image/jpeg" required/>
                            <div class="mt-2">{{__('lang.drop-file')}}</div>
                        </div>
                    </div>
                    <div class="error" id="fileError">
                    @if($errors->has('image'))
                        <div class="errorMsg" id="descriptionError">{{ $errors->first('image') }}</div>
                    @endif
                    </div>
				</div>

                <div class="col-xs-12 col-sm-7">
                    <div class="form-group">
                        <label>{{ __('lang.price') }}:</label>
                        <input type="text" name="price" class="form-control txtPrice" id="name"
                            placeholder="{{ __('lang.price') }}" value="{{ old('price') }}" required>
                        @if($errors->has('price'))
                            <div class="errorMsg" id="nameError">{{ $errors->first('price') }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-xs-12 col-sm-7">
                    <div class="form-group">
                        <label>{{ __('lang.discounted_price') }}:</label>
                        <input type="text" name="discounted_price" class="form-control txtPrice" id="name"
                            placeholder="{{ __('lang.discounted_price') }}"
                            value="{{ old('discounted_price') }}" required>
                        @if($errors->has('discounted_price'))
                            <div class="errorMsg" id="nameError">{{ $errors->first('discounted_price') }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-sm-12">
                    <button type="submit" class="btn-theme">{{ __('lang.submit') }}</button>
                    <a href="{{url('/superadmin/packages')}}" style="text-decoration:none;"><button type="button" name="reset" class="btn-theme-border">{{ __('lang.cancel') }}</button></a>
                </div>
            </div>
        </div>

</form>

<script src="//cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
<script>
    $(document).ready(function() {
        CKEDITOR.replaceAll( 'descriptionText', {
            removeButtons: 'PasteFromWord',
            removePlugins: 'link, sourcearea, horizontalrule, pastetext, pastefromword, blockquote, specialchar',
            addPlugins: 'smiley, emoji',
        });

        $(".txtPrice").keyup(function() {
            var $this = $(this);
            $this.val($this.val().replace(/[^\d.]/g, ''));
        });
    });

    $(document).on('change','#file' , function(){
        $(this).next('div').html($(this)[0].files[0].name)
        readURL(this);
    })

    $('#category').change(function(){
        var Id = $('#category').val();
        var ajaxurl = app_url + "/superadmin/category/" + Id + "/sub-category" ;

        $.ajax({
            type: 'get',
            url: ajaxurl,
            success: function (data) {
                $("#sub_category").empty();
                console.log(data.length);
                if(data.length > 0){
                    $.each(data,function(key, value) {
                        $('#sub_category').append(`<option value=" ${value.id}" >${value.name}</option>`)
                    });
                } else {
                    $('#sub_category').append(`<option value="" selected>No Record Found</option>`)
                }
            },
            error: function (data) {
            }
        });
    });
</script>
@endsection
