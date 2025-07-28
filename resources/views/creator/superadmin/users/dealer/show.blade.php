
@extends('layouts.app')


@section('content')

</main>
<div class="white-wrapper  profile-wrap wrapper2">
    <div class="container-fluid max-width">
        <h5>{{ __('lang.show-dealer') }}</h5>
    </div>
    <div class="container-fluid max-width">
            <div class="pic-wrapper">
               
                @php $image = $user->image == '' ? asset('assets/images/avatar_default.png') :  asset('storage' . Config::get('constant.PROFILE_PICTURES') . $user->image);  @endphp
                <img src="{{$image}}" class="profile-pic" id="output">
            </div>
            <div class="ml-3">
                <p><b>{{ $user->name }} </b> <span class="btn-theme btn-sm ">{{ucfirst( toRoleLabel($user->roles[0]->name ))}}</span> <br>
                    <small class="text-gray">
                    {{ __('lang.joined-on') }} {{ $user->created_at->format('M Y')}} 
                        
                    </small>
                </p>
            </div>
        </div>
</div>  



<div class="wrapper2">
    <div class="container-fluid max-width">
        <p class="color">{{__('lang.general-information')}}</p>
    </div>
</div>

<div class="white-wrapper wrapper2">

    <div class="container-fluid max-width user-info">

        <div class="row">
            <div class="col-sm-6">
                <div class="d-flex">
                    <label>{{ __('lang.name') }}:</label>
                    <div class="user-label">{{ $user->name }}</div>
                </div>
               
                <div class="d-flex">
                    <label>{{ __('lang.country') }}:</label>
                    <div class="user-label">{{ $user->country->name }}</div>
                </div>
                <div class="d-flex">
                    <label>{{ __('lang.region') }}:</label>
                    <div class="user-label">{{ $user->region ? $user->region->name : 'N/A' }}</div>
                </div>

                <div class="d-flex">
                    <label>{{ __('lang.group') }}:</label>
                    <div class="user-label">
                    @if(isset($user->group ))
                                {{$user->group->name}}
                            @else
                            N/A
                            @endif
                    </div>
                </div>

                <div class="d-flex">
                    <label>{{ __('lang.remarks') }}:</label>
                    <div class="user-label">{{ $user->remarks ? $user->remarks : 'N/A' }}</div>
                </div>
                
            </div>
        
            <div class="col-sm-6">
                <div class="d-flex">
                    <label>{{ __('lang.email') }}:</label>
                    <div class="user-label">{{ $user->email }}</div>
                </div>

                <div class="d-flex">
                    <label>{{ __('lang.job-role') }}:</label>
                    <div class="user-label">
                        
                    @if(isset($user->jobRole->name ))
                                {{$user->jobRole->name}}
                                @else
                                N/A
                            @endif
                    </div>
                </div>

                <div class="d-flex">
                    <label>{{ __('lang.role') }}:</label>
                    <div class="user-label">{{ toRoleLabel($user->roles[0]->name) }}</div>
                </div>

                <div class="d-flex">
                    <label>{{__('lang.created-on')}}:</label>
                    <div class="user-label">{{ date('d M Y', strtotime($user->created_at)) }}</div>
                </div>
                
            </div>
        </div>
    </div>
</div>


@if(count($user->learningPaths))
<div class="white-wrapper wrapper2">

        <div class="container-fluid max-width">
        <div class="">
            <h5>{{__('lang.assigned-learning-path-title')}}<h5>
        </div>
            <div class="row">

            </div>
            @foreach($user->userLearningPaths as $userLearningPath)
            <div class="row">
                <div class="col-md-6">
                    <img src="{{asset('storage' . Config::get('constant.LEARNING_PATH_STORAGE').'/'.$userLearningPath->learningPath->featured_image) }}" style="max-height:50px; max-width:100%;">
                    {{ ucfirst($userLearningPath->learningPath->name) }}
                </div>
                <div class="col-md-3">
                    @if($userLearningPath->progress_percentage == null)
                        <label class="badge badge-warning">{{__('lang.incomplete')}}</label>
                    @else
                        <label class="badge badge-success">{{ $userLearningPath->progress_percentage }}%
                        {{__('lang.completed')}}</label>
                    @endif
                    &nbsp;
                    <a href="{{ url('/superadmin/learning-paths/resource/'.$user->id.'/'.$userLearningPath->learningPath->id) }}">  <button type="submit" class="btn-theme btn-sm noDecoration"> {{ __('lang.progress-in-detail') }} </button>  </a>
                </div>
                @if($userLearningPath->badge != null)
                    <div class="col-md-3">
                        <span class="btn-badge">
                            <img src="{{ asset('assets/images/' . $userLearningPath->badge->image) }}"
                                alt="">
                            {{ ucfirst($userLearningPath->badge->name) }}</span>
                    </div>
                @endif
            </div>
            <hr>
@endforeach

@else
            <div class="row">
            {{__('lang.no-learning-path')}}
            </div>
</div>
        @endif
@endsection