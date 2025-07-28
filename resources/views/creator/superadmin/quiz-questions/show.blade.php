@extends('layouts.app')


@section('content')
<div class="dash-title container-fluid mt-2">
    <div>
        <a href="{{ url('/superadmin/quizzes') }}"><b>{{ __('lang.quiz') }} &gt;</b></a>
        <span class="bradcrumb">{{ __('lang.manage-questions') }}</span>
    </div>
</div>

<div class="container-fluid">
		<div class="white-wrapper">
			<form class="d-lg-flex justify-content-end align-items-center" method="GET" >

				@if(isset($_GET['search']))
					<input type="text"  placeholder="{{__('lang.search-by-name-placeholder')}}" class="form-controller form-control mb-0"  id="search" name="search" value="{{$_GET['search']}}">
				@else
					<input type="text"  placeholder="{{__('lang.search-by-name-placeholder')}}" class="form-controller form-control mb-0" id="search" name="search">
				@endif
				<button type="submit" class="ml-2 btn-theme">{{__('lang.search')}}</button>
			</form>

			<div class="table">
				<table>
					<thead>
						<tr>
							<th>{{__('lang.no')}}</th>
							<th>{{__('lang.title')}}</th>
							<th width="280px">{{__('lang.action')}}</th>
						</tr>
					</thead>
					<tbody>
					@if(count($questions)>0)
                        @foreach ($questions as $question)
                            <tr>
                                <td>{{ ++ $index}}</td>
                                <td>{{ucfirst($question->question_text)}}</td>
                                <td>
                                    <a href="{{ url($routeSlug .'/learning-paths/' . $learningPath->id . '/edit') }}">
                                            <i class="fa fa-pencil" aria-hidden="true"></i> {{__('lang.edit')}}
                                    </a>
                                    <button type="submit" class="text-danger "  data-href="{{ url($routeSlug .'/learning-paths/' . $learningPath->id) }}" data-id="{{$learningPath->id}}" data-route="{{$routeSlug}}">
                                        <i class="fa fa-trash" aria-hidden="true"></i> {{__('lang.delete')}}
                                    </button>
                                </td>

                            </tr>
                        @endforeach
                    @else
                    <tr>
                        <td colspan="7" style="text-align: center;">{{__('lang.no-record')}} </td>
                    </tr>

                    @endif
					</tbody>
				</table>
			</div>
		</div>
@endsection
