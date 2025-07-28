


$(document).ready(function(){

  var selectedLink = '';
  /**** login *****/
  $("#login_form input[name=email]").keyup(function(){

      $('#emailError').html("");
  });

  $("#login_form input[name=password]").keyup(function(){

      $('#passwordError').html("");
  });

  /**** job role *****/
  $("#create-job input[name=name]").keyup(function() {
          $('#nameError').html("");
     });

  $("#create-job textarea[name=description]").keyup(function() {
          $('#descriptionError').html("");
     });

   $("#edit-job input[name=name]").keyup(function() {
          $('#nameError').html("");
     });

  $("#edit-job textarea[name=description]").keyup(function() {
          $('#descriptionError').html("");
     });

  /**** group *****/
   $("#group-add input[name=name]").keyup(function() {
          $('#nameError').html("");
     });

  $("#group-add textarea[name=description]").keyup(function() {
          $('#descriptionError').html("");
     });

  $("#group-edit input[name=name]").keyup(function() {
          $('#nameError').html("");
     });

  $("#group-edit textarea[name=description]").keyup(function() {
          $('#descriptionError').html("");
     });

   /**** add course *****/
   $("#add-course input[name=name]").keyup(function() {
          $('#nameError').html("");
     });

  $("#add-course textarea[name=description]").keyup(function() {
          $('#descriptionError').html("");
     });

  $('#add-course input[type="file"]').change(function(e){
          $('#contentError').html("");
      });

  $("#add-course input[name=pre_training_chatbot]").keyup(function() {
          $('#pre_training_chatbotError').html("");
     });
  $("#add-course input[name=post_training_chatbot]").keyup(function() {
          $('#post_training_chatbotError').html("");
     });

  /**** edit course****/
  $("#edit-course input[name=name]").keyup(function() {
          $('#nameError').html("");
     });

  $("#edit-course textarea[name=description]").keyup(function() {
          $('#descriptionError').html("");
     });

  $('#edit-course input[type="file"]').change(function(e){
          $('#contentError').html("");
      });

  $("#edit-course input[name=pre_training_chatbot]").keyup(function() {
          $('#pre_training_chatbotError').html("");
     });
   $("#edit-course input[name=post_training_chatbot]").keyup(function() {
          $('#post_training_chatbotError').html("");
     });

 /**** add learning path *****/

 $("#add-learning-path input[name=name]").keyup(function() {
          $('#nameError').html("");
          $('#slugError').html("");
     });

  $("#add-learning-path textarea[name=description]").keyup(function() {
          $('#descriptionError').html("");
     });


 /**** edit learning path *****/

 $("#edit-learning_path input[name=name]").keyup(function() {
          $('#nameError').html("");
          $('#slugError').html("");
     });

  $("#edit-learning-path textarea[name=description]").keyup(function() {
          $('#descriptionError').html("");
     });

  /********* edit profile **********/
  $("#edit-profile input[name=name]").keyup(function() {
        $('#nameError').html("");
     });

  /********* update profile **********/
  $("#change-password input[name=current_password]").keyup(function() {
        $('#oldPassError').html("");
     });
  $("#change-password input[name=new_password]").keyup(function() {
        $('#newPassError').html("");
     });
  $("#change-password input[name=confirm_password]").keyup(function() {
        $('#confirmPassError').html("");
     });

  /********* add user *********/
  $("#add_user input[name=name]").keyup(function() {
        $('#nameError').html("");
     });

  $("#add_user input[name=email]").keyup(function() {
        $('#emailError').html("");
     });

  $("#add_user input[name=password]").keyup(function() {
        $('#passwordError').html("");
     });

  $("#add_user input[name=confirm-password]").keyup(function() {
        $('#confirm-passwordError').html("");
     });

  /********* edit user ********/

  $("#edit_user input[name=name]").keyup(function() {
        $('#nameError').html("");
     });

  $("#edit_user input[name=email]").keyup(function() {
        $('#emailError').html("");
     });

  $("#edit_user input[name=password]").keyup(function() {
        $('#passwordError').html("");
     });

  $("#edit_user input[name=confirm-password]").keyup(function() {
        $('#confirm-passwordError').html("");
     });

  /********* create Certificate ********/

   $("#create_certificate input[name=name]").keyup(function() {
        $('#nameError').html("");
     });
   $("#create_certificate input[name=certificate_prescription]").keyup(function() {
        $('#prescriptionError').html("");
     });

  /********* add Sales Tips ********/
   $("#add-sales-tips input[name=title]").keyup(function() {
        $('#titleError').html("");
     });
   $("#add-sales-tips input[name=description]").keyup(function() {
        $('#descriptionError').html("");
     });

  /********* edit Sales Tips ********/
   $("#edit-sales-tips input[name=title]").keyup(function() {
        $('#titleError').html("");
     });
   $("#edit-sales-tips input[name=description]").keyup(function() {
        $('#descriptionError').html("");
     });

   /********* add news & promotion ********/
   $("#add-news-promotions input[name=title]").keyup(function() {
        $('#titleError').html("");
     });
   $("#add-news-promotions input[name=description]").keyup(function() {
        $('#descriptionError').html("");
     });

  /********* edit news & promotion ********/
   $("#edit-sales-tips input[name=title]").keyup(function() {
        $('#titleError').html("");
     });
   $("#edit-sales-tips input[name=description]").keyup(function() {
        $('#descriptionError').html("");
     });

   /********* add faq ********/
   $("#add-faq input[name=question]").keyup(function() {
        $('#questionError').html("");
     });
   $("#add-faq input[name=answer]").keyup(function() {
        $('#answerError').html("");
     });

  /********* edit faq ********/
   $("#edit-faq input[name=question]").keyup(function() {
        $('#questionError').html("");
     });
   $("#edit-faq input[name=answer]").keyup(function() {
        $('#answerError').html("");
     });


     $('body').click(function(e) {
        if ( e.target.id != "" && e.target.id !== "region-box" && e.target.id !== 'region' && !$(e.target).closest('#region-checkbox').length){
            $("#region-checkbox").hide();
        }

        if (e.target.id != "country-checkbox-select" && !$(e.target).closest('#country-checkbox').length){
          $("#country-checkbox").hide();
        }

        if ( e.target.id != "region-checkbox-select" && !$(e.target).closest('#regions-checkbox').length){
          $("#regions-checkbox").hide();
        }

        if ( e.target.id != "dealer-checkbox-select" && !$(e.target).closest('#dealer-checkbox').length){
          $("#dealer-checkbox").hide();
        }

        if ( e.target.id != "jobrole-checkbox-select" && !$(e.target).closest('#jobrole-checkbox').length){
          $("#jobrole-checkbox").hide();
        }

        if ( e.target.id != "group-checkbox-select" && !$(e.target).closest('#group-checkbox').length){
          $("#group-checkbox").hide();
        }


        if ( e.target.id != "learningPath-checkbox-select" && !$(e.target).closest('#learningPath-checkbox').length){
          $("#learningPath-checkbox").hide();
        }
    });
});


