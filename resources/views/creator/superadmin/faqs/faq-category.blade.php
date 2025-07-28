@extends('layouts.app')

@section('content')
<div class="piaggio-alert"> 
	<div id="faqCategoryAlert"></div>
</div>

<div class="dash-title container-fluid">
	<b>{{__('lang.FAQ-category')}}</b>
	<button type="submit"  data-toggle="modal"  data-backdrop="static" data-keyboard="false" class="btn-theme" onclick="showAddFaqCategory();">
	+ {{__('lang.add-FAQ-category')}}
	</button>
</div>

	<div class="container-fluid">
		<div class="white-wrapper">
                    
            <form class="d-lg-flex justify-content-end align-items-center"  method="GET" style="margin-left: 70%">
				@if(isset($_GET['name']))
				<input type="text"  placeholder="{{__('lang.search-by-name-placeholder')}}" class="form-control" id="search" name="name" value="{{$_GET['name']}}">
				@else
                <input type="text"  placeholder="{{__('lang.search-by-name-placeholder')}}" class="form-control" id="search" name="name">
        		@endif
				<button type="submit" class="btn-theme ml-2">{{__('lang.search')}}</button> 
		    </form>
			<div class="table">
				<table class="data-table">
					<thead>
							<tr>
								<th>{{__('lang.no')}}</th>
								<th>{{__('lang.category-name')}}</th>
								<th>{{__('lang.created-on')}}</th>
								<th width="280px" class="th-action">{{__('lang.action')}}</th>
							</tr>
					</thead>

					<tbody>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<!-- Modal -->
  	<div class="modal fade custom-model" id="addFAQCategory" role="dialog">
      	<div class="modal-dialog">
          	<div class="modal-content">
              	<div class="modal-body">
					<h5 class="modal-title mb-2">{{ __('lang.add-FAQ-category') }}</h5>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
                  	<!-- form -->
                  	<form action="" method="POST"
                      id="addCategoryForm">
                      @csrf
                      	<div class="form-group">
							<label>{{ __('lang.category-name') }}:</label>
							<input type="text" name="name" class="form-control"
                                      placeholder="{{ __('lang.category-name') }}"
                                      value="{{ old('name') }}" required>
							<div class="errorMsg" id="addCategoryError"></div>
						</div>
						<div class="text-center">
							<button type="button" class="btn-theme addFAQCategory">{{ __('lang.submit') }}</button>
							<button type="button" class="btn-theme-border" data-dismiss="modal">{{ __('lang.cancel') }}</button>
						</div>
					</form>
          		</div>

      		</div>
  		</div>
	</div>


  <!------ Edit FAQCategory-----> 

    <!-- Modal -->
<div class="modal fade custom-model" id="editFAQCategory" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-body">
                <!-- form -->
                <h5 class="modal-title mb-2">{{ __('lang.edit-FAQ-Category') }}</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <form action="" method="POST" id="editFAQCategoryForm">
                    @csrf
                    <input type="hidden" name="id" id="categoryId" value="">
                    <div class="form-group">
                        <label>{{ __('lang.category-name') }}:</label>
                        <input type="text" name="name" id="categoryName" class="form-control"
                            placeholder="{{ __('lang.name') }}"
                            value="{{ old('name') }}" required>
                        <div class="errorMsg" id="editCategoryError"></div>
                    </div>

                    <div class="text-center">
                        <button type="button" id="editFAQCategoryBtn"
                            class="btn-theme editFAQCategoryBtn">{{ __('lang.submit') }}</button>
                        <button type="button" class="btn-theme-border" data-dismiss="modal">{{ __('lang.cancel') }}</button>
                    </div>
                </form>

            </div>

        </div>
  	</div>
</div>

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.js"></script>
<script type="text/javascript">

    /********** add country *********/ 
        $('.addFAQCategory').on('click', function() {
           
        var formData = $("#addCategoryForm").serialize();
        $( '#addCategoryError').html( "" );
       
        var ajaxurl = app_url + "/superadmin/faq/categories/create" ;
        var type = 'add';
        sendPostRequest(ajaxurl, formData, type);

    });

    //************* edit country modal ************//

    function showAddFaqCategory() {
        $('.errorMsg').html("");
        $('#editFAQCategory').attr("style", "display: none !important");
        $('#addFAQCategory').modal('show');
    }

    /********** edit country *********/
    $(".data-table").on("click", ".editFAQCategory", function() {
            var categoryId = $(this).data('id');
            var categoryName = $(this).data('name');
            $('#categoryId').val(categoryId);
            $('#categoryName').val(categoryName);
            $('#editFAQCategory').modal('show');
    });

    $('.editFAQCategoryBtn').on('click', function() {
        event.preventDefault();
        $('#editCategoryError').html(" ");
        let ajaxurl = $('.editFAQCategory').attr('data-href');
        let formData = $('#editFAQCategoryForm').serialize();
        let type = "edit";
        sendPostRequest(ajaxurl, formData, type);

    });   

    function sendPostRequest (ajaxurl, formData, type)
    {
        $.ajax({
            url: ajaxurl,
            type:'POST',
            data:formData,
            success:function(data) {
               if(data.success == true){
                    if(type == 'add'){
                        $('#addFAQCategory').modal('hide');
                        $('#faqCategoryAlert').append(`<div  class="alert alert-success"><strong>` + "{{ __('lang.FAQ-category-added') }}" + `</strong></div> `);
                    } else {
                        $('#editFAQCategory').modal('hide'); 
                        $('#faqCategoryAlert').append(`<div  class="alert alert-success"><strong>` + "{{ __('lang.FAQ-category-updated') }}" + `</strong></div> `);  
                    } 
               
                            $('body').find('.piaggio-alert .alert').css('animation' , 'alertIN  ease-in-out .35s forwards , alertOut  ease-in-out  .35s 4s forwards')
                            setTimeout(() => {
                                location.reload()
                            }, 2000);
                } else {
                   $('.errorMsg').html(data.messsage.name);
                }
            },
            error: function (reject) {
                if( reject.status === 422 ) {
                    var data = $.parseJSON(reject);
                    if(type == 'add'){
                        $("#addCountryError").html(data.errors.name[0]);
                    } else {
                        $("#editCountryError").html(data.errors.countryName[0]);
                    }
                    
                }
            }
            
        });
    } 


    $(document).ready(function (){
      var ajaxUrl = "{{url('superadmin/faq/categories')}}" + window.location.search;
      var table = $('.data-table').DataTable({
          processing: true,
          serverSide: true,
          cache : false,
          processData: false,
          ajax: ajaxUrl,

              columns: [
                  {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false },
                  {data: 'faq_category', name: 'faq_category'},
                  {data: 'created_at', name: 'created_at'},
                  {data: 'action', name: 'action', orderable: false},
              ],

              'searching': false,
              'lengthChange': false,
              'order': [1, 'asc'],
              "createdRow": function( row ) {
                  $(row).find('td:eq(3)').addClass('flex');
              }
      });
    });
</script>
@endsection
@endsection