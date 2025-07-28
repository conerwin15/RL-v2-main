
@extends('layouts.app')

@section('content')

<div class="dash-title container-fluid">
    <b>{{__('lang.learning-paths')}}</b>
</div>

<div  class="white-wrapper">
        <div  class="table">

            <table class="table table-bordered data-table">
                <thead>
                    <tr>
                        <th>{{ __('lang.no') }}</th>
                        <th>image</th>
                        <th>{{ __('lang.learning-path') }}</th>
                        <th>{{ __('lang.assign-by') }}</th>
                        <th>Status</th>
                        <th>{{ __('lang.status') }}</th>
                    </tr>
                </thead>

                <tbody>
                        @foreach($userLearningPaths as $userLearningPath)
                            <tr>
                                <td>{{ ++$index }}</td>
                                <td><img src="{{asset('storage' . Config::get('constant.LEARNING_PATH_STORAGE').'/'.$userLearningPath->learningPath->featured_image)}}" ></td>
							
                                <td>{{ $userLearningPath->learningPath->name }}</td>
                                <td>{{ (ucfirst($userLearningPath->assignBy->name))}}</td>
                                <td>
                                @if($userLearningPath->progress_percentage == null)
                                        <label class="badge badge-warning">{{ __('lang.incomplete') }}</label>
                                    @else
                                        <label class="badge badge-success">{{ $userLearningPath->progress_percentage }}%
                                        {{ __('lang.completed') }}</label>
                                    @endif
                                </label>
                                </td>
                                <td>
                                    @if($userLearningPath->learningPath->status == 1)
                                       Active
                                    @else
                                      Inactive
                                    @endif
                                    
                                </td>
                            </tr> 
                        @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection