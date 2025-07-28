@extends('layouts.app')
@section('content')

<div id="threadAlert"> </div>  
<div class="dash-title container-fluid max-width mb-3">
    <div>
        <a href="{{ url($routeSlug .'/forum/threads') }}"><b>{{__('lang.threads')}} &gt;</b></a> 
        <span class="bradcrumb">{{ ucfirst($comment->thread->title) }}</span>
    </div>
   
    <div class="pull-right">
            <a class="btn-theme" href="{{  url($routeSlug .'/forum/threads') }}"> {{__('lang.back')}}</a>
    </div>
</div>

<div class="discussion2">
    <div class="white-wrapper container-fluid max-width" >
        <div class="flex align-items-start">
            
            <div class="flex-col-12">
                <div class=" col-sm-12 pl-0 pr-0  align-items-start justify-content-start">
                    
                    <div class="flex col-sm-12 pl-0 pr-0  align-items-start justify-content-between">
                        <span class="flex">
                            <img src="{{asset('assets/images/avatar_default.png')}}" class="usericon">
                            <div class="pt-1">
                                <p class="mb-0"><b class="mr-2">{{$comment->thread->creator->name}}</b>
                                {{ $comment->thread->created_at->diffForHumans() }} <br>
                                <span class="text-gray"> {{__('lang.category')}}:  {{$comment->thread->category_id ? $comment->thread->category->name : 'N/A'}} </span>
                                </p>
                            </div>
                        </span>
                        <div class="flex align-items-center">
                            
                                @if( $comment->thread->status != 0 ) 

                                    @if( $comment->thread->creator->id ==  Auth::user()->id || $user->roles[0]->name == "superadmin" || ($user->roles[0]->name == "admin" && $ownerRole[0] != 'superadmin'))
                                
                                        <form action="{{ url($routeSlug . '/forum/threads/status') }}" method="POST" class="mb-0 mr-3">
                                        @csrf    
                                        <input type="hidden" name="threadId" value="{{$comment->thread->id}}">
                                        <input type="hidden" name="status" value="0">
                                        <button type='submit' class='text-danger nobtn'>
                                            <i class="fa fa-ban" aria-hidden="true"></i> {{__('lang.close-thread')}}
                                        </button>
                                        </form>
                                    @endif    

                                @else

                                    @if( $user->roles[0]->name == "superadmin" || ($user->roles[0]->name == "admin" && $ownerRole[0] != 'superadmin') )
                                        <form action="{{ url($routeSlug . '/forum/threads/status') }}" method="POST" class="mb-0 mr-3">
                                            @csrf    
                                            <input type="hidden" name="threadId" value="{{$comment->thread->id}}">
                                            <input type="hidden" name="status" value="1">
                                            <button type='submit' class='text-success nobtn'>
                                                <i class="fa fa-check-square-o" aria-hidden="true"></i>  {{__('lang.unclose-thread')}}
                                            </button>
                                        </form>
                                    @endif
                                @endif

                            @if(Auth::user()->getRoleNames()->first() == 'superadmin')

                                @if($comment->thread->is_pinned == false)
                                    <form action="{{ url($routeSlug . '/thread/update') }}" method="POST"  class="mb-0 mr-1">
                                        @csrf
                                        <input type="hidden" name="thread_id" value="{{$comment->thread->id}}">
                                        <input type="hidden" name="type" value="pin">
                                        <button type='submit' class='btn-theme btn-sm '>{{__('lang.pin-thread')}}</button>
                                    </form> &nbsp; &nbsp;

                                @else
                                    <form action="{{ url($routeSlug . '/thread/update') }}" method="POST"  class="mb-0 mr-1">
                                        @csrf
                                        <input type="hidden" name="thread_id" value="{{$comment->thread->id}}">
                                        <input type="hidden" name="type" value="unpin">
                                        <button type='submit' class='btn-theme btn-sm '>{{__('lang.unpin-thread')}}</button>
                                    </form> &nbsp; &nbsp;
                                @endif

                            @endif

                        

                            @if(Auth::user()->getRoleNames()->first() == 'superadmin')

                                @elseif($comment->thread->status != 0 && ($comment->thread->creator->id != Auth::user()->id) && (Auth::user()->getRoleNames()->first() != 'admin'))
                                <form action="{{ url($routeSlug . '/forum/threads/' . $comment->thread->id . '/report') }}" method="POST" style="margin: 0;">
                                    @csrf    
                                    <input type="hidden" name="threadId" value="{{$comment->thread->id}}">
                                    <input type="hidden" name="status" value="3">
                                    <button type='submit' class='text-danger nobtn ml-2'><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> {{__('lang.report')}}</button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <div style="padding-left:42px">
                        <span class="color">{{$comment->thread->title}}</span><br>
                        <p class="text-gray">{{$comment->thread->body}}</p>
                    </div>
                </div>

                
            </div>
        </div>

        @if($comment->thread->status != 0)
        <hr class="mb-1 mt-0" style="margin-left:-15px;margin-right:-15px;opacity:.5">
        <div class="flex">
            <div class="flex-col-1 pt-2">
            </div>
            <div class="flex-col-12">
                <div class="flex" style="justify-content: flex-start">
                    <div style="margin-right: 10px;">
                        @if(empty($isLiked))
                        <form action="{{ url($routeSlug . '/forum/thread/' . $comment->thread->id . '/like') }}" method="POST" id="likeForm" class="mb-0">
                            @csrf
                            <input type="hidden" name="threadId" class="threadId" value="{{$comment->thread->id}}">
                            <button type='submit' style="display: contents;">
                                <i class="fa fa-thumbs-o-up fa-lg color" aria-hidden="true"  id="add-like"></i> {{__('lang.like')}}
                            </button>
                            
                        </form> 
                        @else
                            <form  method="POST" id="removeLikeForm" action="{{url($routeSlug . '/forum/thread/' . $comment->thread->id . '/like') }}" class="mb-0"> 
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="threadId" class="threadId" value="{{$comment->thread->id}}">
                                <button type='submit' class="btn-nobg" onclick="return confirm('{{ __('lang.delete-confirmation-text') }}')">
                                    <i class="fa fa-thumbs-up fa-lg color" aria-hidden="true"  id="remove-like"></i> {{__('lang.like')}}
                                </button>
                            </form> 
                        @endif
                        @endif
                    </div>
                    <div class="text-gray">
                        <span class="likeCount ">{{$allLikes}}</span> {{__('lang.likes')}}
                    </div>
                </div>
            </div>
        </div>

        <hr class="mt-1" style="margin-left:-15px;margin-right:-15px;opacity:.5">
        <div class="flex">
            <div class="flex-col-1 pt-2">
            </div>
            <div class="flex flex-col-12">
                @if( $comment->thread->status != 0 ) 
                <img src="{{asset('assets/images/superadmin.png')}}" class="usericon">
                <form  action="{{ url( $routeSlug . '/forum/threads/' . $comment->thread->id. '/replies') }}" method="POST"  id="add-reply" class="discussion-form">
                    @csrf
                        <input type="hidden" name="threadId" value="{{$comment->thread->id}}">
                        <input type="text" class="form-control"  name="reply" placeholder="{{__('lang.post-placeholder')}}" value="{{ old('reply') }}" required>
                        <button type="submit" class="ml-1 btn-theme">{{__('lang.add-reply')}}</button>
                </form>

                @endif   
            </div>
        </div>

        @if($errors->has('reply'))
            <div class="errorMsg" id="bodyError">{{ $errors->first('reply') }}</div>
        @endif
        <hr class="mt-1" style="margin-left:-15px;margin-right:-15px;opacity:.5">
        <div class="flex">
            <div class="flex-col-1 pt-2">
            </div>

                

  <div class="flex-col-12">
            <p class="color  mb-2">{{__('lang.all-replies')}}</p> 

            @if( ! (is_null($comment->parentDetails)))
                <!-- parent comment -->
                <div class="flex align-items-start justify-content-start reply-div">  <!-- add active class here --->

                    <!---- toggle button -->
                    <button class="show-reply" id ="show-reply-{{$comment->parentDetails->id}}" data-id="{{$comment->parentDetails->id}}" style="border:none; background:none; display:none;">
                        <i class="icon-toggle-{{$comment->parentDetails->id}} fa fa-chevron-up ml-4" aria-hidden="true" style="color:#2176bd;"></i>
                    </button> 
                    
                    <img src="{{asset('assets/images/avatar_default.png')}}" class="usericon">
                    <div>
                        <p class="mb-0"><b class=" mr-2">{{ $comment->parentDetails->owner->name }} </b> {{ $comment->parentDetails->created_at->diffForHumans() }} &nbsp; &nbsp; </p>
                        <p class="mt-0"> {{ $comment->parentDetails->body }}</p> 
                    </div>
                </div>      
                <hr  style="margin-left:-54px;margin-right:-15px;opacity:.5">
            @endif    


            <!-- reported comment -->
            <div style="padding-left:8%">
                <div class="flex align-items-start justify-content-start reply-div">  <!-- add active class here --->

                    <img src="{{asset('assets/images/avatar_default.png')}}" class="usericon">
                    <div>
                        <p class="mb-0"><b class=" mr-2">{{ $comment->owner->name }} </b> {{ $comment->created_at->diffForHumans() }} &nbsp; &nbsp; </p>
                        <p class="mt-0 error"> {{ $comment->body }}</p>
                        <div class="flex" style="justify-content: flex-start;">
                        
                        @if(Auth::user()->getRoleNames()->first() == 'superadmin')
                            @if($comment->is_hidden != null && $type == 'hidden')
                            
                                <form action="{{ url('superadmin/comment/update') }}" method="POST" class="mb-0 mr-2">
                                    @csrf
                                    <input type="hidden" name="replyId" value="{{$comment->id}}">
                                    <input type="hidden" name="type" value="unhide">
                                    <button type='submit' class='nobtn color'><i class="fa fa-eye" aria-hidden="true"></i> {{__('lang.unhide')}}</button>
                                </form>

                            @endif

                            @if($comment->is_hidden == null && $type == 'hidden')
                                <form action="{{ url('superadmin/comment/update') }}" method="POST" class="mb-0 mr-2">
                                    @csrf
                                    <input type="hidden" name="replyId" value="{{$comment->id}}">
                                    <input type="hidden" name="type" value="hide">
                                    <button type='submit' class='nobtn color'><i class="fa fa-eye" aria-hidden="true"></i> {{__('lang.hide-comment')}}</button>
                                </form>
                            @endif

                            <form action="{{ url('superadmin/reply/'.  $comment->id . '/delete' ) }}" method="POST"  class="mb-0">
                                @csrf
                                <input type="hidden" name="replyId" value="{{ $comment->id}}">
                                <button type="submit" class="text-danger nobtn ml-2 mr-2" style="border: 0px;" onclick="return confirm('{{ __('lang.delete-confirmation-text') }}')"><i class="fa fa-trash" aria-hidden="true"></i></button>
                            </form> 
                        @endif

                        </div>
                        
                    </div>
                </div>      
                <hr  style="margin-left:-54px;margin-right:-15px;opacity:.5">
            </div>    

            @foreach($comment->replies as $reply)
            <div style="padding-left:16%">
                <div class="flex align-items-start justify-content-start reply-div">  <!-- add active class here --->

                    <!---- toggle button -->
                    <button class="show-reply" id ="show-reply-{{$reply->id}}" data-id="{{$reply->id}}" style="border:none; background:none; display:none;">
                        <i class="icon-toggle-{{$reply->id}} fa fa-chevron-up ml-4" aria-hidden="true" style="color:#2176bd;"></i>
                    </button> 
                    
                    <img src="{{asset('assets/images/avatar_default.png')}}" class="usericon">
                    <div>
                        <p class="mb-0"><b class=" mr-2">{{ $reply->owner->name }} </b> {{ $reply->created_at->diffForHumans() }} &nbsp; &nbsp; </p>
                        <p class="mt-0"> {{ $reply->body }}</p>
                        <div class="flex" style="justify-content: flex-start;">
                            <a href="javascript:void(0)" onclick="showReply({{$reply->id}})"> <span class="reply-count-{{$reply->id}}"> </span> {{__('lang.replies')}} </a>  
                        </div>
                    </div>
                </div>      
                 <hr  style="margin-left:-54px;margin-right:-15px;opacity:.5">  
                 <div style="padding-left:8%"> 
                    <div class="child-reply-{{$reply->id}}"> </div>
                </div>  
            </div>     
            @endforeach
        </div>
        </div>
    </div>