/****** User module ******/

$(function(){
 $("#user_role").on('change', function(){
    var role = $(this).val();
    if(role=='User')
    {
         $('.show_dealers').show();
         $(".show_dealers").on('change', function(){
             var dealerId = $('#show_dealer').val();
             $('#dealer').val(dealerId);
         });
    }else
    {
      $('#dealer').val(0);
    }
 })

});


/****************  add pre course link **********************/
$(document).ready(function() {
var max_fields           = 1000; //maximum input boxes allowed
var wrapper              = $(".input_pre_course_wrap");
var add_button           = $(".add_pre_course_link");
var pre_placeholder      = "Pre training course link";
var pre_placeholder_text = "Pre training text";
var x = 1;
$(add_button).click(function(e){
  e.preventDefault();
  if(x < max_fields) {
     x++;
     $(wrapper).append('<div class="col-xs-12 col-sm-12 col-md-12" style="margin-left: 2px;"><div class="form-group"><input type="hidden" name="content_location[]" value="pre_course"><input type="text" name="pre_training_course_link[]" class="form-control" placeholder="'+pre_placeholder+'" value="" required> <input type="text" name="pre_course_text[]" class="form-control" placeholder="'+pre_placeholder_text+'"></div> <a href="#" class="remove_field">Remove</a></div>'); //add input box
  }
});

$(wrapper).on("click",".remove_field", function(e){ //user click on remove text
  e.preventDefault(); $(this).parent('div').remove(); x--;
})
});


/****************  add post course link **********************/
$(document).ready(function() {
var max_fields            = 1000; //maximum input boxes allowed
var wrapper               = $(".input_post_course_wrap");
var add_button            = $(".add_post_course_link");
var post_placeholder      = "post training course link";
var post_placeholder_text = "Post training text";

var x = 1;
$(add_button).click(function(e){
  e.preventDefault();
  if(x < max_fields) {
     x++;
     $(wrapper).append('<div class="col-xs-12 col-sm-12 col-md-12" style="margin-left: 2px;"><div class="form-group"><input type="hidden" name="content_location[]" value="post_course"><input type="text" name="post_training_course_link[]" class="form-control" placeholder="'+post_placeholder+'" value="" required><input type="text" name="post_course_text[]" class="form-control" placeholder="'+post_placeholder_text+'"></div> <a href="#" class="remove_field">Remove</a></div>'); //add input box
  }
});

$(wrapper).on("click",".remove_field", function(e){
  e.preventDefault(); $(this).parent('div').remove(); x--;
})
});


/**************** show event varaibles *******************/

function createHtmlLiData(data){

  var lidata = ''
  data.forEach(newdata=>{
    lidata +=
    `<li class="text-gray"> ${newdata}</li>`
  })
  $('#variable').html(lidata)
}

