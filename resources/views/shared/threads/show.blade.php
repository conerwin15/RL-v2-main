@extends('layouts.app')
@section('content')

<div id="threadAlert"> </div>  
<div class="dash-title container-fluid max-width mb-3">
    <div>
        <a href="{{ url($routeSlug .'/forum/threads') }}"><b>{{__('lang.threads')}} &gt;</b></a> 
        <span class="bradcrumb">{!! ucfirst($thread->title) !!}</span>
    </div>
   
    <div class="pull-right">
            <a class="btn-theme" href="{{  url($routeSlug .'/forum/threads') }}"> {{__('lang.back')}}</a>
    </div>
</div>
<div class="discussion2">
    <div class="white-wrapper container-fluid max-width " >
        <div class="flex align-items-start">
            <div class="flex-col-1 pt-2">
                
                <a href="{{ url($routeSlug .'/forum/threads') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="13.909" viewBox="0 0 17 13.909"><path d="M1.226,10.5a.773.773,0,0,1-.167-.251.762.762,0,0,1,0-.59.773.773,0,0,1,.167-.251L7.408,3.226A.773.773,0,1,1,8.5,4.319L3.638,9.182H17.227a.773.773,0,1,1,0,1.545H3.638L8.5,15.59a.773.773,0,1,1-1.093,1.093Z" transform="translate(-1 -3)"/></svg>
                </a>
            </div>
            <div class="flex-col-12">
                <div class=" col-sm-12 pl-0 pr-0  align-items-start justify-content-start">
                    
                    <div class="flex col-sm-12 pl-0 pr-0  align-items-start justify-content-between">
                        <span class="flex">
                            <img src="{{asset('assets/images/avatar_default.png')}}" class="usericon">
                            <div class="pt-1">
                                <p class="mb-0"><b class="mr-2">{{$thread->creator->name}}</b>
                                {{ $thread->created_at->diffForHumans() }} <br>
                                <span class="text-gray"> {{__('lang.category')}}: {{$thread->category_id ? $thread->category->name : 'N/A'}}</span>
                                </p>
                            </div>
                        </span>
                        <div class="flex align-items-center">
                            
                                @if( $thread->status != 0 ) 

                                    @if( $thread->creator->id ==  Auth::user()->id || $user->roles[0]->name == "superadmin" || ($user->roles[0]->name == "admin" && $ownerRole[0] != 'superadmin'))
                                
                                        <form action="{{ url($routeSlug . '/forum/threads/status') }}" method="POST" class="mb-0 mr-3">
                                        @csrf    
                                        <input type="hidden" name="threadId" value="{{$thread->id}}">
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
                                            <input type="hidden" name="threadId" value="{{$thread->id}}">
                                            <input type="hidden" name="status" value="1">
                                            <button type='submit' class='text-success nobtn'>
                                                <i class="fa fa-check-square-o" aria-hidden="true"></i>  {{__('lang.unclose-thread')}}
                                            </button>
                                        </form>
                                    @endif
                                @endif

                                @if($pinnedThread == null)
                                    <form action="{{ url($routeSlug . '/thread/update') }}" method="POST"  class="mb-0 mr-1">
                                        @csrf
                                        <input type="hidden" name="thread_id" value="{{$thread->id}}">
                                        <input type="hidden" name="type" value="pin">
                                        <button type='submit' class='btn-theme btn-sm '>{{__('lang.pin-thread')}}</button>
                                    </form> &nbsp; &nbsp;

                                @else
                                    <form action="{{ url($routeSlug . '/thread/update') }}" method="POST"  class="mb-0 mr-1">
                                        @csrf
                                        <input type="hidden" name="thread_id" value="{{$thread->id}}">
                                        <input type="hidden" name="type" value="unpin">
                                        <button type='submit' class='btn-theme btn-sm '>{{__('lang.unpin-thread')}}</button>
                                    </form> &nbsp; &nbsp;
                                @endif

                            @if(Auth::user()->getRoleNames()->first() == 'admin' && $thread->is_hidden == false)
                                <form action="{{ url($routeSlug . '/forum/thread/hide') }}" method="POST"  class="mb-0 mr-2">
                                    @csrf
                                    <input type="hidden" name="thread_id" value="{{$thread->id}}">
                                    <button type='submit' class='btn-theme btn-sm '>{{__('lang.report-hide')}}</button>
                                </form> &nbsp; &nbsp;
                            @endif

                            @if($thread->status != 0 && $thread->creator->id != Auth::user()->id)
                                @if( !empty($isSubscriber))
                                <form action="{{ url($routeSlug . '/forum/thread/unsubscribe') }}" method="POST" class="mb-0 mr-2">
                                    @csrf
                                    <input type="hidden" name="thread_id" value="{{$thread->id}}">
                                    <button type='submit' class='btn-theme btn-sm'>{{__('lang.unsubscribe')}}</button>
                                </form>
                                @else   
                                <form action="{{ url($routeSlug . '/forum/thread/subscribe') }}" method="POST"  class="mb-0 mr-2">
                                    @csrf
                                    <input type="hidden" name="thread_id" value="{{$thread->id}}">
                                    <button type='submit' class='btn-theme btn-sm '>{{__('lang.subscribe')}}</button>
                                </form>
                                @endif
                            @endif

                            @if($thread->creator->id == Auth::user()->id && $thread->status != 0)
                                <a  href="{{ $thread->id . '/edit' }}" class="mr-1">
                                <i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                            @endif

                            @if(Auth::user()->getRoleNames()->first() == 'superadmin' || $thread->creator->id == Auth::user()->id)
                            <form action="{{ url($routeSlug .'/forum/threads/' .$thread->id) }}" method="POST"  class="mb-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-danger nobtn ml-2 mr-2" style="border: 0px;" onclick="return confirm('{{ __('lang.delete-confirmation-text') }}')"><i class="fa fa-trash" aria-hidden="true"></i></button>
                            </form> 
                            @endif

                            @if(Auth::user()->getRoleNames()->first() == 'superadmin')

                                @elseif($thread->status != 0 && ($thread->creator->id != Auth::user()->id) && (Auth::user()->getRoleNames()->first() != 'admin'))
                                <form action="{{ url($routeSlug . '/forum/threads/' . $thread->id . '/report') }}" method="POST" style="margin: 0;">
                                    @csrf    
                                    <input type="hidden" name="threadId" value="{{$thread->id}}">
                                    <input type="hidden" name="status" value="3">
                                    <button type='submit' class='text-danger nobtn ml-2'><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> {{__('lang.report')}}</button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <div style="padding-left:42px">
                        <span class="color">{!! $thread->title !!}</span><br>
                        <div> <img src="{{($thread->image!= null) ? asset('storage' . Config::get('constant.THREAD_IMAGE_STORAGE_PATH') . $thread->image) : asset('assets/images/avatar_default.png')}}" alt="image" style="width: 20%;"> </div><br>
                        @if($thread->embedded_link != null)
                            <a href="{{url($routeSlug.'/forum/thread/' . $thread->id. '/preview')}}" target="_blank"><p class=""> {{$thread->embedded_link}}</p></a><br>
                        @else
                            <p class="text-gray">{!! $thread->body !!}</p>
                        @endif
                    </div>
                </div>

                
            </div>
        </div>
        @if($thread->status != 0)
        <hr class="mb-1 mt-0" style="margin-left:-15px;margin-right:-15px;opacity:.5">
        <div class="flex">
            <div class="flex-col-1 pt-2">
            </div>
            <div class="flex-col-12">
                <div class="flex" style="justify-content: flex-start">
                    <div style="margin-right: 10px;">
                        @if(empty($isLiked))
                        <form action="{{ url($routeSlug . '/forum/thread/' . $thread->id . '/like') }}" method="POST" id="likeForm" class="mb-0">
                            @csrf
                            <input type="hidden" name="threadId" class="threadId" value="{{$thread->id}}">
                            <button type='submit' style="display: contents;">
                                <i class="fa fa-thumbs-o-up fa-lg color" aria-hidden="true"  id="add-like"></i> {{__('lang.like')}}
                            </button>
                            
                        </form> 
                        @else
                            <form  method="POST" id="removeLikeForm" action="{{url($routeSlug . '/forum/thread/' . $thread->id . '/like') }}" class="mb-0"> 
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="threadId" class="threadId" value="{{$thread->id}}">
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

            <div class="">
                @if( $thread->status != 0 ) 
                <form  action="{{ url( $routeSlug . '/forum/threads/' . $thread->id. '/replies') }}" method="POST"  id="add-reply" class="col-xs-12 col-sm-12" enctype="multipart/form-data">
                    @csrf
                        <input type="hidden" name="threadId" value="{{$thread->id}}">
                        <div class="row">
                            <img src="{{asset('assets/images/superadmin.png')}}" class="usericon">
                            <div class="col-xs-8 col-sm-8 col-md-8">
                                <textarea class="form-control descriptionText" rows="4" name="reply" required>{{ old('reply') }}</textarea> &nbsp; &nbsp;
                            </div>
                        </div>

                        <div class="form-group col-sm-8" style="margin-left: 25px;">
                            <label> <strong>Upload Picture:</strong></label>
                            <div class="featuredimg">
                                <div>
                                    <svg  width="30" height="30" viewBox="0 0 30 30"><g transform="translate(-1 -1)"><path d="M27.92,1H4.08A3.08,3.08,0,0,0,1,4.08V27.92A3.08,3.08,0,0,0,4.08,31H13V29H4.08A1.08,1.08,0,0,1,3,27.92V4.08A1.08,1.08,0,0,1,4.08,3H27.92A1.08,1.08,0,0,1,29,4.08V27.92A1.08,1.08,0,0,1,27.92,29H19v2h8.92A3.08,3.08,0,0,0,31,27.92V4.08A3.08,3.08,0,0,0,27.92,1Z" fill="#0097c4"/><path d="M22.29,15.71a1,1,0,1,0,1.42-1.42L16,6.59l-7.71,7.7a1,1,0,0,0,1.42,1.42L15,10.41V31h2V10.41Z" fill="#0097c4"/></g></svg>
                                    <img src="" class="preview">
                                    <input type="file" id="file" name="image"  accept="image/x-png,image/jpeg"/>
                                    <div class="mt-2" id="file-preview">{{__('lang.drop-file')}}</div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="ml-1 btn-theme" style="margin-top: 1%;margin-left: 43px !important;">{{__('lang.add-reply')}}</button>
                </form>

                @endif   
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
                @foreach($replies as $reply)
                <div class="flex align-items-start justify-content-start reply-div discussion2"> 
                     <!-- add active class here --->

                    <!---- toggle button -->
                    <button class="show-reply" id ="show-reply-{{$reply->id}}" data-id="{{$reply->id}}" style="border:none; background:none; display:none;">
                        <i class="icon-toggle-{{$reply->id}} fa fa-chevron-up ml-4" aria-hidden="true" style="color:#2176bd;"></i>
                    </button> 
                    
                    <img src="{{asset('assets/images/avatar_default.png')}}" class="usericon">
                    <div>
                        <p class="mb-0"><b class=" mr-2">{{ $reply->owner->name }} </b> {{ $reply->created_at->diffForHumans() }} &nbsp; &nbsp; </p>
                        <p class="mt-0"> {!!$reply->body !!}</p>
                        @if($reply->image!= null)
                                <img src="{{asset('storage' . Config::get('constant.THREAD_REPLY_IMAGE_STORAGE_PATH') .'/'.$reply->image)}}" alt="image" style="width: 135px;"><br>
                        @endif
                        <div class="flex" style="justify-content: flex-start;">
                            @if($thread->status != 0 )
                                <a href="javascript:void(0)" onClick="$('#first-relpy-{{$reply->id}}').show();" class="mr-2">Reply</a>
                            @endif    
                            <a href="javascript:void(0)" onclick="showReply({{$reply->id}})"> <span class="reply-count-{{$reply->id}}"> {{$reply->replies_count}} </span> {{__('lang.replies')}} </a>  
                        &nbsp; &nbsp;  
                        
                            @if($thread->status != 0 )
                                <div class="like-dislike-{{$reply->id}}">
                                    @if( !empty($isReplyLiked) && in_array($reply->id, array_column($isReplyLiked, 'reply_id')))

                                    <form  method="POST" id="removeReplyLikeForm" action="{{url($routeSlug . '/reply/' . $reply->id . '/like')}}" class="mx-2"  style="margin-top: 7px !important;"> 
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="replyId" class="reply-{{$reply->id}}" value="{{$reply->id}}">
                                        <button type='submit' class="btn-nobg" onclick="return confirm('{{ __('lang.delete-confirmation-text') }}')">
                                            <i class="fa fa-thumbs-up fa-lg color" aria-hidden="true"  ></i> 
                                        </button>
                                    </form> 
                                    @else
                                    <form id="replyLikeForm" action="{{url($routeSlug . '/reply/' . $reply->id . '/like')}}" method="POST" class="mx-2 replyLikeForm" style="margin-top: 8px !important;">
                                        @csrf
                                        <input type="hidden" name="replyId" class="reply-{{$reply->id}}" value="{{$reply->id}}">
                                        <button type='submit' class="btn-nobg" >
                                            <i class="fa fa-thumbs-o-up fa-lg color" aria-hidden="true"  id="reply-add-like"></i>
                                        </button>
                                    </form> 
                                    @endif
                                </div> 
                            @endif    
    
                            <span class="allReplyCount-{{$reply->id}}">{{$reply->reply_like_count}} </span> &nbsp; {{__('lang.likes')}} &nbsp;

                            @if(Auth::user()->getRoleNames()->first() != 'superadmin' &&  $reply->user_id != Auth::user()->id && $thread->status != 0 ) 
                                <!--- commnet report --->

                                @if(Auth::user()->getRoleNames()->first() == 'admin')
                                    <form action="{{ url($routeSlug . '/reply/' . $reply->id . '/comment/hide') }}" method="POST" style="margin: 0;">
                                        @csrf    
                                        <input type="hidden" name="replyId" value="{{$reply->id}}">
                                        <button type='submit' class='text-danger nobtn ml-2'><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> {{__('lang.report-hide')}}</button>
                                    </form>

                                @endif
                                    <form class="mx-2"  action="{{ url($routeSlug . '/reply/' . $reply->id . '/report/comment') }}" method="POST">
                                        @csrf    
                                        <input type="hidden" name="replyId" value="{{$reply->id}}">
                                        <button type='submit' class='text-danger nobtn ml-2'><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> {{__('lang.report')}}</button>
                                    </form> &nbsp; 
                            
                            @endif

                            @if($reply->user_id == Auth::user()->id)
                                &nbsp;<a  href="{{ url($routeSlug . '/reply/' . $reply->id . '/edit') }}" class="mr-2" style="margin-top: 6px;">
                                <i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                            @endif
                        </div>
                        
                    </div>
                </div>
                
                <hr class="mt-1" style="margin-left:-15px; margin-right:-15px; opacity:.5;">
                    <div class="" style="display: none;margin-left:15px;" id="first-relpy-{{$reply->id}}">
                        <div>
                            <form  action="{{ url( $routeSlug . '/reply/' . $reply->id. '/replies') }}" method="POST"  id="add-child-reply" class="add-child-reply" enctype="multipart/form-data">
                                @csrf
                                    <input type="hidden" name="threadId" value="{{$thread->id}}">
                                    <input type="hidden" name="replyId" value="{{$reply->id}}">
                                    <div class="row">
                                        <img src="{{asset('assets/images/superadmin.png')}}" class="usericon">
                                        <div class="col-xs-8 col-sm-8 col-md-8">
                                            <textarea class="form-control descriptionText" rows="4" name="childReply" required>{{ old('childReply') }}</textarea>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="form-group col-sm-8" style="margin-left: 25px;">
                                        <label> <strong>Upload Picture:</strong></label>
                                        <div class="featuredimg">
                                            <div>
                                                <svg  width="30" height="30" viewBox="0 0 30 30"><g transform="translate(-1 -1)"><path d="M27.92,1H4.08A3.08,3.08,0,0,0,1,4.08V27.92A3.08,3.08,0,0,0,4.08,31H13V29H4.08A1.08,1.08,0,0,1,3,27.92V4.08A1.08,1.08,0,0,1,4.08,3H27.92A1.08,1.08,0,0,1,29,4.08V27.92A1.08,1.08,0,0,1,27.92,29H19v2h8.92A3.08,3.08,0,0,0,31,27.92V4.08A3.08,3.08,0,0,0,27.92,1Z" fill="#0097c4"/><path d="M22.29,15.71a1,1,0,1,0,1.42-1.42L16,6.59l-7.71,7.7a1,1,0,0,0,1.42,1.42L15,10.41V31h2V10.41Z" fill="#0097c4"/></g></svg>
                                                <img src="" class="preview">
                                                <input type="file" id="file" name="image"  accept="image/x-png,image/jpeg"/>
                                                <div class="mt-2" id="file-preview">{{__('lang.drop-file')}}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="ml-1 btn-theme" style="margin-top: 1%;margin-left: 43px !important;">{{__('lang.add-reply')}}</button>
                            </form>
                        </div>
                    </div>
                    @if($errors->has('reply'))
                        <div class="errorMsg" id="bodyError">{{ $errors->first('reply') }}</div>
                    @endif
                
                    <div style="padding-left:8%"> 
                    
                        <div class="child-reply-{{$reply->id}}"> </div>
                    </div>    
            
                <!-- @if(Auth::user()->id == $reply->user_id)   
                <div class="d-footer">  
                    <div class="flex align-items-center">
                        <button class="mr-2 btn-nobg editReply" type="submit" data-toggle="modal"
                            data-backdrop="static" data-keyboard="false" class="btn btn-success"
                            data-id="{{ $reply->id }}" data-reply="{{ $reply->body }}"
                            data-target="#editReply"
                            data-href="{{ url($routeSlug. '/forum/threads/' .$thread->id. '/replies/'. $reply->id) }}" style="display:contents;color:#3490dc">
                            <i class="fa fa-pencil" aria-hidden="true"></i> {{ __('lang.edit') }} &nbsp;
                        </button>
                        
                        <form action="{{ url($routeSlug. '/forum/threads/' .$thread->id. '/replies', $reply->id) }}" method="POST" class="mb-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-danger mb-0"style="background:transparent !important; border:0px;" ><i class="fa fa-trash" aria-hidden="true"></i> {{__('lang.delete')}}</button>
                        </form>
                    </div>
                </div>
                @endif -->
                <hr  style="margin-left:-54px;margin-right:-15px;opacity:.5">
                @endforeach
            </div>
        </div>
    </div>
