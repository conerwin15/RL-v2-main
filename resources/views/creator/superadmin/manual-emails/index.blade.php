@extends('layouts.app')

@section('content')

<div class="piaggio-alert">
        <div id="userAlert"></div>
</div>

    <div class="dash-title container-fluid">
        <div>     
        <a href="{{ url('/superadmin/scheduled/mails') }}"><b>{{__('lang.manual-email')}}  &gt;</b></a>
            <span class="bradcrumb">{{__('lang.campaigns')}}</span>
        </div>

    </div>

    <form class="container-fluid" method="GET">
        <div class="white-wrapper pb-4">
            <div class="row align-items-end">
                <div class="col-sm-12">
                    <h6><b>{{__('lang.filters')}}</b></h6>
                </div>
                <div class="col-sm-2">
                    <label>{{__('lang.select-country')}}: </label>
                    <select  name="filter_country" id="country" onchange="getRegion(this.value, true)" class="form-control select" >
                        <option value="0"> {{__('lang.all-countries')}}</option>
                        @foreach ($countries as $country)
                            <option value="{{$country->id}}"  {{ @$_GET['filter_country'] == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-sm-2">
                    <label>{{ __('lang.job-role') }}:</label>
                    <select name="filter_jobrole" class="form-control select"  id="jobRole">
                        <option value="0"> {{__('lang.all')}} </option>
                        @foreach($jobRoles as $jobRole)
                            <option value="{{ $jobRole->id }}" 
                                {{ @$_GET['filter_jobrole'] == $jobRole->id ? 'selected' : '' }}>
                                {{ ucfirst($jobRole->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-sm-2">
                    <label>{{__('lang.region')}}: </label>
                    <select  name="filter_region" id="region" onchange="getDealer()" class="form-control select" disabled="disabled" >
                        <option value="0">{{__('lang.all-regions')}}</option>
                    </select>
                </div>
                
                <div class="col-sm-2">
                    <label>{{ __('lang.role') }}:</label>
                    <select name="filter_role" class="form-control select" id="role">
                        <option value="0"> {{__('lang.all')}} </option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" id="Id"
                                {{ @$_GET['filter_role'] == $role->id ? 'selected' : '' }}>
                                {{ ucfirst(toRoleLabel($role->name)) }}
                            </option>
                        @endforeach
                    </select>
                </div> 

                <div class="col-sm-2">
                    <label>{{ __('lang.group') }}:</label>
                    <select name="filter_group" class="form-control select" id="group">
                        <option value="0"> {{__('lang.all')}} </option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" 
                                {{ @$_GET['filter_group'] == $group->id ? 'selected' : '' }}>
                                {{ ucfirst($group->name) }}
                            </option>
                        @endforeach
                    </select>
                </div> 

                <div class="col">
                    <label for=""><br></label><br>
                    <button type="submit" class="btn-theme">{{ __('lang.apply-filter') }}</button>
                </div>
            </div>
        </div>
    </form>

    <form action="{{url('superadmin/mail/send')}}" method="POST" class="container-fluid" id="frm-manual">
        @csrf
            <div class="container-fluid">
                <div class="white-wrapper">
                
                    <table class="data-table display" id="manual_mail">
                        <thead>
                            <tr>
                                <th width="12%" >
                                    <input name="select_all" value="1" type="checkbox">
                                    <input type="hidden" id="filter_country" name="filter_country" value="{{@$_GET['filter_country']}}" >
                                    <input type="hidden" id="filter_region" name="filter_region" value="{{@$_GET['filter_region']}}" >
                                    <input type="hidden" id="filter_role" name="filter_role" value="{{@$_GET['filter_role']}}" >
                                    <input type="hidden" id="filter_jobrole" name="filter_jobrole" value="{{@$_GET['filter_jobrole']}}" >
                                    <input type="hidden" id="filter_group" name="filter_group" value="{{@$_GET['filter_group']}}" >
                                    <input type="hidden" id="form_type" name="form_type" value="0">
                                
                                </th>
                                <th>{{__('lang.s-no')}}</th>
                                <th>{{__('lang.learner-name')}}</th>
                                <th>{{__('lang.email')}}</th>
                                <th>{{__('lang.country')}}</th>
                                <th>{{__('lang.dealer')}}</th>
                                <th>{{__('lang.job-role')}}</th>
                                <th>{{__('lang.region')}}</th>
                                <th>{{__('lang.role')}}</th>
                                <th>{{__('lang.group')}}</th>
                
                            </tr>
                        </thead>   
                        <tbody>
                        
                        </tbody> 
                        
                    </table>
                    @if($errors->has('learners_mail'))
                       <div class="errorMsg" id="learnersMailError">{{ __('lang.manual-leaner-error') }}</div>
                    @endif
                    <div class="errorMsg" id="learnersError"> </div>
                </div>
            </div>

            <div class="col-12 col-md-7">
                <div class="form-group">
                    <label>{{ __('lang.campaign-name') }}:</label>
                    <input class="form-control" name="campaign" placeholder="{{ __('lang.campaign') }}">
                    @if($errors->has('campaign'))
                        <div class="errorMsg" id="quizTextError">{{ $errors->first('campaign') }}</div>
                    @endif
                    <div class="errorMsg" id="campaignError"> </div>
                </div>
            </div>

            <div class="col-sm-4 mb-3">
                <div class="form-group">
                <label>{{ __('lang.recurrence') }}:</label>
                    <select name="recurrence" class="select form-control recurrence"  required>
                        <option value="once"> {{ __('lang.once') }} </option>
                        <option  value="every"> {{ __('lang.repeat') }}</option>
                    </select>
                </div>
            </div>

            <div class="col-sm-8">
                <div class="row">
                    <div class="col-sm-6 mb-3 frequency_unit" style="display:none;">
                        <div class="form-group">
                            <label>{{ __('lang.every') }} X:</label>
                            <select name="unit" class="select form-control"  required>
                                <option value="day"> {{ __('lang.day') }}(s)</option>
                                <option value="week"> {{ __('lang.week') }}(s) </option>
                                <option value="month"> {{ __('lang.month') }}(s) </option>
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-6 mb-3 frequency_amount" style="display:none;">
                    <div class="form-group">
                    <label>X:</label>
                    <input type="number" class="form-control frequencyAmt" name="frequency_amt" placeholder="{{ __('lang.for-x-times') }}">
                    @if($errors->has('frequency_amt'))
                        <div class="errorMsg">{{ $errors->first('frequency_amt') }}</div>
                    @endif
                    <div class="errorMsg" id="amountError"> </div>
                </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-8">
                <div class="row">
                    <div class="col-6 col-md-4">
                        <div class="form-group">
                            <label>{{ __('lang.start-date') }}:</label>
                            <input type="datetime-local" class="form-control startDate" name="start_date" placeholder="{{ __('lang.start date') }}" min="{{$currentDate}}">
                            @if($errors->has('start_date'))
                                <div class="errorMsg" id="quizTextError">{{ $errors->first('start_date') }}</div>
                            @endif
                            <div class="errorMsg" id="startDateError"> </div>
                        </div>
                    </div>

                    <div class="col-6 col-md-4 end_date" style="display:none;">
                        <div class="form-group">
                            <label>{{ __('lang.end-date') }}:</label>
                            <input type="date" class="form-control endDate" name="end_date" min="{{$endDate}}">
                            @if($errors->has('end_date'))
                                <div class="errorMsg" id="quizTextError">{{ $errors->first('end_date') }}</div>
                            @endif
                            <div class="errorMsg" id="endDateError"> </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-7">

                <div class="form-group">
                    <label>{{ __('lang.mail-subject') }}:</label>
                    <input class="form-control" name="subject" placeholder="{{ __('lang.subject') }}" required>
                    @if($errors->has('subject'))
                        <div class="errorMsg" id="quizTextError">{{ $errors->first('subject') }}</div>
                    @endif
                    <div class="errorMsg" id="subjectError"> </div>
                </div>
            </div>

            <div class="col-12 col-md-7">
                <div class="form-group">
                    <label>{{ __('lang.mail-content') }}:</label>
                    <textarea class="form-control" rows="4" name="description" placeholder="{{ __('lang.description') }}" maxlength = "1200" required> {{old('description')}}</textarea>
                    @if($errors->has('description'))
                        <div class="errorMsg" id="quizTextError">{{ $errors->first('description') }}</div>
                    @endif
                    <div class="errorMsg" id="contentError"> </div>
                </div>
            </div>
            <div class="text-center">
                <button type="submit"  id="assignAll" class="btn-theme" onclick="return confirm('{{ __('lang.are-you-sure') }}')" >{{__('lang.send-now')}}</button>
                <button type="button"  id="scheduleEmail" class="btn-theme">{{__('lang.schedule')}}</button>
                <button type="reset" class="btn-theme"> {{__('lang.cancel')}} </button>
            </div>
        
    </form>        
@section('scripts')  
<srcipt src="http://www.gyrocode.com/articles/jquery-datatables-checkboxes/"></script>
<script>

function updateDataTableSelectAllCtrl(table){
   var $table             = table.table().node();
   var $chkbox_all        = $('tbody input[type="checkbox"]', $table);
   var $chkbox_checked    = $('tbody input[type="checkbox"]:checked', $table);
   var chkbox_select_all  = $('thead input[name="select_all"]', $table).get(0);

   // If none of the checkboxes are checked
   if($chkbox_checked.length === 0){
      chkbox_select_all.checked = false;
      if('indeterminate' in chkbox_select_all){
         chkbox_select_all.indeterminate = false;
      }

   // If all of the checkboxes are checked
   } else if ($chkbox_checked.length === $chkbox_all.length){
      chkbox_select_all.checked = true;
      if('indeterminate' in chkbox_select_all){
         chkbox_select_all.indeterminate = false;
      }

   // If some of the checkboxes are checked
   } else {
      chkbox_select_all.checked = true;
      if('indeterminate' in chkbox_select_all){
         chkbox_select_all.indeterminate = true;
      }
   }
}

/*********** datatable ***********/
$(document).ready(function (){
        var rows_selected = [];
        var email_selected = [];
        var Id_selected = [];
        var table = $('.data-table').DataTable({
          processing: true,
          serverSide: true,
            ajax: {
                url: "{{url('superadmin/mail/list')}}",
                data: function (d) {
                    d.filter_country = $('#filter_country').val(),
                    d.filter_jobrole = $('#filter_jobrole').val(),
                    d.filter_region  = $('#filter_region').val(),
                    d.filter_role    = $('#filter_role').val(),
                    d.filter_group   = $('#filter_group').val()
                  
                }
             },    

                columns: [
                    
                    {data: 'checkbox', name: 'checkbox', orderable: false},
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false },
                    {data: 'name', name: 'name'},
                    {data: 'email', name: 'email'},
                    {data: 'country', name: 'country'},
                    {data: 'dealer', name: 'dealer'},
                    {data: 'jobRole', name: 'jobRole'},
                    {data: 'region', name: 'region'},
                    {data: 'role', name: 'role'},
                    {data: 'group', name: 'group'},
                  
                  
                ],
                'columnDefs':
                [{
                    'targets': 0,
                    'searchable':false,
                    'orderable':false,
                    'width':'1%',
                    'className': 'dt-body-center',
                }],

                'order': [2, 'asc'],
                'lengthChange': false,
                'rowCallback': function(row, data){
                    // Get row ID
                    var rowId = data.DT_RowIndex;
                    // If row ID is in the list of selected row IDs
                    if($.inArray(rowId, rows_selected) !== -1){
                        $(row).find('input[type="checkbox"]').prop('checked', true);
                        $(row).addClass('selected');
                    }
                }
        });

   

    $('#manual_mail tbody').on('click', 'input[type="checkbox"]', function(e){
    
        var $row = $(this).closest('tr');
        var data = table.row($row).data();
        var rowId = data.DT_RowIndex;
    
        // Determine whether row ID is in the list of selected row IDs 
        var index = $.inArray(rowId, rows_selected);

        // If checkbox is checked and row ID is not in list of selected row IDs
        if(this.checked && index === -1){
            rows_selected.push(rowId);
            email_selected.push(data.email);
            Id_selected.push(data.id);
        } else if (!this.checked && index !== -1){
            rows_selected.splice(index, 1);
            email_selected.splice(index, 1);
            Id_selected.splice(index, 1)
        }

        if(this.checked){
            $row.addClass('selected');
        } else {
            $row.removeClass('selected');
        }

        // Update state of "Select all" control
        updateDataTableSelectAllCtrl(table);

        // Prevent click event from propagating to parent
        e.stopPropagation();
   });

   // Handle click on table cells with checkboxes
   $('#manual_mail').on('click', 'tbody td, thead th:first-child', function(e){
      $(this).parent().find('input[type="checkbox"]').trigger('click');
   });

   // Handle click on "Select all" control
   $('thead input[name="select_all"]', table.table().container()).on('click', function(e){
        if(this.checked){
            $('#manual_mail tbody input[type="checkbox"]:not(:checked)').trigger('click');
        } else {
            $('#manual_mail tbody input[type="checkbox"]:checked').trigger('click');
        }

        // Prevent click event from propagating to parent
        e.stopPropagation();
   });

    // Handle table draw event
     table.on('draw', function(){
          updateDataTableSelectAllCtrl(table);
     });
    
   // Handle form submission event 

   $('#frm-manual').on('submit', function(e){
      var form = this;
         // Create a hidden element 
        $(form).append(
             $('<input>')
                .attr('type', 'hidden')
                .attr('name', 'learners_mail')
                .val(email_selected)
        );
    });

    // submit form using ajax
    $('.recurrence').on('change', function (e){
        if( this.value == 'every')
        {
            $('#assignAll').css('display', 'none');
            $('.frequency_unit').css('display', 'block');
            $('.end_date').css('display', 'block');
            $('.frequency_amount').css('display', 'block');
        } else {
            $('#assignAll').css('display', 'inline');
            $('.frequency_unit').css('display', 'none');
            $('.end_date').css('display', 'none');
            $('.frequency_amount').css('display', 'none');
        }
    });

    $('#scheduleEmail').on('click', function (e){
        $('#form_type').val("1");
        $("#learnersError").html(" ");
        $("#campaignError").html(" ");
        $("#startDateError").html(" ");
        $("#subjectError").html(" ");
        $("#contentError").html(" ");
        $('#amountError').html(" ");
        $('#endDateError').html(" ");

        if($('.recurrence').val() == 'every')
        {
            if($('.frequencyAmt').val() == '')
            {
                $('#amountError').html("{{ __('lang.frequency-amount-required') }}");
                return false;
            }

            var end_date = $('.endDate').val();
            var start_date = $('.startDate').val();
            if(end_date == '')
            {
                $('#endDateError').html("{{ __('lang.end-date-required') }}");
                return false;
            }

            if(start_date > end_date)
            {
                $('#endDateError').html("{{ __('lang.end-date-greater-than-start-date') }}");
                return false;
            }
        }

        var formData = $("#frm-manual").serializeArray();
        formData.push({ name: "learners_id", value: Id_selected });
        var ajaxurl = app_url + "/superadmin/schedule/mail" ;
        $.ajax({
            url: ajaxurl,
            type:'POST',
            data:formData,
            success:function(data) {

                if(data.success === true){
                    $('#userAlert').append(`<div  class="alert alert-success"><strong>` + data.message + `</strong></div> `); 
                    $('body').find('.piaggio-alert .alert').css('animation' , 'alertIN ease-in-out .35s forwards , alertOut ease-in-out .35s 4s forwards')
                        setTimeout(() => {
                        window.location.href = app_url + "/superadmin/scheduled/mails";
                        }, 2000);

                } else if(data.success === false && typeof data.nextRunDateError !== 'undefined') {
                    $('#endDateError').html(data.nextRunDateError);
                } else {

                    if(typeof data.message['learners'] !== 'undefined')
                    {
                        $("#learnersError").html(data.message.learners[0]);
                    }

                    if (typeof data.message['campaign'] !== 'undefined') {
                        $('#campaignError').html(data.message.campaign[0]);
                    }

                    if (typeof data.message['start_date'] !== 'undefined') {
                        $('#startDateError').html(data.message.start_date[0]);
                    }

                    if (typeof data.message['subject'] !== 'undefined') {
                        $('#subjectError').html(data.message.subject[0]);
                    }

                    if (typeof data.message['description'] !== 'undefined') {
                        $('#contentError').html(data.message.description[0]);
                    }
                }
            },
            error: function (reject) {
            }
        });
    });
});

</script>  
@endsection
@endsection
