@extends('layouts.app')

@section('content')

<style>
.form-field{
  width: 400px;
  height: auto;
  min-height: 34px;
  padding: 8px;
  margin: 8px;
  cursor: text;
}

.form-field .chips .chip {
  display: inline-block;
  width: auto;
  background-color: #0077b5;
  color: #fff;
  border-radius: 3px;
  margin: 2px;
  overflow: hidden;
}
.form-field .chips .chip{
  float: left;
}

.form-field .chips .chip .chip--button {
  padding: 8px;
  cursor: pointer;
  background-color: #004471;
  display: inline-block;
}
.form-field .chips .chip .chip--text {
  padding: 8px;
  cursor: no;
  display: inline-block;
  pointer-events: none
}

.form-field > input{
  padding: 15px;
  display: block;
  box-sizing: border-box;
  width: 100%;
  height: 34px;
  border: none;
  margin: 5px 0 0;
  display: inline-block;
  background-color: transparent;
}

.fileinpurt input:disabled {
    background-color: white;
    opacity: 0;
}
</style>

<div class="piaggio-alert">
        <div id="pathAlert"></div>
</div>

<form action="{{url ($routeSlug .'/learning-paths')}}" method="POST" id="add-learning-path" enctype="multipart/form-data" onsubmit="submitForm(event)">
    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">

    <div class="dash-title container-fluid">
		<div>
            <a  href="{{ url($routeSlug .'/learning-paths') }}"><b>{{__('lang.learning-paths')}} ></b></a>
            <span class="bradcrumb">{{ __('lang.add-learning_path')}}</span>
        </div>
		<button type="submit" class="btn-theme finalSubmit" >
            <i class="fa fa-refresh fa-spin loader" aria-hidden="true" style="display:none"></i> {{__('lang.submit-path-details')}}
        </button>
	</div>

    <div class="container-fluid">
		<div class="white-wrapper">
            <div id="formError" class="error"> </div>
            <div class="row">
                <div class="form-group col-sm-7">
                    <label>{{__('lang.learning-path-title')}} <sup>*</sup>:</label>
                    <input type="text" name="name" class="form-control" id="name" placeholder="{{__('lang.learning-path-title')}}" value="{{ old('name') }}" required>
                    <div class="error" id="nameError"></div>
                </div>
                <div class="form-group col-sm-8">
                    <label>{{__('lang.featured-image')}} ({{ __('lang.max-5mb') }})<sup>*</sup>:</label>
                    <div class="featuredimg">
                        <div>
                            <svg  width="30" height="30" viewBox="0 0 30 30"><g transform="translate(-1 -1)"><path d="M27.92,1H4.08A3.08,3.08,0,0,0,1,4.08V27.92A3.08,3.08,0,0,0,4.08,31H13V29H4.08A1.08,1.08,0,0,1,3,27.92V4.08A1.08,1.08,0,0,1,4.08,3H27.92A1.08,1.08,0,0,1,29,4.08V27.92A1.08,1.08,0,0,1,27.92,29H19v2h8.92A3.08,3.08,0,0,0,31,27.92V4.08A3.08,3.08,0,0,0,27.92,1Z" fill="#0097c4"/><path d="M22.29,15.71a1,1,0,1,0,1.42-1.42L16,6.59l-7.71,7.7a1,1,0,0,0,1.42,1.42L15,10.41V31h2V10.41Z" fill="#0097c4"/></g></svg>
                            <img src="" class="preview">
                            <input type="file" id="file" name="image"  accept="image/x-png,image/jpeg"  required/>
                            <div class="mt-2 image_preview">{{__('lang.drop-file')}}</div>
                        </div>
                    </div>
                    <div class="error" id="fileError"></div>
                </div>

                <div class="form-group col-sm-10">
                    <label>{{__('lang.learning-path')}} {{__('lang.description')}} ({{ __('lang.max-char-lenth-mark-featured') }}) <sup>*</sup>:</label>
                    <textarea class="form-control" rows="4" name="description" id="description" required>{{ old('description') }}</textarea>
                    <div class="error" id="descriptionError"> </div>
                </div>

                <div class="form-group col-sm-7">
                    <label>{{__('lang.bot_only')}}:</label>
                    <input type="checkbox" class="bot-checkbox"  id="is_bot_only" name="is_bot_only" value="1" > &nbsp;{{__('lang.yes')}}
                </div>

                <div class="row form-group col-sm-7">
                    <div class="col-sm-4">
                    <label>{{__('lang.language')}}: </label>
                    <select class="select form-control" name="language" id="language">
                        <option value="1">{{ __('lang.english') }}</option>
                        <option value="2">{{ __('lang.vietnamese') }}</option>
                    </select>
                    </div>
                    <div class="col-sm-4">
                        <label>{{__('lang.type')}}: </label>
                        <select class="select form-control" name="type" id="type" disabled>
                            <option value="LP" id="">{{ __('lang.multi-module') }}</option>
                            <option value="RB" id="">{{ __('lang.reallybot') }}</option>
                        </select>
                    </div>
                </div>
                <br>

                <div class="row form-group col-sm-7">
                    <div class="col-sm-5">
                        <label>{{__('lang.select-category')}}: </label>
                        <select  name="category" id="category" class="form-control select" >
                        <option  disabled selected> {{ __('lang.select') }}  {{ __('lang.category') }} </option>
                            @foreach ($categories as $category)
                                <option value="{{$category->id}}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <div class="error" id="categoryError"></div>
                    </div>
                    <div class="col-sm-5">
                        <label>{{ __('lang.sub-category') }}:</label>
                        <select name="sub_category" id="sub_category" class="select form-control"   required>
                            <option  disabled selected> {{ __('lang.select') }}  {{ __('lang.sub-category') }} </option>
                            @foreach($subCategories as $subCategory)
                                <option value="{{ $subCategory->id }}" id="jobRoleId">
                                    {{ $subCategory->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="error" id="subCategoryError"></div>
                    </div>
                </div>
                <br>
                <div class="form-group col-sm-7">
                    <label>{{__('lang.level')}} <sup>*</sup>:</label>
                    <input type="text" name="level" class="form-control" id="level" placeholder="{{__('lang.level')}}" value="{{ old('level') }}" required>
                    <div class="error" id="nameError"></div>
                </div>

                <div class="form-group col-sm-7">
                    <label>{{__('lang.price')}} <sup>*</sup>:</label>
                    <input type="text" name="price" class="form-control amount" id="price" placeholder="{{__('lang.price')}}" value="{{ old('price') }}" required>
                    <div class="error" id="nameError"></div>
                </div>

                <div class="form-group col-sm-7">
                    <label>{{__('lang.suitable_for')}} <sup>*</sup>:</label>
                    <input type="text" name="suitable_for" class="form-control" id="suitable_for" placeholder="{{__('lang.suitable_for')}}" value="{{ old('suitable_for') }}" required>
                    <div class="error" id="nameError"></div>
                </div>

                <div class="form-group col-sm-7">
                    <label>{{__('lang.duration')}} <sup>*</sup>:</label>
                    <input type="text" name="duration" class="form-control" id="duration" placeholder="{{__('lang.duration')}}" value="{{ old('duration') }}" required>
                    <div class="error" id="nameError"></div>
                </div>

                <div class="form-group col-sm-7">
                    <label>{{__('lang.requirements')}}:</label>
                    <input type="text" class="form-control"  id="requirements" name="requirements" value="">
                </div>

                <div class="form-group col-sm-7">
                    <label>{{__('lang.instructor')}} <sup>*</sup>:</label>
                    <input type="text" name="instructor" class="form-control" id="instructor" placeholder="{{__('lang.instructor')}}" value="{{ old('instructor') }}" required>
                    <div class="error" id="nameError"></div>
                </div>

                <div class="form-group col-sm-7">
                    <label>{{__('lang.uploaded_by')}}<sup>*</sup>:</label>
                    <input type="text" class="form-control"  id="uploaded_by" name="uploaded_by" value="" required>
                </div>

                <div class="form-field form-group col-sm-12">
                    <label>{{__('lang.tags_Keywords')}} <sup>*</sup>:</label>
                    <div class='chips form-group col-sm-7'>
                    </div>
                    <div class="form-group col-sm-7" style="margin-left: -12px;">
                        <div class="flex">
                            <input type="text" name="tags_Keywords" class="form-control chip-input" autocomplete="on" id="tags_Keywords" placeholder="{{__('lang.tags_Keywords')}}" value="{{ old('tags_Keywords') }}">
                            <div class="error" id="tagsError"></div>
                        </div>
                    </div>
                </div>

                <div id="botDiv" class="form-group col-sm-12" style="display:none; margin-left: -11px;">

                        <div class="form-group col-sm-4">
                            <label>{{ __('lang.chatbots') }}:</label>
                            <select name="chatbot" id="chatbot" class="select form-control">
                                <option  disabled> {{ __('lang.select') }}  {{ __('lang.chatbot') }} </option>
                            </select>
                        </div>

                        <div class="form-group col-sm-7">
                            <label>{{__('lang.iframe_link')}} <sup>*</sup>:</label>
                            <input type="text" class="form-control" rows="4" name="iframe_link" id="iframe_link">
                            <div class="error" id="iframe_linkError"> </div>
                        </div>
                </div>

                <div class="form-group col-sm-12" id="resourceDiv">
                    <div class="row">
                        <div class="resource-select col-sm-5">
                            <label for="">{{__('lang.add-resources')}} <sup>*</sup>:</label>
                            <select id="select-link" class="form-control select">
                                <option value="0">{{__('lang.select-option')}}</option>
                                <option value="chatbot_link">{{__('lang.chatbot-link')}}</option>
                                <option value="course_link">{{__('lang.course-file')}}</option>
                                <option value="media_link">{{__('lang.media-link')}}</option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <br>
                            <button type="button" class="mt-2 btn-theme add_resource_link" > {{__('lang.add')}} </button>
                        </div>
                    </div>
                    <div class="error" id="resourcesError"></div>
                </div>
                <br>
                <div id="getinputdata" class="getinputdata col-sm-12" ></div>
            </div>
        </div>
    </div>
    </form>
<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
    <script>


        var chipText = "";
        $(document).on('change','#file' , function(){
            $(this).next('div .image_preview').html($(this)[0].files[0].name)
            readURL(this);
        })


        $('#name').on('change', function(){
            if($(this).val().length < 4){
                $('#nameError').html("{{ __('lang.learning-path-title-error') }}");
                $('.finalSubmit').prop('disabled', true);
            }else{
                $('#nameError').html(" ");
                $('.finalSubmit').prop('disabled', false);
            }
        })

        $(document).ready(function()
        {
            $('.chips').css('margin-bottom','0px');
            CKEDITOR.replace( 'description', {

                removeButtons: 'PasteFromWord',
                removePlugins: 'image, sourcearea, specialchar, horizontalrule, pastetext, pastefromword, blockquote, link'
            });

            CKEDITOR.instances.description.on('change',function(event){

                    var str = convertToPlain(CKEDITOR.instances.description.getData());
                    if(str.length <= 10){
                    $('#descriptionError').html("{{ __('lang.learning-path-description-error') }}");
                    $('.finalSubmit').prop('disabled', true);
                    } else {
                        $('#descriptionError').html(" ");
                        $('.finalSubmit').prop('disabled', false);
                    }
            });

            $('#file').bind('change', function() {
                var fileLenght = (this.files[0].size);

                // 5mb
                if(fileLenght > 5000000) {
                    $('#fileError').html("{{ __('lang.learning-path-image-error')}}")
                    $('.finalSubmit').prop('disabled', true);

                } else {
                    $('#fileError').html(" ");
                    $('.finalSubmit').prop('disabled', false);
                }
            });

            // chip maker
            var input = document.querySelector(".chip-input");
            var chips = document.querySelector(".chips");

            document.querySelector(".form-field")
            .addEventListener('click',() => {
                input.style.display = 'block';
            });

            input.addEventListener('blur',()=>{
            input.style.display = 'block';
            });

            input.addEventListener('keypress', function(event){
            if(event.which === 13)
            {
                $('.chips').css('margin-bottom','50px');
                chips.appendChild(function ()
                {
                    var _chip = document.createElement('div');
                    _chip.classList.add('chip');
                    _chip.addEventListener('click', chipClickHandler);

                    _chip.append(
                        (function ()
                        {
                        var _chip_text = document.createElement('span');
                        _chip_text.classList.add('chip--text');
                        _chip_text.innerHTML = input.value;
                        chipText = chipText + ',' + input.value;
                        return _chip_text;
                        })(),
                        (function ()
                        {
                        var _chip_button = document.createElement('span');
                        _chip_button.classList.add('chip--button');
                        _chip_button.innerHTML = 'x';

                        return _chip_button;
                        })()
                    );

                    return _chip;
                }());
                input.value = '';
            }
            });

            function chipClickHandler(event){
                var text = $(this).find('.chip--text').text();
                chipText = chipText.replace(text,"");
                chips.removeChild(event.currentTarget);
            }

            // call ajax for chatbot
            var ajaxurl = "https://v2.reallybot.com/api/chatbots/clientchatbots/" + `{{ env('CHATBOT_ID')}}`;
            $.ajax({
                    url: ajaxurl,
                    type: 'GET',
                    contentType: "application/json",
                    success: function (response) {
                        if (response.success == true) {
                            if(response.chatbots.length > 0){
                                $.each(response.chatbots,function(key, value) {
                                $('#chatbot').append(`<option value="${value._id}" >${value.name}</option>`)
                            });
                            } else {
                                $('#chatbot').append(`<option value="" selected>No Record Found</option>`)
                            }
                        } else {
                            $('#chatbot').append(`<option value="" selected>No Record Found</option>`)
                        }
                    },
                    error: function (reject) {

                    }
            });
        });

        function submitForm(event)
        {
            $('#fileError').html('');
            $('#descriptionError').html('');
            $('#tagsError').html('');
            $('#resourcesError').html('');
            $('#iframe_linkError').html('');
            $('#categoryError').html('');
            $('#subCategoryError').html('');

            var botOnly = 0;
            if($("#is_bot_only").prop('checked') == true){
                botOnly = 1;
            } else {
                botOnly = 0;
            }
            event.preventDefault()
            $('.loader').show();
            var resources = [];
            let token =  $('#token').val();
            $('.mediadata').each(function(index) {
              // var order
                resources.push({
                    orderID: index+1,
                    title: $(this).find('.title').val(),
                    link: $(this).find('.link').val(),
                    resource_type : $(this).find('.resource_type').val(),
                    package_id: $(this).find('.packageId').val(),
                })
            });

            if(botOnly == 1)
            {
                var newdata = {
                    name: $('#name').val(),
                    description: CKEDITOR.instances['description'].getData(),
                    _token : $('#token').val(),
                    category:$('#category').val(),
                    sub_category:$('#sub_category').val(),
                    suitable_for:$('#suitable_for').val(),
                    language:$('#language').val(),
                    instructor:$('#instructor').val(),
                    level:$('#level').val(),
                    price:$('#price').val(),
                    type:$('#type').val(),
                    tags_Keywords:chipText,
                    duration:$('#duration').val(),
                    requirements:$('#requirements').val(),
                    uploaded_by:$('#uploaded_by').val(),
                    botOnly: 1,
                    chatbot:$('#chatbot').val(),
                    iframe_link:$('#iframe_link').val()

                }

            } else {
                var newdata = {
                    name: $('#name').val(),
                    description: CKEDITOR.instances['description'].getData(),
                    resources: resources,
                    _token : $('#token').val(),
                    category:$('#category').val(),
                    sub_category:$('#sub_category').val(),
                    suitable_for:$('#suitable_for').val(),
                    language:$('#language').val(),
                    instructor:$('#instructor').val(),
                    level:$('#level').val(),
                    price:$('#price').val(),
                    type:$('#type').val(),
                    requirements:$('#requirements').val(),
                    uploaded_by:$('#uploaded_by').val(),
                    tags_Keywords:chipText,
                    duration:$('#duration').val()
                }
            }

            var form_data = new FormData();
            for ( var key in newdata ) {
              if(key == 'resources') {
                  let resourcesData = newdata[key];
                  for (var i = 0; i < resourcesData.length; i++) {
                    form_data.append(`resources[${i}][title]`, resourcesData[i].title);
                    form_data.append(`resources[${i}][link]`, resourcesData[i].link);
                    form_data.append(`resources[${i}][orderID]`, resourcesData[i].orderID);
                    form_data.append(`resources[${i}][resource_type]`, resourcesData[i].resource_type);
                    form_data.append(`resources[${i}][packageId]`, resourcesData[i].package_id);
                  }
              } else {
                  form_data.append(key, newdata[key]);
              }
            }
            form_data.append('image', $('input[name=image]')[0].files[0]);

            var ajaxurl = $('#add-learning-path').attr('action');
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                dataType: 'json',           // what to expect back from the PHP script, if anything
                data: form_data,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                contentType: false,
                processData: false,
                success: function(result) {
                    $('.loader').hide();
                    $('.mediadata').each(function(){
                    })
                    if(!result.success) {
                        if(result.messsage.description){
                            $('#descriptionError').html(result.messsage.description);
                        }
                        if(result.messsage.tags_Keywords){
                            $('#tagsError').html(result.messsage.tags_Keywords);
                        }

                        if(result.messsage.resources){
                            $('#resourcesError').html(result.messsage.resources);
                        }

                        if(result.messsage.iframe_link){
                            $('#iframe_linkError').html(result.messsage.iframe_link);
                        }

                        if(result.messsage.category)
                        {
                            $('#categoryError').html(result.messsage.category);
                        }

                        if(result.messsage.sub_category)
                        {
                            $('#subCategoryError').html(result.messsage.sub_category);
                        }
                    } else {
                        $('#pathAlert').append(`<div  class="alert alert-success">` + "{{ __('lang.learningpath-created') }}" + `</div> `);
                        $('body').find('.piaggio-alert .alert').css('animation' , 'alertIN ease-in-out .35s forwards , alertOut ease-in-out .35s 4s forwards')
                        setTimeout(() => {
                            window.location.href ="{{url ($routeSlug .'/learning-paths')}}"
                        }, 3000);

                    }
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    $('.loader').hide()
                    $('#nameError').html(err.messsage)
                    $('#descriptionError').html(err.messsage)
                    $('#fileError').html(err.messsage)
                }
          })

        }

        $( ".bot-checkbox" ).click(function() {
            if(this.checked){
                $('#botDiv').show();
                $('#resourceDiv').hide(); // hide resource
                $("#type").val('RB');
            }
            if(!this.checked){
                $('#botDiv').hide();
                $('#resourceDiv').show(); // show resource
                $("#type").val('LP');
            }
        });

        $(document).on('change','#chatbot_file' , function(){
            $(this).next('.chatbot_preview').html($(this)[0].files[0].name)
        })


      /*  $('#category').change(function(){
            var Id = $('#category').val();
            var ajaxurl = app_url + "/superadmin/category/" + Id + "/sub-category" ;

            $.ajax({
            type: 'get',
            url: ajaxurl,
                success: function (data) {
                    $("#sub_category").empty();
                    $("#sub_category").prop("disabled", false);
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
*/
        $(".amount").keyup(function() {
            var $this = $(this);
            $this.val($this.val().replace(/[^\d.]/g, ''));
        });
    </script>

@endsection