</div>

    @section('scripts')
        <script src="https://momentjs.com/downloads/moment.min.js"></script>
        <script type="text/javascript">

            function showReply(replyId)
            {
                
                let ajaxUrl =  app_url + '/'+ logged_user +'/reply/'+replyId+'/child';
                var childClass = 'child-reply-'+replyId;

                $("." + childClass).html(" ");
                $.ajax({
                url: ajaxUrl,
                type:"GET",
                cache : false,
                data :{"_token": "{{ csrf_token() }}" },
                success:function(response){
    
                    if(response) {
                       
                        // toggle
                        var replyDiv = '.child-reply-' + replyId;
                        var iconId = '.icon-toggle-' + replyId;
                       
                        $(replyDiv).slideDown('fast');
                        $(iconId).toggleClass('fa-chevron-down', false);
                        $(iconId).toggleClass('fa-chevron-up', true);

                        var replyLikeCount = Object.keys(response.isReplyLiked).length;
                        var realArray = $.makeArray( response.isReplyLiked )  
            
                        var divId = '#show-reply-' + replyId;  // show toggle button
                        $(divId).show();
                        $.each(response.data,function(index, obj) {

                                let url =  app_url + '/'+ logged_user +'/reply/'+obj.id+'/replies';
                              
                                var res = searchReplyId(realArray, obj.id);

                                $("." + childClass).append(
                                    `<div class="flex align-items-start justify-content-start reply-div">

                                        <!---- toggle button -->
                                        <button class="show-reply" id ="show-reply-${obj.id}" data-id="${obj.id}" style="border:none; background:none; display:none;">
                                            <i class="icon-toggle-${obj.id} fa fa-chevron-up ml-4" aria-hidden="true" style="color:#2176bd;"></i>
                                        </button> 

                                        <img src="{{asset('assets/images/avatar_default.png')}}" class="usericon">
                                        <div>
                                            <p><b>${obj.owner_details.name}</b> &nbsp; &nbsp${moment(obj.created_at).fromNow()}</p>
                                            <p class="mt-0"> ${obj.body}</p>
                                            <p class="mb-0">
                                            <div>
                                                <a href="javascript:void(0)" onclick="showReply(${obj.id})"> <span class="reply-count-${obj.id}"> ${obj.replies_count} </span> {{__('lang.replies')}}
                                                </a>
                                            </div>
                                            </p>

                                        </div>
                                    </div>
                                
                                    <div style="padding-left:8%"> 
                                        <div class="child-reply-${obj.id}"> </div>
                                    </div> 
                                    `
                                );
                                  
                        });
                        
                    }
                },
                });
            } 

            // chceck id exist in reply array
            function searchReplyId(array, id) {
                for(var i=0; i < array.length; i++) {
                   
                    if (array[i].reply_id === id)
                    {
                        return true;   
                    }
                }
                return false;
            }

             // toggle reply

            $(document).on('click', '.show-reply', function(e) {

                var replyId = $(this).data('id');
                var replyDiv = '.child-reply-' + replyId;
                var iconId = '.icon-toggle-' + replyId;
                $(this).parent().toggleClass('active');
                $(replyDiv).slideToggle();


                // check condition for toggle icon
                if($(iconId).hasClass("fa-chevron-up")) {
                    $(iconId).toggleClass('fa-chevron-up', false);
                    $(iconId).toggleClass('fa-chevron-down', true);
                } else {
                    $(iconId).toggleClass('fa-chevron-up', true);
                    $(iconId).toggleClass('fa-chevron-down', false);
                }


            }); 

        </script>     
    @endsection

@endsection