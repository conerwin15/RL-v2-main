@extends('layouts.app')

@section('content')

<div class="row container-fluid max-width">
    <div class="container-fluid user-learning-path col-sm-7">
        <div class="dash-title container-fluid mt-1">
            <div>
                <b> {{ __('lang.learning-paths') }}</b>
            </div>

            <a href="{{url($routeSlug. '/learning-paths')}}" target="_blank"> {{ __('lang.view-all') }} </a>
        </div>
            <div class="row">
                @if(count($userLearningPaths) > 0)
                    @foreach ($userLearningPaths as $userLeaningPath)
                        @if($userLeaningPath->learningPath != null)
                            <div class="col-sm-6 mb-4">
                                <div class="white-wrapper" style="border-radius: 15px;">
                                    <div class="img-wrap">
                                        <img src="{{asset('storage' . Config::get('constant.LEARNING_PATH_STORAGE').'/'.$userLeaningPath->learningPath->featured_image)}}" style="object-fit: contain;">
                                    </div>
                                    <div class="flex justify-content-start">
                                        <div>
                                        <p class="mb-1">
                                            <strong>{{$userLeaningPath->learningPath->name}}</strong>
                                        </p>
                                        <p class="mb-1 show-read-more">
                                            {!! $userLeaningPath->learningPath->description !!}
                                        </p>
                                        <p>
                                            <small class="text-gray mt-2 weight300"><b>{{ __('lang.assign-on') }}</b></small><br>
                                            <small >{{date('d M Y', strtotime($userLeaningPath->created_at))}}</small></p>
                                        </div>
                                    </div>
                                    <div class="w-footer pt-2 pr-3 mb-0 flex justify-content-between align-items-center">
                                        <div class="pl-2">
                                            <b>{{__('lang.status')}}:</b>
                                            @if($userLeaningPath->progress_percentage == null)
                                                <label class="badge badge-warning">{{ __('lang.incomplete') }}</label>
                                            @else
                                                <label class="badge badge-success">{{$userLeaningPath->progress_percentage}}% {{ __('lang.completed') }}</label>
                                            @endif

                                        </div>
                                        <a href="{{url($routeSlug. '/learning-paths/' . $userLeaningPath->learningPath->id)}}" class="btn-theme-border" style="padding: 2px 16px;">
                                            {{__('lang.view')}}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                @else
                <h4 style="text-align: center;">{{__('lang.no-record')}} </h4>
                @endif
            </div>
    </div>

    <div class="col-sm-5 container-fluid" style="background-color:#d5f5ec;">
        <div class="dash-title mt-1">
                <ul class="nav ul-tabs" style="display: flex;margin-bottom: 0;">
                    <li style="width: max-content;margin-left: 12px;"> <b> {{ __('lang.discussion-forum') }}</b></li> &nbsp;&nbsp;&nbsp;&nbsp;
                    <li class="active" id="private" style="background: none !important;"><a data-toggle="tab" href="#private" style="">{{ __('lang.private-threads') }}</a></li> &nbsp;&nbsp;&nbsp;&nbsp;
                    <li id="public" style="background: none !important;"><a data-toggle="tab" href="#public">{{ __('lang.public-threads') }}</a></li>
                </ul>
        </div>
        <br>
        <div id="privateDiv">
            <div class="dash-title container-fluid">
                <div>
                    <b> {{ __('lang.private-threads') }}</b>
                </div>

                <a href="{{url($routeSlug. '/forum/threads?forum_type=1')}}" target="_blank"> {{ __('lang.view-all') }} </a>
            </div>
            <!-- private Thread -->
            <div class="container-fluid max-width mt-3 ">
                @if(count($privateThreads)>0)
                    @foreach ($privateThreads as $thread)
                        <div class="discussion">
                            <img src="{{ $thread->creator->image ? asset('storage' . Config::get('constant.PROFILE_PICTURES') . $thread->creator->image) : asset('assets/images/avatar_default.png') }}" class="usericon">
                            <div style="width:100%">
                                <div class="flex justify-content-between"style="width:100%  ">
                                    <p class="mb-0"><b class="mr-2">{{$thread->creator->name}}</b>
                                        {{ $thread->created_at->diffForHumans() }}
                                        <br>
                                        <span class="text-gray">{{__('lang.category')}}: {{$thread->category_id ? $thread->category->name : 'N/A'}}</span>
                                    </p>

                                        <div class="flex">
                                        @if($thread->status == 0)
                                            <span class="text-danger">
                                            <i class="fa fa-ban" aria-hidden="true"></i>
                                            {{__('lang.closed')}}
                                            </span>
                                        @endif

                                        @if($thread->status == 1)
                                            <span class="text-success"><i class="fa fa-check" aria-hidden="true"></i> {{__('lang.active')}} 
                                            </span>
                                        @endif

                                        </div>
                                </div>
                                <br>
                                <div class="row">

                                    <div class="threadReply col-sm-4" data-id="{{$thread->id}}" data-title="{{$thread->title}}" data-body="{{$thread->body}}" data-link="{{$thread->embedded_link}}" data-creator="{{$thread->creator->name}}" data-time="{{$thread->created_at->diffForHumans()}}" data-image="{{($thread->image!= null) ? asset('storage' . Config::get('constant.THREAD_IMAGE_STORAGE_PATH') . $thread->image) : asset('assets/images/avatar_default.png')}}"
                                        data-category="{{$thread->category->name}}"  data-reply="{{(count($thread->replies) > 0) ? $thread->replies[0]->body: ''}}" data-replyBy = "{{count($thread->replies) > 0 ? ucfirst($thread->getNameById($thread->replies[0]->user_id)) : ''}}" data-replyTime="{{count($thread->replies) > 0 ? ($thread->replies[0]->created_at->diffForHumans() ) : ''}}">
                                        <img src="{{($thread->image!= null) ? asset('storage' . Config::get('constant.THREAD_IMAGE_STORAGE_PATH') . $thread->image) : asset('assets/images/avatar_default.png')}}" alt="image" style="width: 100%;height: 100%;"><br>
                                    </div>

                                    <div class="col-sm-8">
                                        <a href="{{ 'forum/threads/' . $thread->id }}" class="" data-id="{{$thread->id}}" data-title="{{$thread->title}}" data-body="{{$thread->body}}" data-link="{{$thread->embedded_link}}" data-creator="{{$thread->creator->name}}" data-time="{{$thread->created_at->diffForHumans()}}" data-image="{{($thread->image!= null) ? asset('storage' . Config::get('constant.THREAD_IMAGE_STORAGE_PATH') . $thread->image) : asset('assets/images/avatar_default.png')}}"
                                        data-category="{{$thread->category->name}}"  data-reply="{{(count($thread->replies) > 0) ? $thread->replies[0]->body: ''}}" data-replyBy = "{{count($thread->replies) > 0 ? ucfirst($thread->getNameById($thread->replies[0]->user_id)) : ''}}" data-replyTime="{{count($thread->replies) > 0 ? ($thread->replies[0]->created_at->diffForHumans() ) : ''}}">
                                        <b>
                                           @if (strlen($thread->title) > 150 )
                                                {!! substr($thread->title,0,150) !!}...
                                           @else
                                                {!! $thread->title !!}
                                           @endif
                                        </b></a>
                                        <div class="thread-content">
                                            @if($thread->embedded_link != null)
                                                <a href="{{url($routeSlug.'/forum/thread/' . $thread->id. '/preview')}}" target="_blank"><p class=""> {{$thread->embedded_link}}</p></a><br>
                                            @else
                                                <p class="text-gray">
                                                    @if (strlen($thread->body) > 250 )
                                                            {!! substr($thread->body,0,250) !!}...
                                                    @else
                                                            {!! $thread->body !!}
                                                    @endif
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    <br>
                                </div>

                                <div class="d-footer" style="float: right;">
                                    <div>
                                        <span class="text-gray mr-2">{{$thread->is_liked_by_count}} {{__('lang.likes')}} </span>
                                        <span class="text-gray">{{$thread->replies_count}} {{__('lang.replies')}}</span>
                                    </div>
                                </div>
                        </div>

                    </div>

                    @endforeach
                @else
                            <span style="margin-left:40%">{{ __('lang.no-record') }}</span>

                @endif

            </div>
        </div>
 
        <!-- public Thread -->
        <div style="background-color:#d5f5ec; display:none;" id="publicDiv">
            <div class="dash-title container-fluid">
                <div>
                    <b> {{ __('lang.public-threads') }}</b>
                </div>

                <a href="{{url($routeSlug. '/forum/threads')}}" target="_blank"> {{ __('lang.view-all') }} </a>
            </div>
            <div class="container-fluid max-width mt-3 ">
                @if(count($publicThreads)>0)
                    @foreach ($publicThreads as $thread)
                        <div class="discussion">
                            <img src="{{ $thread->creator->image ? asset('storage' . Config::get('constant.PROFILE_PICTURES') . $thread->creator->image) : asset('assets/images/avatar_default.png') }}" class="usericon">
                            <div style="width:100%">
                                <div class="flex justify-content-between"style="width:100%  ">
                                    <p class="mb-0"><b class="mr-2">{{$thread->creator->name}}</b>
                                        {{ $thread->created_at->diffForHumans() }}
                                        <br>
                                        <span class="text-gray">{{__('lang.category')}}: {{$thread->category_id ? $thread->category->name : 'N/A'}}</span>
                                    </p>
                                        <div class="flex">
                                        @if($thread->status == 0)
                                            <span class="text-danger">
                                            <i class="fa fa-ban" aria-hidden="true"></i> 
                                            {{__('lang.closed')}}
                                            </span>
                                        @endif

                                        @if($thread->status == 1)
                                            <span class="text-success"><i class="fa fa-check" aria-hidden="true"></i> {{__('lang.active')}} 
                                            </span>
                                        @endif

                                        </div>
                                </div>
                                <br>
                                <div class="row">

                                    <div class="threadReply col-sm-4" data-id="{{$thread->id}}" data-title="{{$thread->title}}" data-body="{{$thread->body}}" data-link="{{$thread->embedded_link}}" data-creator="{{$thread->creator->name}}" data-time="{{$thread->created_at->diffForHumans()}}" data-image="{{($thread->image!= null) ? asset('storage' . Config::get('constant.THREAD_IMAGE_STORAGE_PATH') . $thread->image) : asset('assets/images/avatar_default.png')}}"
                                        data-category="{{$thread->category->name}}" data-reply="{{(count($thread->replies) > 0) ? $thread->replies[0]->body: ''}}" data-replyBy = "{{count($thread->replies) > 0 ? ucfirst($thread->getNameById($thread->replies[0]->user_id)) : ''}}" data-replyTime="{{count($thread->replies) > 0 ? ($thread->replies[0]->created_at->diffForHumans() ) : ''}}">
                                        <img src="{{($thread->image!= null) ? asset('storage' . Config::get('constant.THREAD_IMAGE_STORAGE_PATH') . $thread->image) : asset('assets/images/avatar_default.png')}}" alt="image" style="width: 100%;height: 100%;">
                                    </div>

                                    <div class="col-sm-8">
                                        <a href="{{ 'forum/threads/' . $thread->id }}" class="" data-id="{{$thread->id}}" data-title="{{$thread->title}}" data-body="{{$thread->body}}" data-link="{{$thread->embedded_link}}" data-creator="{{$thread->creator->name}}" data-time="{{$thread->created_at->diffForHumans()}}" data-image="{{($thread->image!= null) ? asset('storage' . Config::get('constant.THREAD_IMAGE_STORAGE_PATH') . $thread->image) : asset('assets/images/avatar_default.png')}}" 
                                        data-category="{{$thread->category->name}}" data-reply="{{(count($thread->replies) > 0) ? $thread->replies[0]->body: ''}}" data-replyBy = "{{count($thread->replies) > 0 ? ucfirst($thread->getNameById($thread->replies[0]->user_id)) : ''}}" data-replyTime="{{count($thread->replies) > 0 ? ($thread->replies[0]->created_at->diffForHumans() ) : ''}}">
                                        <b>
                                            @if (strlen($thread->title) > 150 )
                                                {!! substr($thread->title,0,150) !!}...
                                            @else
                                                {!! $thread->title !!}
                                            @endif
                                        </b></a>
                                        <div class="thread-content">
                                            @if($thread->embedded_link != null)
                                                <a href="{{url($routeSlug.'/forum/thread/' . $thread->id. '/preview')}}" target="_blank"><p class=""> {{$thread->embedded_link}}</p></a><br>
                                            @else
                                                <p class="text-gray">
                                                    @if (strlen($thread->body) > 250 )
                                                            {!! substr($thread->body,0,250) !!}...
                                                    @else
                                                            {!! $thread->body !!}
                                                    @endif
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="d-footer" style="float: right;">
                                    <div>
                                        <span class="text-gray mr-2">{{$thread->is_liked_by_count}} {{__('lang.likes')}} </span>
                                        <span class="text-gray">{{$thread->replies_count}} {{__('lang.replies')}}</span>
                                    </div>
                                </div>
                        </div>

                    </div>

                    @endforeach
                @else
                            <span style="margin-left:40%">{{ __('lang.no-record') }}</span>

                @endif

            </div>
        </div><br>
    </div>
    <br>
    <!--- Modal --->
    <div class="modal fade custom-model" id="threadModal" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-body">
                    <!-- form -->
                    <h5 class="modal-title mb-2">{{ __('lang.discussion-forum') }}</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <div class="flex-col-12">
                <div class=" col-sm-12 pl-0 pr-0  align-items-start justify-content-start">
                    <div class="flex col-sm-12 pl-0 pr-0  align-items-start justify-content-between">
                        <span class="flex" style="justify-content: flex-start !important;">
                            <img src="{{asset('assets/images/avatar_default.png')}}" class="usericon" style="width:10%">
                            <div class="pt-1">
                                <p class="mb-0"><b class="mr-2" id="thread_creator"></b>
                                <span id="thread_time"> </span> <br>
                                <p class="text-gray"> {{__('lang.category')}}: <span  id="thread_category"></span> </p>
                                </p>
                            </div>
                        </span>
                    </div>

                    <div style="padding-left:42px">
                        <span class="color" id="thread_title"></span><br>
                        <p id="imageBox"></p>
                        <hr>
                        <div id="thread_body" style="word-break: break-all;">
                        </div>
                    </div>
                </div>

                <div class="flex-col-12">
                    <p class="mb-2" style="color:#6A7284;">{{__('lang.reply')}}</p>
                    <div class="flex col-sm-12 pl-0 pr-0  align-items-start justify-content-between">
                        <span class="flex" style="justify-content: flex-start !important;">
                            <img src="{{asset('assets/images/avatar_default.png')}}" class="usericon" style="width:10%">
                            <div class="pt-1">
                                <p class="mb-0"><b class="mr-2" id="reply_owner"></b>
                                    <span id="reply_time"> </span> <br>
                                </p>
                                <p class="mt-0" id="reply_body"></p>
                            </div>
                        </span>
                    </div>
                </div>

            </div>
                </div>

            </div>
        </div>
    </div>