$("#events").on('change', function(){
    var event = $(this).val();
    var url = $(this).attr('data-href');
    $('#variable').html();
    var variableAppend = '';
      $.ajax({
            type: 'GET',
            url :  url + '/' + logged_user +'/event/' + event + '/variables',
            cache: false,
            success: function (data) {
                     createHtmlLiData(data)
            },
            error: function (data) {

            }
      });
 });

/********** delete course links ***********/
$(document).on('click', '.link_delete', function(){

 if(confirm('Are You Sure?'))
          {
              var url = $(this).attr('data-href');
              $.ajax({
                      type: 'get',
                      url : url,
                      cache: false,
                      data :{"_token": "{{ csrf_token() }}" },
                      success: function (data) {
                          $('#alert').append(
                              `<div  class="alert alert-success">
                              <p>`+ "{{__('lang.link-delete')}}" +`</p>
                              </div>
                              `)
                          setTimeout(function(){ $('.alert').hide();location.reload() }, 5000);
                      },
                      error: function (data) {

                      }
              });
          }
});

// get country and region Validation

$('#country').on('change', function(){
  if($(this).val() > 0 ){
      $('#region').attr('disabled' , false)
      $('#region').css('height', 'auto')
  }else{
      $('#region').attr('disabled' , false)
  }
})
$('#region').on('change', function(){
  if($(this).val() != trans('lang.select-region')){
      $('#dealer').attr('disabled' , false)
  }else{
      $('#dealer').attr('disabled' , true)
  }
})


$('#region').on('change', function(){
if($(this).val() != trans('lang.select-admin')){
  $('#trainingadmin').attr('disabled' , false)
}else{
  $('#trainingadmin').attr('disabled' , true)
}
})
/************** get region ***********/
// Country value is not none
$(document).ready(function(){

 // upload picture
 $('#upload_picture_form').on('submit', function (e) {
    e.preventDefault();

    var profileData = new FormData(this);

    var ajaxurl = app_url + "/" + logged_user + "/upload-profile-picture";

    $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: profileData,
            contentType: false,
            processData: false,
            success: function (data) {
                if (data.success == true) {
                    location.reload();
                }
            },
            error: function (reject) {

            }
        });

});

if(logged_user == 'superadmin') {
  var countryId = $('#country').val();
  if(countryId) { // if not defined
    if(countryId == -1) {
      window.getRegion(countryId , true)
    } else {
      window.getRegion(countryId , false)
    }
  }
}
})

function getQueryValue(param) {
const urlParams = new URLSearchParams(window.location.search);
return urlParams.get(param);
}

function getRegion (countryId, allowAll) {
if($('#country').val() >= 0 ){
  $('#region').attr('disabled' , false)
}else{
  $('#region').attr('disabled' , true)
}

var ajaxurl = app_url + "/superadmin/country/" + countryId + "/region" ;
let selectedRegion = window.getQueryValue('filter_region');

var getregionID = $('#selectregion').val()

if(selectedRegion == null){
  selectedRegion = getregionID
}

  $.ajax({
    type: 'get',
    url: ajaxurl,
    success: function (data) {
      $("#region").empty();
      if(allowAll == true){
        $("#region").append('<option value="-1"> ' + trans('lang.select-region') + '</option>');
      } else {
        $("#region").append('<option value="0"  >' + trans('lang.select-region') + '</option>');
      }
      if(data){
        $.each(data,function(key, value) {
          $('#region').append(
            `<option value="${value.id}" ${value.id == selectedRegion ? 'selected' : ''}>${value.name}</option>`
          );
        });

      }
    },
    error: function (data) {

    }
  });

}

function getAdmin() {
var region = $('#region').val();
var ajaxurl = app_url + "/superadmin/region/" + region + "/admin" ;

$.ajax({
  type: 'get',
  url: ajaxurl,
  success: function (data) {

    $("#trainingadmin").empty();
    $("#trainingadmin").append('<option value="" disabled selected >' + trans('lang.select-admin') + '</option>');
    if(data)
    {
      $.each(data,function(key,value){
        $('#trainingadmin').append($("<option/>", {
          value: value.id,
          text: value.name
        }));
      });
    }
  },
  error: function (data) {

  }
});
}

function getDealer() {
var region = $('#region').val();

var ajaxurl = app_url + "/superadmin/region/" + region + "/dealer" ;

$.ajax({
          type: 'get',
          url: ajaxurl,
          success: function (data) {
             $("#dealer").empty();
              $("#dealer").append('<option value="" disabled selected>' + trans('lang.select-dealer') + '</option>');
              if(data)
              {
                  $.each(data,function(key,value){
                      $('#dealer').append($("<option/>", {
                         value: value.id,
                         text: value.name
                      }));
                  });
              }
          },
          error: function (data) {
                console.log(data);
          }
      });
}