</div>



    
        

    
<div class="col-sm-12">
    
<p class="text-center">
        
        {{ $replies->appends(request()->query())->links('vendor.pagination.bootstrap-4') }}
</p>
</div>
</div>

<!-- Modal -->
<div class="modal fade custom-model" id="editReply" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <h5 class="modal-title mb-3">{{ __('lang.edit-comment') }}</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <!-- form -->
                <form action="" method="POST" id="editReplyForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="replyId" value="">
                    <div class="form-group">
                        <label>{{ __('lang.comment') }}:</label>
                        <input type="text" name="reply" id="reply" class="form-control" placeholder="{{ __('lang.comment') }}" value="" required>
                        <div class="errorMsg" id="replyEditError">  </div>    
                    </div>

                    <div class="text-center">
                        <button type="button" id="editReplyButton"  class="btn-theme editReplyButton">{{ __('lang.submit') }}</button>
                        <button type="button" class="btn-theme-border" data-dismiss="modal">Close</button>
                    </div>
                </form>

            </div>
        </div>

    </div>
    
</div>


    @section('scripts')
        <script src="https://momentjs.com/downloads/moment.min.js"></script>
        <script src="//cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
        <script type="text/javascript">
        
            $('#likeForm').on('submit',function(e){
                e.preventDefault();
                let thread_id = $('.threadId').val(); 
                let ajaxUrl = app_url + '/'+ logged_user + '/thread/' + thread_id + "/like";
              
                $.ajax({
                url: ajaxUrl,
                type:"POST",
                data:{
                    "_token": "{{ csrf_token() }}",
                    threadId:thread_id,
                },
                success:function(response){
                    var img_src =  app_url + "/assets/images/like.png";
                    $("#add-like").attr("src", img_src);
                    $('.likeCount').html(response.allLikes)
                    location.reload();
                },
                });
            });    

             // remove likes 
            $('#removeLikeForm').on('submit',function(e){
                e.preventDefault();
                let thread_id = $('.threadId').val();
                let ajaxUrl =  app_url + '/'+ logged_user +'/thread/'+thread_id+'/like';

                $.ajax({
                    url: ajaxUrl,
                    type:"DELETE",
                    data:{
                        "_token": "{{ csrf_token() }}",
                        threadId:thread_id,
                },
                success:function(response){
                    var img_src =  app_url + "/assets/images/unlike.png";
                    $("#remove-like").attr("src", img_src);
                    $('.likeCount').html(response.allLikes)
                    location.reload();
                },
                });
            }); 

            // edit comment
            $(function () {
                    $(".editReply").click(function () {
                        var replyId = $(this).data('id');
                        var reply = $(this).data('reply');
                       
                        $('#replyId').val(replyId); 
                        $('#reply').val(reply); 
                        $("#replyEditError").html("");
                    
                    });
            });

            $("#editReplyButton").click(function(){
                event.preventDefault();
                let href = $('.editReply').attr('data-href');
                let data = $('#editReplyForm').serialize();
              
                $.ajax({
                        url: href,
                        type: 'POST',
                        data: $('#editReplyForm').serialize(),
                        success: function(result) {
                           
                        $('#editReply').modal('hide');
                             
                        if(result.success == true){
                                $('#threadAlert').append(
                                            `<div  class="alert alert-success">
                                            <p>Comment updated successfully</p>
                                            </div>
                                            `)
                                $('body').find('.piaggio-alert .alert').css('animation' , 'alertIN  ease-in-out .35s forwards , alertOut  ease-in-out  .35s 4s forwards')
                                setTimeout(() => {
                                    location.reload()
                                }, 2000);
                            } else {
                                $('#threadAlert').append(
                                                `<div  class="alert alert-error">
                                                <p>Comment not updated </p>
                                                </div>
                                                `)
                                $('body').find('.piaggio-alert .alert').css('animation' , 'alertIN  ease-in-out .35s forwards , alertOut  ease-in-out  .35s 4s forwards')
                                setTimeout(() => {
                                    location.reload()
                                }, 2000);
                            }
                        },  

                        error: function (reject) {
                           
                            if( reject.status === 422 ) {
                                var data = $.parseJSON(reject.responseText);
                                if (typeof data.errors.name !== 'undefined') {
                                    $("#replyEditError").html(data.errors.name[0]);
                                }
                               
                            }
                        }  
                })
            });   


            $(document).on('submit', '#replyLikeForm', function(e) {
                e.preventDefault();
                var myform = $(this).closest("form"); 
                var rid = $(this).closest("form").find("input[name='replyId']").val();
               
                $.ajax({
                    url: $(this).attr('action'),
                    type:"POST",
                    cache : false,
                    data: myform.serialize(), 
                    success:function(response){
                        var childreplyCount = 'allReplyCount-'+rid;
                        $("." + childreplyCount).html(response.allLikes)
                        var form = showRemoveLikeForm(rid);
                        $('.like-dislike-' + rid).html(form);
                        $('allReplyCount-' + rid).html(response.allLikes);
                    },
                });
            }); 

            //remove reply lkes
            $(document).on('submit', '#removeReplyLikeForm', function(e) {
                e.preventDefault();
                var myform = $(this).closest("form"); 
                var rid = $(this).closest("form").find("input[name='replyId']").val();

                $.ajax({
                url: $(this).attr('action'),
                type:"DELETE",
                cache : false,
                data: myform.serialize(), 
                success:function(response){
                    var childreplyCount = 'allReplyCount-'+rid;
                    $("." + childreplyCount).html(response.allLikes)
                    var form = showLikeForm(rid);
                    $('.like-dislike-' + rid).html(form);
                    $('allReplyCount-' + rid).html(response.allLikes);
                    $('#file').next('div').html(" ");
                },
                });
            }); 

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
                        $('#file').html(" ");
                        $.each(response.data,function(index, obj) {

                                let url =  app_url + '/'+ logged_user +'/reply/'+obj.id+'/replies';
                                let likeurl = app_url + '/' + logged_user + '/reply/' + obj.id + '/like';
                                let reportCommentUrl = app_url + '/' + logged_user + '/reply/' + obj.id + '/report/comment';
                                let commentHideUrl = app_url + '/' + logged_user + '/reply/' + obj.id + '/comment/hide';
                                let likeForm ='';
                                let commentReport = '';
                                let threadStatus = {{$thread->status}};
                                let editForm = '';
                                let editFormLink = app_url + '/' + logged_user + '/reply/' + obj.id + '/edit';
                                let imageDiv = '';
                                let imagePath = "{{asset('storage' . Config::get('constant.THREAD_REPLY_IMAGE_STORAGE_PATH'))}}" + '/' + obj.image;
                                if(obj.image != null)
                                {
                                     imageDiv = `<img src="${imagePath}" style="width:135px;>`
                                }
                                console.log(imagePath);
                                var res = searchReplyId(realArray, obj.id);

                                // add conditon to show like/dislike form

                                if(threadStatus == 1) {

                                    if(replyLikeCount > 0 &&(res == true)) {
                                    
                                        likeForm = `<form  method="POST" id="removeReplyLikeForm" action="${likeurl}" class="mx-2 removeReplyLikeForm"  style="margin-top: 7px !important;" class="mx-2"> 
                                                                @csrf
                                                                @method('DELETE')
                                                                <input type="hidden" name="replyId" class="reply-${obj.id}" value="${obj.id}">
                                                                <button type='submit' class="btn-nobg" onclick="return confirm('{{ __('lang.delete-confirmation-text') }}')">
                                                                    <i class="fa fa-thumbs-up fa-lg color" aria-hidden="true"  ></i> 
                                                                </button>
                                                            </form>`;
                                    } else {                        
                                        likeForm =  `<form id="replyLikeForm" action="${likeurl}" method="POST" class="mx-2 replyLikeForm" style="margin-top: 7px !important;">
                                                                @csrf
                                                                <input type="hidden" name="replyId" class="reply-${obj.id}" value="${obj.id}">
                                                                <button type='submit' class="btn-nobg" >
                                                                    <i class="fa fa-thumbs-o-up fa-lg color" aria-hidden="true"  id="reply-add-like"></i>
                                                                </button>
                                                        </form>`;
                                    }                    
                               

                                // comment report form

                                    if(logged_user != 'superadmin' && logged_user_id != obj.user_id) {

                                        if(logged_user == 'admin') {

                                            commentReport = `<form action="${commentHideUrl}" method="POST" style="margin: 0;">
                                                                @csrf    
                                                                <input type="hidden" name="replyId" value="${obj.id}">
                                                                <button type='submit' class='text-danger nobtn ml-2'><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> {{__('lang.report-hide')}}</button>
                                                            </form> &nbsp` + 

                                                            `<form class="mx-2"  action="${reportCommentUrl}" method="POST">
                                                                @csrf    
                                                                <input type="hidden" name="replyId" value="${obj.id}">
                                                                <button type='submit' class='text-danger nobtn ml-2'><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> {{__('lang.report')}}</button>
                                                            </form> &nbsp;`
                                        } else {

                                            commentReport = `<form class="mx-2"  action="${reportCommentUrl}" method="POST">
                                                                @csrf    
                                                                <input type="hidden" name="replyId" value="${obj.id}">
                                                                <button type='submit' class='text-danger nobtn ml-2'><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> {{__('lang.report')}}</button>
                                                            </form> &nbsp;`;
                                        }
                                        
                                    } else {
                                        commentReport = '';
                                    } 

                                }    

                                if(logged_user_id == obj.user_id)
                                {
                                    editForm = `&nbsp;<a  href="${editFormLink}" class="mr-2" style="margin-top: 6px;">
                                                <i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>`;
                                }
                                $("." + childClass).append(
                                    `<div class="flex align-items-start justify-content-start reply-div discussion2">

                                        <!---- toggle button -->
                                        <button class="show-reply" id ="show-reply-${obj.id}" data-id="${obj.id}" style="border:none; background:none; display:none;">
                                            <i class="icon-toggle-${obj.id} fa fa-chevron-up ml-4" aria-hidden="true" style="color:#2176bd;"></i>
                                        </button> 

                                        <img src="{{asset('assets/images/avatar_default.png')}}" class="usericon">
                                        <div>
                                            <p><b>${obj.owner_details.name}</b> &nbsp; &nbsp${moment(obj.created_at).fromNow()}</p>
                                            <p class="mt-0"> ${obj.body}</p>`+
                                            `<p>${imageDiv}</p><br>`+
                                            `<p class="mb-0">
                                            <div class="flex">` + 

                                                (threadStatus == 1 ? `<a href="javascript:void(0)" class="mr-2" id="childreplyBtn" onClick="$('#thread-div-${obj.id}').show();">{{__('lang.reply')}}</a>` : '') +
                                              
                                                `<a href="javascript:void(0)" onclick="showReply(${obj.id})"> <span class="reply-count-${obj.id}"> ${obj.replies_count} </span> {{__('lang.replies')}}
                                                </a> <div class="like-dislike-${obj.id}">` +
                                                 likeForm
                                                +`</div>&nbsp; &nbsp;<span class="allReplyCount-${obj.id}"> ${obj.reply_like_count}</span> &nbsp; {{__('lang.likes')}} &nbsp;`
                                                + commentReport +
                                                editForm +
                                            
                                            `</div>
                                            </p>

                                        </div>
                                    </div>
                                    <div class="thread-div-editor" style="padding-left:8%; display: none;" id="thread-div-${obj.id}">
                    
                                        <form  action="${url}" method="POST"  id="add-child-reply" class="add-child-reply" enctype="multipart/form-data">
                                            @csrf
                                                <input type="hidden" name="threadId" value="${obj.thread_id}">
                                                <input type="hidden" name="replyId" value="${obj.id}">

                                                <div class="row">
                                                    <img src="{{asset('assets/images/superadmin.png')}}" class="usericon">
                                                    <div class="col-xs-8 col-sm-8 col-md-8">
                                                        <textarea class="form-control descriptionText-${obj.id}" rows="4" name="childReply" required>{{ old('childReply') }}</textarea>
                                                    </div>
                                                </div>
                                                <br>
                                                <div class="form-group col-sm-8" style="margin-left: 25px;">
                                                    <label> <strong>Upload Picture:</strong></label>
                                                    <div class="featuredimg" style="">
                                                        <div>
                                                            <svg  width="30" height="30" viewBox="0 0 30 30"><g transform="translate(-1 -1)"><path d="M27.92,1H4.08A3.08,3.08,0,0,0,1,4.08V27.92A3.08,3.08,0,0,0,4.08,31H13V29H4.08A1.08,1.08,0,0,1,3,27.92V4.08A1.08,1.08,0,0,1,4.08,3H27.92A1.08,1.08,0,0,1,29,4.08V27.92A1.08,1.08,0,0,1,27.92,29H19v2h8.92A3.08,3.08,0,0,0,31,27.92V4.08A3.08,3.08,0,0,0,27.92,1Z" fill="#0097c4"/><path d="M22.29,15.71a1,1,0,1,0,1.42-1.42L16,6.59l-7.71,7.7a1,1,0,0,0,1.42,1.42L15,10.41V31h2V10.41Z" fill="#0097c4"/></g></svg>
                                                            <img src="" class="preview">
                                                            <input type="file" id="file2" name="image"  accept="image/x-png,image/jpeg"/>
                                                            <div class="mt-2">{{__('lang.drop-file')}}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <button type="submit" class="ml-1 btn-theme" style="margin-left: 43px !important;">{{__('lang.add-reply')}}</button>
                                        </form>
                                      
                                    </div>
                                    <div style="padding-left:8%"> 
                                        <div class="child-reply-${obj.id}"> </div>
                                    </div> 
                                    `
                                    ///console.log("error")
                                );

                                $(document).on('change','#file2' , function(){
                                    $(this).next('div').html($(this)[0].files[0].name)
                                })
                                CKEDITOR.replaceAll( "descriptionText-"+obj.id, {
                                    removeButtons: 'PasteFromWord',
                                    removePlugins: 'link, sourcearea, horizontalrule, pastetext, pastefromword, blockquote, specialchar',
                                    addPlugins: 'smiley, emoji',
                                });
                                  
                        });
                        
                    }
                },
                });
            } 


            // add child reply
            $(document).on('submit', '#add-child-reply', function(e) {
                e.preventDefault();
                var form = $(this).closest("form");
                var myform = new FormData(form[0]);
                console.log(myform);
                $.ajax({
                    url: $(this).attr('action'),
                    type:"POST",
                    data: myform,
                    processData: false,
                    contentType: false,
                    success:function(response){
                        var replyClass = '.reply-count-' + response.replyId;
                        $('.reply-input') .val('');                // empty input fields
                        $(replyClass).text(response.replyCount);  // change counting of replies
                        showReply(response.replyId);              // show new reply
                        
                    },
                });

                return false;
            });
            
           
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
            

            function showLikeForm(rid)
            {
                let likeurl = app_url + '/' + logged_user + '/reply/' + rid + '/like';
                var form = `
                    <form  method="POST" id="replyLikeForm" action="${likeurl}" class="mx-2 replyLikeForm"  style="margin-top: 7px !important;"> 
                        @csrf
                        <input type="hidden" name="replyId" class="reply-${rid}" value="${rid}">
                        <button type='submit' class="btn-nobg">
                            <i class="fa fa-thumbs-o-up fa-lg color" aria-hidden="true"  ></i> 
                        </button>
                    </form> 
                `;

                return form;
            }
            
            function showRemoveLikeForm(rid)
            {
                let likeurl = app_url + '/' + logged_user + '/reply/' + rid + '/like';
                var form = `
                    <form  method="POST" id="removeReplyLikeForm" action="${likeurl}" class="mx-2 removeReplyLikeForm"  style="margin-top: 7px !important;"> 
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="replyId" class="reply-${rid}" value="${rid}">
                        <button type='submit' class="btn-nobg" onclick="return confirm('{{ __('lang.delete-confirmation-text') }}')">
                            <i class="fa fa-thumbs-up fa-lg color" aria-hidden="true"  ></i> 
                        </button>
                    </form> 
                `;

                return form;
            }
            $(document).ready(function() {
                CKEDITOR.replaceAll( 'descriptionText', {
                    removeButtons: 'PasteFromWord',
                    removePlugins: 'link, sourcearea,  horizontalrule, pastetext, pastefromword, blockquote, specialchar',
                });
		    });

            $(document).on('change','#file' , function(){
                $('#file-preview').html($(this)[0].files[0].name);
            })

            $(document).on('change','#file1' , function(){
                $('#file1-preview').html($(this)[0].files[0].name);
            })

        </script>
    @endsection
@endsection