</div>
<style>
    .ul-tabs .active a {
    background: #388FB5 !important;
    color: #fff;
    border-radius: 15px;
    height: 31px;
    padding: 3px;
}
    .ul-tabs a {
    justify-content: center !important;
    display: flex;
    background: #fff;
    border: 1px solid;
    color: #388FB5;
    border-radius: 15px;
    height: 31px;
    padding: 3px;

}
    .ul-tabs li:hover a {
    background: #388FB5 !important;
    color:#fff;
    border-radius: 15px;
    height: 31px;
    padding: 3px;
}
    .ul-tabs li{
    width: 120px;
    text-align: center;
    margin-right:-2px;
}
</style>

    <script>
        $(document).ready(function (){
            $(".threadReply").on("mouseover", function () {

                var url = app_url + '/' + logged_user + '/forum/thread/' + $(this).data('id') + '/preview';
                var link = app_url + '/' + logged_user + '/forum/threads/'+ $(this).data('id');
                var threadBody = $(this).data('body');
                var reply = $(this).data('reply');
                $('#thread_creator').text($(this).data('creator'))
                $('#thread_time').text($(this).data('time'))
                $('#thread_category').text($(this).data('category'))
                $('#thread_title').html($(this).data('title'))
                $("#imageBox").html('<img src="' + $(this).data('image') + '"  style="width:140px;"/>');
                if(threadBody == '')
                {
                    $('#thread_body').html('<a href="' + url + '"/>'+ $(this).data('link') +'</a>')
                } else {

                    if(threadBody.length > 200)
                    {
                        $('#thread_body').html(threadBody.substr(0,200) + ".....");
                    } else{
                        $('#thread_body').html($(this).data('body'));
                    }
                }

                if(reply == '')
                {
                    $('#reply_body').html("No Reply");
                } else {
                    if(reply.length > 200)
                    {
                        $('#reply_body').html(reply.substr(0,200) + ".....");
                    } else{
                        $('#reply_body').html($(this).data('reply'));
                    }
                }
                $('#reply_owner').html($(this).data('replyby'));
                $('#reply_time').text($(this).data('replytime'));
                $('#threadModal').modal('show');
            })

            var mymodal = $('#threadModal');
            mymodal.mouseover(function(e){
                if(!$(e.target).closest('.modal-content').length) {
                    mymodal.modal('hide');
                }
            });
        });

        $('#private').click(function (){
            $('#public').removeClass('active');
            $('#private').addClass('active');
            $('#privateDiv').show();
            $('#publicDiv').hide();
        })

        $('#public').click(function (){
            $('#private').removeClass('active');
            $('#public').addClass('active');
            $('#publicDiv').show();
            $('#privateDiv').hide();
        })
    </script>
@endsection