//************* edit learning path modal ************//
$(function () {
      $(".editLearningPath").click(function () {
          var learningPathId = $(this).data('id');
          var learningPathName = $(this).data('path');
          $('#LearningPathId').val(learningPathId);
          $('#LearningPathName').val(learningPathName);

      });
});

$("#editLearningPathButton").click(function(){
  event.preventDefault();
  let href = $('.editLearningPath').attr('data-href');
  let data = $('#editLearningPath').serialize();

  $.ajax({
          url: href,
          type: 'POST',
          data: $('#editLearningPathForm').serialize(),
          success: function(result) {
            $('#editLearningPath').modal('hide');
            $('#pathAlert').append(
                            `<div  class="alert alert-success">
                            <p><strong>` + "{{__('lang.learningpath-updated')}}" + `</strong></p>
                            </div>
                            `)
            setTimeout(function(){ $('.alert').hide();location.reload() }, 2000);
          },
  })
});

$("#searchCourse").keyup(function () {
let searchData = $('#searchCourse').val();
let learningPathId = $('#learning_path_id').val();
if(searchData == "")
{
  searchData = -1;
} else {
  searchData = searchData;
}
var ajaxurl = app_url + "/superadmin/learningPath/"+learningPathId+"/course/" + searchData ;
$.ajax({
          url: ajaxurl,
          type: 'GET',
          success: function(data) {
            $('.searchRecord').empty();
            $.each(data,function(key,value){

                $('.searchRecord').append('<div class="row" style="margin-left: 5%;"><input type="checkbox" name="assign_course[]"" class="form-check-input" value="'+value.id+'">'+ value.name +  '</div>');
            });
          }
  })

});


/**************** Select All Learners *************/

$("#assignAll").on("click", function() {
$('#assign').val(-1);
});

/************ add resources link***************/

$('#select-link').on('change', function() {
  selectedLink = $(this).val();
});

