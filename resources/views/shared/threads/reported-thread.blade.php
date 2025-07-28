@extends('layouts.app')

@section('content')
<div class="dash-title container-fluid max-width">
    <b>{{__('lang.threads')}} </b>
    <div class="flex">
        <form class="w-200 mr-2" method="GET" id="category-form">
            <select  name="category"  class="form-control select"  required>
                <option value="-1"> {{__('lang.all')}}</option>
                @foreach ($threadCategories as $threadCategory)
                    <option value="{{$threadCategory->id}}"  {{ @$_GET['category'] == $threadCategory->id ? 'selected' : '' }}>{{ $threadCategory->name }}</option>
                @endforeach
            </select>
        </form>
        <form class="d-lg-flex justify-content-end align-items-center" method="GET">
            @if(isset($_GET['search']))
                <input type="text" placeholder="{{ __('lang.search-by-name-placeholder') }}" class="form-control" id="search" name="search"
                    value="{{ $_GET['search'] }}">
            @else
                <input type="text" placeholder="{{ __('lang.search-by-name-placeholder') }}" class="form-control" id="search" name="search">
            @endif
            <button type="submit" class="btn-theme ml-2 mr-3">{{ __('lang.search') }}</button>

        </form>
    </div>
</div>

    


<div class="container-fluid max-width">
    @if(count($reportedThreads)>0) 
        @foreach ($reportedThreads as $reportedThread)
            <div  class="discussion" >
                <img src="{{ $reportedThread->thread->creator->image ? asset('storage' . Config::get('constant.PROFILE_PICTURES') . $reportedThread->thread->creator->image) : asset('assets/images/avatar_default.png') }}" class="usericon">
                <div style="width:100%">
                    <div class="flex justify-content-between"style="width:100%  ">
                        <p><b class="mr-2">{{$reportedThread->thread->creator->name}}</b>
                            {{ $reportedThread->thread->created_at->diffForHumans() }}<br>
                            <span class="text-gray">{{__('lang.category')}}: {{$reportedThread->thread->category_id ? $reportedThread->thread->category->name : 'N/A'}}</span>
                        </p>   
                    <div class="flex align-items-center">

                    @if($reportedThread->thread->is_hidden == true)
                        <form action="{{ url('superadmin/thread/update') }}" method="POST" class="mb-0 mr-2">
                            @csrf
                            <input type="hidden" name="thread_id" value="{{$reportedThread->thread->id}}">
                            <input type="hidden" name="type" value="unhide">
                            <button type='submit' class='nobtn color'><i class="fa fa-eye" aria-hidden="true"></i> {{__('lang.unhide')}}</button>
                        </form>

                    @else
                        <form action="{{ url('superadmin/thread/update') }}" method="POST" class="mb-0 mr-2">
                            @csrf
                            <input type="hidden" name="thread_id" value="{{$reportedThread->thread->id}}">
                            <input type="hidden" name="type" value="hide">
                            <button type='submit' class='nobtn color'><i class="fa fa-eye" aria-hidden="true"></i> {{__('lang.hide-thread')}}</button>
                        </form>

                    @endif

                        <form action="{{ url('superadmin/thread/delete' ) }}" method="POST"  class="mb-0">
                            @csrf
                            <input type="hidden" name="thread_id" value="{{$reportedThread->thread->id}}">
                            <button type="submit" class="text-danger nobtn ml-2 mr-2" style="border: 0px;" ><i class="fa fa-trash" aria-hidden="true"></i></button>
                        </form> 
                    </div>
                    </div>
                        {{$reportedThread->thread->title}} <br>
                        <span class="text-dim">
                        {{$reportedThread->thread->body}}
                        </span>
                    </p>
                </div>

            </div>

        @endforeach
    @else
            <tr>
                <td colspan="6" style="text-align: center;">{{ __('lang.no-record') }} </td>
            </tr>

    @endif    

{{ $reportedThreads->appends(request()->query())->links('vendor.pagination.bootstrap-4') }}
</div>
<script>
    $('select').on('change' , function(){
        $('#search-thread').submit()
    })
    $('#category-form select').on('change' , function(){
        $('#category-form').submit()
    })
</script>
@endsection
