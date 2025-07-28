@extends('layouts.app')

@section('content')

    <div class="piaggio-alert">
        <div id="bonusAlert"></div>
    </div>
    <div class="container-fluid max-width">

    <div class="dash-title container-fluid">
            <div>
                <a href="{{ url( 'superadmin/leaderboard') }}"><b>{{ __('lang.leaderboard') }} </b></a> <b> &gt; </b> 
                <span class="bradcrumb">{{ __('lang.view-point-history') }} </span>
            </div>
    </div>
    <div class="container-fluid max-width mt-3">
        <div class="f-emp">
            <h5 class="bg-blue" style="margin: -15px -15px  16px -15px">{{ __('lang.featured-employee') }}</h5>
        
            <div class="flex justify-content-start">
                <div class="mr-2">
                    @php $image = $pointHistories[0]->user->image == '' ? asset('assets/images/avatar_default.png') :  asset('storage' . Config::get('constant.PROFILE_PICTURES') . $pointHistories[0]->user->image);  @endphp
                    <img  src="{{$image}}"  id="output" class="f-employee">
                </div>
                <div class="emp-details">
                    <div class="basic-details">
                        <div class="flex justify-content-between">
                            <p class="mb-0"><b>{{ $pointHistories[0]->user->name}}</b>
                                <br> <small class="text-gray"> {{ $pointHistories[0]->user->country->name}}</small>
                            </p>
                        </div>
                    </div>

                    <form class="mb-0" action="{{ url('/superadmin/leaderboard/user/'.$pointStats[0]->user_id.'/points') }}" method="POST">
                        @csrf
                        <div style="margin-left: 44%;">
                            <button type="submit" class="btn-theme ml-2 mr-2" style="padding: 4px 14px;" id="submit">{{ __('lang.submit') }}</button>
                        </div><br>
                        <div class="flex align-items-center justify-content-start mb-2">
                            <span style="width:140px">{{ __('lang.accumulated-points') }}: </span> <span class="form-control w-100 ml-2 input-small">{{ $pointStats[0]->accumulated}}</span>
                            &nbsp; &nbsp;  <span style="width:120px" class="ml-4">{{ __('lang.adjust-points-by') }}: </span>
                                <input type="text" class="form-control input-small w-100"  name="adjust_point" required>
                        </div>
                        <div class="flex align-items-center justify-content-start">
                            <span style="width:140px">{{ __('lang.current-month-points') }}: </span> 
                            <span class="form-control w-100 ml-2 input-small">{{ $pointStats[0]->current_month_points == null ? 0 : $pointStats[0]->current_month_points}}</span>
                            &nbsp;&nbsp;
                            <span class="mr-2 white-space" style="margin-left: 3%;">{{ __('lang.why-bonus-point-given') }}:</span>&nbsp;
                            <input type="text" class="form-control input-small mr-2" rows="2" name="bonus_point_reason" placeholder="{{__('lang.max-char-lenth-mark-featured')}}" style="max-width:25%;" maxlength="1200"> 
                                
                        </div>
                    </form>
                        <span>
                            @if($errors->has('adjust_point'))
                                <div class="errorMsg" id="adjustPointtError">{{ $errors->first('adjust_point') }}</div>
                            @endif
                        </span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container-fluid max-width mt-4">
        <b>{{ __('lang.history') }}</b>

        <div class="white-wrapper pt-0 pb-0">
            <div class="table">
                <table class="table data-table">

                    <thead style="background-color: #388FB5;">
                        <tr>
                            <th>{{ __('lang.date') }}</th>
                            <th>{{ __('lang.activity') }}</th>
                            <th>{{ __('lang.points-earn') }}</th>
                            <th>{{ __('lang.points-given') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>
    <div>
    <!-- Modal -->
<div class="modal fade custom-model" id="editPointReason" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-body">
                <!-- form -->
                <h5 class="modal-title mb-2">{{ __('lang.why-bonus-point-given') }}</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <form action="" method="POST" id="editBonusForm">
                    @csrf
                    <input type="hidden" name="id" id="editReasonId" value="">
                    <div class="form-group">
                        <label>{{ __('lang.why-bonus-point-given') }}:</label>
                        <input type="text" name="bonus_reason" id="reasonName" class="form-control"
                            placeholder="{{ __('lang.reason-name') }}"
                            value="{{ old('bonus_reason') }}" required>
                        <div class="errorMsg" id="editReasonError"></div>
                    </div>

                    <div class="text-center">
                        <button type="button" id="editCountryButton"
                            class="btn-theme editBonusBtn">{{ __('lang.submit') }}</button>
                        <button type="button" class="btn-theme-border" data-dismiss="modal">{{ __('lang.cancel') }}</button>
                    </div>
                </form>

            </div>

        </div>
    </div>
</div>
@section('scripts')
    <script>
    /*********** datatable ***********/
        $(document).ready(function() {

            var ajaxUrl = "{{url('superadmin/view-point-history/'.$userid)}}" + window.location.search;
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                cache : false,
                processData: false,
                ajax:  ajaxUrl,
                columns: [
		            { 'name': 'created_at.timestamp', 'data': { '_': 'created_at.display', 'sort': 'created_at.timestamp' } },
                    {
                        data: 'activity',
                        name: 'activity'
                    },
                    {
                        data: 'points',
                        "render":function (data)
                        {
                            return '<svg  width="20" viewBox="0 0 65 57"><g transform="translate(-564 -1439)"><path d="M43.437,0A4,4,0,0,1,46.9,2L59.852,24.5a4,4,0,0,1,0,3.99L46.9,51a4,4,0,0,1-3.467,2H17.563A4,4,0,0,1,14.1,51L1.148,28.5a4,4,0,0,1,0-3.99L14.1,2a4,4,0,0,1,3.467-2Z" transform="translate(566 1441)" fill="#777d91"/><path d="M46.426,0A4,4,0,0,1,49.9,2.019l13.969,24.5a4,4,0,0,1,0,3.963L49.9,54.981A4,4,0,0,1,46.426,57H18.574A4,4,0,0,1,15.1,54.981L1.13,30.481a4,4,0,0,1,0-3.963L15.1,2.019A4,4,0,0,1,18.574,0Z" transform="translate(564 1439)" fill="#777d91" opacity="0.41"/><path d="M37.089,1.225l-4.537,9.2L22.4,11.9a2.224,2.224,0,0,0-1.23,3.793l7.343,7.156L26.78,32.96A2.222,2.222,0,0,0,30,35.3l9.08-4.773,9.08,4.773a2.224,2.224,0,0,0,3.224-2.341L49.65,22.851,56.993,15.7a2.224,2.224,0,0,0-1.23-3.793l-10.15-1.48-4.537-9.2a2.225,2.225,0,0,0-3.988,0Z" transform="translate(557.914 1449.537)" fill="#fff"/></g></svg> ' +data;
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false
                    },
                ],
                "searching": false,
                "bLengthChange": false,
            });
        });

        $(".data-table").on("click", ".editPointReason", function() {
            $(".editPointReason").click(function () {
                var Id = $(this).data('id');
                var pointReason = $(this).data('reason');
                $('#editReasonId').val(Id);
                $('#reasonName').val(pointReason);
                $('#editPointReason').attr("style", "display: flex !important");
                $('#editPointReason').modal('show');

            });
        });

        $('.editBonusBtn').on('click', function () {

            var formData = $("#editBonusForm").serialize();
            var Id =  $('#editReasonId').val();
            var ajaxurl = app_url + "/" + logged_user + "/bonus-reason/" + Id + '/edit';
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: formData,
                success: function (data) {
                    if (data.success == true) {

                        $('#editPointReason').modal('hide');

                        $('#bonusAlert').append(

                                                `<div  class="alert alert-success">
                                                <p><strong>` + data.messsage + `</strong></p>
                                                </div>
                                                `)
                        $('body').find('.piaggio-alert .alert').css('animation' , 'alertIN  ease-in-out .35s forwards , alertOut  ease-in-out  .35s 4s forwards')
                        setTimeout(() => {
                            location.reload()
                        }, 2000);
                    }
                },
                error: function (reject) {
                    if (reject.status === 422) {
                        var data = $.parseJSON(reject);
                        $("#editReasonError").html(data.errors.name[0]);

                    }
                }
            });
        });
    </script>
@endsection
@endsection