$(document).ready(function() {

var max_fields           = 10; //maximum input boxes allowed
var add_button           = $(".add_resource_link");
var itemCount 	       = 0;
var wrapper_media        = $("#getinputdata");
  $(add_button).click(function(e){

  itemCount++
  e.preventDefault();
  // media link
      if (selectedLink == 'media_link') {
          // assign anchor
          var x = 1;
          if($('div.getinputdata').length < max_fields) {
              $(wrapper_media).append(`
                <div class="row mediadata">
                  <input type="hidden" class="packageId"  value="">
                  <input  type="hidden" name="resource_type" value="media_link" class="resource_type">
                  <div class="col-sm-3">
                    <label>`+ trans('lang.media-title') +`</label>
                    <input type="text" name="media_title[]" class="form-control title" placeholder="" value="" required/>
                  </div>
                  <div class="col-sm-6">
                    <label>` + trans('lang.upload-file') + `<small> (` + trans('lang.only-image-pdf-allowed')  +`)</small>:</label>
                    <div class="fileinpurt form-control">
                      <input type="file" id="media_${itemCount}" onchange="fileChanged('media', '${itemCount}')" name="media_file[]" class="form-control uploadScromfile" accept="image/jpeg,image/jpg,image/gif,image/png,application/pdf" required>
                      <div id="file_name_${itemCount}">`+ trans('lang.select-file') +`</div>
                    </div>
                    <input type="hidden" class="link media_url_${itemCount}"  name="media_link[]"  required>
                    <div class="error" id="media_file_error_${itemCount}"></div>
                  </div>
                  <div class="col-sm-3 pt-2">
                    <br>
                    <div class="fileprogress fileprogress-${itemCount}" style="display:none">
                      <div>` + trans('lang.file-uploading') + ` <span class="percentage">0%</span></div>
                      <div class="meter"><span style="width:0%"></span></div>
                    </div>
                    <button type="button" class="btn-theme uploadScrom media-${itemCount}"  onclick="uploadFile('${itemCount}', logged_user, 'media')">`+ trans('lang.upload') +`</button>
                    <a class="btn-theme media-preview-${itemCount}"  target="_blank" href="" style="display:none">`+trans('lang.preview')+`</a>&nbsp; &nbsp;
                    <a class="remove_field text-danger media-remove-${itemCount}" href="#"><i class="fa fa-times" aria-hidden="true"></i>`+ trans('lang.remove') +`</a>
                  </div>
                </div>
              </div>
            `);
          }

      }

      // resource link
      if (selectedLink == 'chatbot_link') {
          if($('div.getinputdata').length < max_fields) {
              $(wrapper_media).append(`
                <div class=" row resource  mediadata">
                <input type="hidden" class="packageId"  value="">
                  <input type="hidden" name="resource_type" value="chatbot_link" class="resource_type">
                  <div class="col-sm-3">
                    <label>`+trans('lang.chatbot-title')+`</label>
                    <input type="text" name="chatbot_title[]" class="form-control title" placeholder="" value="" required />
                  </div>

                  <div class=" col-sm-6">
                    <label>`+trans('lang.chatbot-link')+`</label>
                    <input type="url" name="chatbot_link[]" class="form-control link resource-link-input" placeholder="" required>
                  </div>
                  <div class="col-sm-3 pt-2">
                  <br>
                    <a class="btn-theme resource-preview" href="#"  target="_blank">`+trans('lang.preview')+`</a> &nbsp; &nbsp;
                    <a class="remove_field text-danger" href="#" ><i class="fa fa-times" aria-hidden="true"></i>`+trans('lang.remove')+`</a>
                  </div>
                </div>
              `);
          }
      }

      // course link
      if (selectedLink == 'course_link') {
          if($('div.getinputdata').length < max_fields) {
              $(wrapper_media).append(`
                <div class="row course mediadata" id="container_${itemCount}">
                  <input type="hidden" name="resource_type" value="course_link" class="resource_type">
                  <input type="hidden" class="packageId" name="packageId" value="" id="packageId_${itemCount}">
                  <div class="col-sm-3">
                    <label>`+ trans('lang.course-title') +`</label>
                    <input type="text" name="title" class="form-control title" placeholder="" value="" required>
                  </div>
                  <div class="col-sm-6">
                    <label>`+ trans('lang.upload-file') +` <small>(`+ trans('lang.only-zip-file-allowed') +`)</small>:</label>
                    <div class="fileinpurt form-control">
                      <input type="file" id="course_${itemCount}" onchange="fileChanged('course', '${itemCount}')" name="course_link[]" class="form-control uploadScromfile link" accept=".zip,.rar,.7zip" required>
                      <div id="file_name_${itemCount}">`+ trans('lang.select-file') +`</div>
                    </div>
                    <div class="error" id="course_file_error_${itemCount}"></div>
                  </div>
                  <div class="col-sm-3 pt-2">
                    <br>
                    <div class="fileprogress fileprogress-${itemCount}" style="display:none">
                      <div>`+ trans('lang.file-uploading')+`<span class="percentage">0%</span></div>
                      <div class="meter"><span style="width:0%"></span></div>
                    </div>
                    <button type="button" class="btn-theme uploadScrom course-${itemCount}"  onclick="uploadFile('${itemCount}', logged_user, 'course')">`+ trans('lang.upload') +`</button>
                    <a class="btn-theme course-preview-${itemCount}"  target="_blank" href="" style="display:none">`+trans('lang.preview')+`</a>&nbsp; &nbsp;
                    <a class="remove_field text-danger course-remove-${itemCount}" href="#"><i class="fa fa-times" aria-hidden="true"></i>`+trans('lang.remove')+`</a>
                  </div>
                </div>
            `);
          }
      }
  $(document).scrollTop($(document).height());
  });

$(wrapper_media).on("click",".remove_field", function(e){ //user click on remove text
  e.preventDefault(); $(this).closest('div.mediadata').remove();
})

$(wrapper_media).on("click",".remove_field", function(e){ //user click on remove text
  e.preventDefault(); $(this).closest('div.mediadata').remove();
})

$(wrapper_media).on("click",".remove_field", function(e){ //user click on remove text
  e.preventDefault(); $(this).closest('div.mediadata').remove();
})


$(wrapper_media).on("click",".resource-preview", function(e){ //user click on remove text
  e.preventDefault();
  let linkVal = $(this).closest('div.mediadata').find('.link').val();
  window.open(linkVal, '_blank'); // to open link in new tab
})


});


function fileChanged(type, id) {

 if($(`#${type}_${id}`)[0].files.length > 0) {
  var fileName = $(`#${type}_${id}`)[0].files[0].name;
  $(`#file_name_${id}`).text(fileName);
 } else {
  $(`#file_name_${id}`).text(`Select file`);
 }

}

