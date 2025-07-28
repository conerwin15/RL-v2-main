@extends('layouts.app')


@section('content')

<div id="pathAlert"></div> 

	
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>{{__('lang.show-learning_path')}}</h2>
            </div>

            <div class="pull-right" style="text-align: right; margin-right:10%">
              
              <button type="submit"  data-toggle="modal"  data-backdrop="static" data-keyboard="false" class="btn btn-success" data-target="#addCourse">
                {{__('lang.assign-new-course')}}
              </button>
               
            </div>
        </div>
    </div>

    <br/>
   
    <form class="form-inline" method="GET" style="margin-left: 70%">
       <div class="form-group">
        <label for="search" class="col-sm-2 col-form-label">{{__('lang.search')}}:</label> &nbsp;
        @if(isset($_GET['search']))
                <input type="text"  placeholder="{{__('lang.search-by-course')}}" class="form-controller" id="search" name="search" value="{{$_GET['search']}}">
        @else
                <input type="text"  placeholder="{{__('lang.search-by-course')}}" class="form-controller" id="search" name="search">
        @endif
       
       </div>
       <button type="submit" class="btn btn-primary btn-sm">{{__('lang.search')}}</button> 
               
    </form>

    <div class="row" style="margin-left: 1%">

        <a class="btn btn-primary btn-sm" href="{{route('learning-paths.show',$courses[0]->id)}}" active> {{__('lang.courses')}} </a> &nbsp;
        <a class="btn btn-primary btn-sm" href="#"> {{__('lang.learners')}}</a>

    </div>    
    <br>
    <table class="table table-bordered data-table">
        <thead>
            <tr>
                <th>{{__('lang.no')}}</th>
                <th>{{__('lang.course-name')}}</th>
                <th>{{__('lang.assign-by')}}</th>
                <th>{{__('lang.assign-on')}}</th>
                <th width="280px">{{__('lang.action')}}</th>
            </tr>
        </thead>   
        <tbody>

            @if(count($courses)>0)
             @foreach ($courses[0]->courses as $course)
                <tr>
                    <td>{{++ $index}}</td>
                    <td>{{$course->name}}</td>
                    <td>{{ucfirst($user->getNameById($users->pivot->created_by))}}</td>
                    <td>{{date('d M Y', strtotime($users->pivot->created_at))}}</td>
                    <td>
                    <div class="row" style="margin-left: 2%;">
                    <form action="{{route('courses.destroy',$course->id)}}" method="POST">
                      
                        @csrf
                        @method('DELETE')
                    
                         <button type="submit" class="btn btn-danger btn-sm" style="background-color: #e3342f;border-color: #e3342f;" onclick="return confirm('{{ __('lang.delete-confirmation-text') }}')"><!-- <img src="{{url('images/search_icon.png')}}"> -->{{__('lang.remove-from-path')}}
                         </button>
                      
                       
                    </form>
                    </div>
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

    <!-- Add Learning Path Modal -->
    <div class="modal fade" id="addCourse" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">{{__('lang.assign-new-course')}}</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
        <div class="modal-body">
        <strong>{{__('lang.assign-new-course')}}:</strong>
        <br/>    
        <div class="col-xs-12 col-sm-12 col-md-12">    
        <input type="text" value="" name="searchCourse" id="searchCourse" style="width: inherit;">
        </div> 
        
        <!-- form -->
        <form action="{{ route('courses.store') }}" method="POST" >
        @csrf
        <div class="row">
            <input type="hidden" name="learning_path_id" value="{{$courses[0]->id}}" id="learning_path_id" >
            <div class="col-xs-12 col-sm-12 col-md-12 ">
                <div class="form-group searchRecord">
                    @foreach ($newCourses as $newCourse)
                    <div class="row" style="margin-left: 5%;">
                     <input type='checkbox' name='assign_course[]' class='form-check-input' value="{{$newCourse->id}}"> &nbsp;
                       {{$newCourse->name}}  
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                    <button type="submit" class="btn btn-primary">{{__('lang.submit')}}</button>
            </div>
        </div>
        </form>
         
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">{{__('lang.cancel')}}</button>
        </div>
      </div>
      
    </div>
  </div>

@endsection