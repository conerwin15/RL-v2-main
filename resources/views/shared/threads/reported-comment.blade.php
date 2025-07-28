@extends('layouts.app')

@section('content')

<div class="container-fluid max-width">
    @if(count($reported_replies)>0) 
        @foreach ($reported_replies as $reported_reply)
            <div  class="discussion" >
                <img src="{{ asset('assets/images/avatar_default.png') }}" class="usericon">
                <div style="width:100%">
                    <div class="flex justify-content-between"style="width:100%  ">
                        <p><b class="mr-2">{{$reported_reply->reply->owner->name}}</b>
                            {{ $reported_reply->created_at->diffForHumans() }}<br>
                            
                        </p>   
                    <div class="flex align-items-center">
                        <a href="{{ url('superadmin/forum/threads/' . $reported_reply->reply->thread_id) }}"> <i class="fa fa-eye" aria-hidden="true"></i>  {{ __('lang.view-thread') }} </a>
                        &nbsp; &nbsp;

                        @if($reported_reply->reply->is_hidden == null)
                            <form action="{{ url('superadmin/comment/update') }}" method="POST" class="mb-0 mr-2">
                                @csrf
                                <input type="hidden" name="replyId" value="{{$reported_reply->reply->id}}">
                                <input type="hidden" name="type" value="hide">
                                <button type='submit' class='nobtn color'><i class="fa fa-eye" aria-hidden="true"></i> {{__('lang.hide-comment')}}</button>
                            </form>
                        @else
                        
                            <form action="{{ url('superadmin/comment/update') }}" method="POST" class="mb-0 mr-2">
                                @csrf
                                <input type="hidden" name="replyId" value="{{$reported_reply->reply->id}}">
                                <input type="hidden" name="type" value="unhide">
                                <button type='submit' class='nobtn color'><i class="fa fa-eye" aria-hidden="true"></i> {{__('lang.unhide')}}</button>
                            </form>

                        @endif

                        <form action="{{ url('superadmin/reply/'.  $reported_reply->reply->id . '/delete' ) }}" method="POST"  class="mb-0">
                            @csrf
                            <input type="hidden" name="replyId" value="{{ $reported_reply->reply->id}}">
                            <button type="submit" class="text-danger nobtn ml-2 mr-2" style="border: 0px;" onclick="return confirm('{{ __('lang.delete-confirmation-text') }}')"><i class="fa fa-trash" aria-hidden="true"></i></button>
                        </form> 
                    </div>
                    </div>
                    <span class="text-dim"> {{ __('lang.comment') }}:</span> {{$reported_reply->reply->body}} <br>
                       
                    </p>
                </div>

            </div>

        @endforeach
    @else
            <tr>
                <td colspan="6" style="text-align: center;">{{ __('lang.no-record') }} </td>
            </tr>

    @endif    

{{ $reported_replies->appends(request()->query())->links('vendor.pagination.bootstrap-4') }}
</div>

</script>
@endsection