function uploadFile(id, logged_user, type)
{
  $(`.${type}-remove-${id}`).hide()

  let token = $('#token').val();
  $(this).html('% Uploading')
  event.preventDefault();
  var form_data = new FormData();
  form_data.append('type', type);

  var ajaxurl = app_url + '/'+ logged_user + "/uploadFile";
  if(type == "media"){
      $(`.media_url_${id}`).val("");
      var file_data = $(`#media_${id}`).prop('files')[0];

      form_data.append('file', file_data);
      form_data.append('_token', token);
      $(`#media_file_error_${id}`).html('')

  } else {
      var file_data = $(`#course_${id}`).prop('files')[0];
      form_data.append('file', file_data);
      form_data.append('_token', token);
      if($(`#course_${id}`).prop('files').length == 0 ){
         $(`#${id}_file`).html(trans('lang.please-select-file'))
      } else {
        $(`#course_${id}`).attr('disabled', 'disabled');
        $(`#course_file_error_${id}`).html('')
      }
  }

  // ajax call
    $.ajax({
      //FILE UPLOAD PROGRESS
      xhr: function() {
        var xhr = new window.XMLHttpRequest();
        xhr.upload.addEventListener("progress", function(evt) {
        if (evt.lengthComputable) {
          var percentComplete = evt.loaded / evt.total;
          percentComplete = parseInt(percentComplete * 100);

            $(`.fileprogress-${id}`).show()
            $(`.fileprogress-${id} .percentage`).html(percentComplete +'%')
            $(`.fileprogress-${id} span`).css('width' , percentComplete+'%')
            $(`.course-${id}`).hide()
            $(`.course-remove-${id}`).hide()
                if (percentComplete === 100) {

                }
              }
              }, false);

              return xhr;
            },

        url: ajaxurl,
        type: 'POST',
        dataType    : 'text',           // what to expect back from the PHP script, if anything
        cache       : false,
        contentType : false,
        processData : false,
        data: form_data,
        success: function(data) {
          var result = JSON.parse(data);
          if(result.success) {
            if(result.type == 'media'){
              $(`.fileprogress-${id}`).hide();
              $(`.media-preview-${id}`).show();
              $(`.media-remove-${id}`).show();
              $(`.media-${id}`).hide();
              var href = app_url+'/storage'+result.media ;
              $(`.media_url_${id}`).val(href);
              $(`.changed-media-${id}`).val(href);
              $(`.media-preview-${id}`).attr("href", href);
            } else {
              var href =  app_url + '/'+ logged_user + "/learning-paths/preview/";
              $(`.course-remove-${id}`).show();
              $(`.fileprogress-${id}`).hide();
              $(`#packageId_${id}`).val(result.package_id);
              $(`.course-preview-${id}`).show();
              $(`.course-remove-${id}`).show();
              $(`.course-${id}`).hide();
              href = href + result.package_id;
              $(`.course-preview-${id}`).attr("href", href);
            }
          } else {
            if(form_data.get('type') == 'course') {
              $(`.course-remove-${id}`).show();
              $(`.fileprogress-${id}`).hide();
              $(`.course-${id}`).hide();
              $(`#course_file_error_${id}`).html('Invalid course file');
            }
          }
        },
        error: function(xhr, status, error) {
          $(`.course-remove-${id}`).show();
          $(`.fileprogress-${id}`).hide();
          $(`.course-remove-${id}`).show();
          $(`.course-${id}`).show();
          var err = eval("(" + xhr.responseText + ")");
          $(`#course_file_error_${id}`).html(err.messsage);
        }
    })
}

function showCourse()
{
  let packageId = $('#packageId').val();
  url = app_url + '/superadmin/getScromRef/' + packageId;
  $.ajax({

          type: 'GET',
          url : url,
          success: function (data) {

          }
  });
}


/************** get region for common ***********/


function showRegion () {
var country = $('.countries').val();

if(country && logged_user == 'superadmin')  {
  var ajaxurl = app_url + "/superadmin/country/" + country + "/region" ;
  $.ajax({
    type: 'get',
    url: ajaxurl,
    success: function (data) {
      $(".regions").empty();
      $('.regions').append(`<option value="-1">`+ "{{__('lang.all')}}" +`</option>`)

        if(data)
        {
            $.each(data,function(key,value){
                $('.regions').append($("<option/>", {
                   value: value.id,
                   text: value.name
                }));

                if(region == value.id)
                {
                    $('.regions').attr('selected',true)
                }
            });
        }
    },
    error: function (data) {
        console.log(data);
    }
  });
}

}

window.showRegion()

function getUrlVars()
{
  var vars = [], hash;
  var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
  for(var i = 0; i < hashes.length; i++)
  {
      hash = hashes[i].split('=');
      vars.push(hash[0]);
      vars[hash[0]] = hash[1];
  }
  return vars;
}


function readURL(input) {
if (input.files && input.files[0]) {
  var reader = new FileReader();

  reader.onload = function(e) {
    $('.preview').attr('src', e.target.result);
  }
    reader.readAsDataURL(input.files[0]); // convert to base64 string
}
}


// remove media on sales and tips
$('.remove-attachment').on('click', function () {

if(confirm(trans('lang.are-you-sure')))
{
    $('.div-remove-media').hide();
    $('#remove-media').val(1);
    $("#preview").html(" ");
}

});

/********** delete user ***********/
$(document).on('click', '.delete-user', function(){
var role = $(this).attr('data-role');
var confirmMessage = '';

if(role == 'dealers'){
  confirmMessage = trans('lang.delete-user-alert');
} else{
  confirmMessage = trans('lang.delete-proceed');
}

if(confirm(confirmMessage))
{
            $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
            });
             var url = $(this).attr('data-href');

             $.ajax({
                     type: 'delete',
                     url : url,
                     cache: false,

                     success: function (data) {
                                                  if(data['success'] == false)
                            {
                               $('#userAlert').append(`<div  class="alert alert-danger"><strong>` + data["messsage"] + `</strong></div> `);
                               $('body').find('.piaggio-alert .alert').css('animation' , 'alertIN ease-in-out .35s forwards , alertOut ease-in-out .35s 4s forwards')

                            } else {

                                if(role == "dealers"){
                                  $('#userAlert').append(`<div  class="alert alert-success"><strong>` + data["messsage"] + `</strong></div> `);
                                } else if(role == 'customers') {
                                  $('#userAlert').append(`<div  class="alert alert-success"><strong>` + data["messsage"] + `</strong></div> `);

                                } else {
                                  $('#userAlert').append(`<div  class="alert alert-success"><strong>` + data["messsage"] + `<strong></div> `);

                                }
                                $('body').find('.piaggio-alert .alert').css('animation' , 'alertIN ease-in-out .35s forwards , alertOut ease-in-out .35s 4s forwards')
                                setTimeout(() => {
                                location.reload()
                                }, 1000);
                            }

                  },

                     error: function (data) {
                        $('#userAlert').append(`<div  class="alert alert-error">` + data["messsage"] + `</div> `);
                        $('body').find('.piaggio-alert .alert').css('animation' , 'alertIN ease-in-out .35s forwards , alertOut ease-in-out .35s 4s forwards')
                        setTimeout(() => {
                        location.reload()
                      }, 1000);
                     }
             });
}
});

/********** delete learning path **********/

$(document).on('click', '.delete-learning-path', function() {
    if (confirm(trans('lang.are-you-sure'))) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var url = $(this).attr('data-href');

        $.ajax({
            type: 'DELETE',
            url: url,
            cache: false,
            success: function (data) {
                if (data.success === false) {
                    $('#pathAlert').append(`
                        <div class="alert alert-danger"><strong>${data.messsage}</strong></div>
                    `);
                } else {
                    $('#pathAlert').append(`
                        <div class="alert alert-success"><strong>${data.messsage}</strong></div>
                    `);
                }

                $('.piaggio-alert .alert').css('animation', 'alertIN ease-in-out .35s forwards, alertOut ease-in-out .35s 4s forwards');

                setTimeout(() => {
                    location.reload();
                }, 1000);
            },
            error: function (xhr) {
                let message = "An error occurred.";
                if (xhr.responseJSON && xhr.responseJSON.messsage) {
                    messsage = xhr.responseJSON.messsage;
                }

                $('#pathAlert').append(`
                    <div class="alert alert-danger"><strong>${messsage}</strong></div>
                `);

                $('.piaggio-alert .alert').css('animation', 'alertIN ease-in-out .35s forwards, alertOut ease-in-out .35s 4s forwards');

                setTimeout(() => {
                    location.reload();
                }, 1000);
            }
        });
    }
});





function getRegionAdmin (countryId, allowAll) {
if($('#country').val() >= 0 ){
  $('#region').attr('disabled' , false)
}else{
  $('#region').attr('disabled' , true)
}

var ajaxurl = app_url + "/superadmin/country/" + countryId + "/region" ;
let selectedRegion = window.getQueryValue('filter_region');

var getregionID = $('#selectregion').val()

if(selectedRegion == null){
  selectedRegion = getregionID
}

  $.ajax({
    type: 'get',
    url: ajaxurl,
    success: function (data) {
      $("#region-checkbox").empty();
      if(data){
        $.each(data,function(key, value) {
          $('#region-checkbox').append(
            `<span class="d-block menu-option"><label><input type="checkbox" name="region_id[]" value="${value.id}" ${value.id == selectedRegion ? 'selected' : ''}>&nbsp;
            ${value.name}</label></span>`
          );
        });

      }
    },
    error: function (data) {

    }
  });

}

var expanded = false;
function dropDown(event) {
var checkboxes = document.getElementById("region-checkbox");
if (!expanded) {
  checkboxes.style.display = "block";
  expanded = true;
} else {
  checkboxes.style.display = "none";
  expanded = false;
}
}


/******** Dashbaord js ********/

// showHideDropdown(eevnt, 'region-checbox');

function showHideDropdown(event, id) {
let expanded =  $('#'+ id).css('display');
if (expanded == 'none') {
    $('#'+ id).show();
} else {
    $('#'+ id).hide();
}
}

function doThis(title, buttonId, value, checkboxId) {
let allSelected = $('.' + checkboxId + ':checked');
let selectedLength = allSelected.length;
if(selectedLength > 0) {
    if(selectedLength == 1) {
        $('#' + buttonId).html(allSelected[0].dataset.name + '<span class="custom-dropdown-symbol" style="pointer-events: none;"> &#9013; </span>');
    } else {
        $('#' + buttonId).html(selectedLength + ' Selected' + '<span class="custom-dropdown-symbol" style="pointer-events: none;"> &#9013; </span>');
    }
} else {
    $('#' + buttonId).html(title + '<span class="custom-dropdown-symbol" style="pointer-events: none;"> &#9013; </span>');
}
}

/******** script to add validation of 1200 char on ckeditor ********/
window.onload = function() {

if( typeof(CKEDITOR) !== "undefined" ) {
  CKEDITOR.instances.description.on('key',function(event){
    var deleteKey = 46;
    var backspaceKey = 8;
    var keyCode = event.data.keyCode;
    if (keyCode === deleteKey || keyCode === backspaceKey)
        return true;
    else
    {
        var textLimit = 1200;
        var str = CKEDITOR.instances.description.getData();
        if (str.length >= textLimit)
            return false;
    }
  });
}

};

/*** convert html text to plain text ***/
function convertToPlain(html){

var tempDivElement = document.createElement("div");
tempDivElement.innerHTML = html;
return tempDivElement.textContent || tempDivElement.innerText || "";
}

/********** for superadmin & admin only *************/
$(".data-table").on("click", ".changePasswordModal", function() {
$('#changePasswordForm').attr('action', $(this).data('href'));
    $('#newPassError').html(" ");
$('#confirmPassError').html(" ");
    $('#new_password').val(null);
$('#confirm_password').val(null);
    $('#changePasswordModal').modal('show');
});
/*********** change Password of users  *************/
$('.changePasswordUser').on('click', function () {
    $('#newPassError').html(" ");
$('#confirmPassError').html(" ");
// check if form is empty
    var newPass = $('#changePasswordForm').find('input[name="new_password"]').val();
var confirmPass = $('#changePasswordForm').find('input[name="confirm_password"]').val();
    if(newPass == '')
{
      $('#newPassError').html('new password field is required');
  return false;
}

if(confirmPass == '')
{
  $('#confirmPassError').html('confirm password field is required');
  return false;
}

var confirmResponse = confirm("Are you sure ?");
if (confirmResponse == true) {

    var formData = $("#changePasswordForm").serialize();
    var ajaxurl = $('#changePasswordForm').attr('action');
    $.ajax({
        url: ajaxurl,
        type: 'POST',
        headers: {
          'change-password': true,
        },
        data: formData,
        success: function (data) {
          if (data.success == true) {
                  $('#changePasswordModal').modal('hide');
              $('#userAlert').append(
                  `<div  class="alert alert-success">
                  <p><strong>` + data['messsage'] + `</strong></p>
                  </div>
                  `)
              $('body').find('.piaggio-alert .alert').css('animation',
                  'alertIN  ease-in-out .35s forwards , alertOut  ease-in-out  .35s 4s forwards'
              )
              setTimeout(() => {
                location.reload()
              }, 1000);
          } else {
                  if(typeof data.messsage.new_password !== 'undefined') {
                      $('#newPassError').html(data.messsage.new_password);
              }

              if (typeof data.messsage.confirm_password !== 'undefined') {
                  $('#confirmPassError').html(data.messsage.confirm_password[0]);
              }
          }
      },
      error: function (reject) {
          if (reject.status === 422) {
              var data = $.parseJSON(reject);
                  $("#newPassError").html(data.errors.name[0]);

          }
      }
    });
} else {
    $('#changePassword').modal('hide');
}
});
/********** for superadmin & admin only *************/

/********** publish/unpublish package ************/
$(document).on('click', '.update-package-status', function(){
  var role = $(this).attr('data-role');
  var confirmMessage = '';

  confirmMessage = trans('lang.are-you-sure');

  if(confirm(confirmMessage))
{
            $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
            });
             var url = $(this).attr('data-href');

             $.ajax({
                     type: 'PUT',
                     url : url,
                     cache: false,

                     success: function (data) {
                            if(data['success'] == false)
                            {
                               $('#publishAlert').append(`<div  class="alert alert-danger"><strong>` + data["messsage"] + `</strong></div> `);
                               $('body').find('.piaggio-alert .alert').css('animation' , 'alertIN ease-in-out .35s forwards , alertOut ease-in-out .35s 4s forwards')

                            } else {

                                $('#publishAlert').append(`<div  class="alert alert-success"><strong>` + data['messsage'] + `<strong></div> `);

                                $('body').find('.piaggio-alert .alert').css('animation' , 'alertIN ease-in-out .35s forwards , alertOut ease-in-out .35s 4s forwards')
                                setTimeout(() => {
                                location.reload()
                                }, 1000);
                            }

                  },

                     error: function (data) {
                        $('#publishAlert').append(`<div  class="alert alert-error">` + data["messsage"] + `</div> `);
                        $('body').find('.piaggio-alert .alert').css('animation' , 'alertIN ease-in-out .35s forwards , alertOut ease-in-out .35s 4s forwards')
                        setTimeout(() => {
                        location.reload()
                      }, 1000);
                     }
             });
}

});
