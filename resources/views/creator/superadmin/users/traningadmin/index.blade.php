@extends('layouts.app')

@section('content')

<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>{{__('lang.user-management')}}</h2>
        </div>


        <div class="pull-right">
            <a class="btn btn-success" href="{{ route('trainingadmins.create') }}">{{__('lang.create-training-admin')}}
            </a> &nbsp; &nbsp;

            <a class="btn btn-success" href="{{ route('dealers.create') }}">{{__('lang.create-dealer')}}
            </a> &nbsp; &nbsp;

            <a class="btn btn-success" href="{{ route('users.create') }}">{{__('lang.create-user')}}
            </a>


        </div>
       
    </div>
</div>
<br/>

<div class="row">
    <div class="row col-xs-8 col-sm8 col-md-8">
    <form class="form-inline" method="GET">
           <div class="form-group">
            <label for="search" class="col-sm-2 col-form-label">{{__('lang.search')}}:</label> &nbsp;
            @if(isset($_GET['search']))
                    <input type="text"  placeholder="{{__('lang.search')}}" class="form-controller" id="search" name="search" value="{{$_GET['search']}}">
            @else
                    <input type="text"  placeholder="{{__('lang.search')}}" class="form-controller" id="search" name="search">
            @endif
           
           </div>
         <button type="submit" class="btn btn-primary btn-sm">{{__('lang.search')}}</button> &nbsp;
         <button type="reset" class="btn btn-secondary btn-sm">{{__('lang.reset')}}</button>
                   
    </form>
    </div>

     <div class="row col-xs-4 col-sm-4 col-md-4" >
          <form class="form-inline" method="GET">
                <div class="form-group">
                      <strong>{{__('lang.select-role')}}:</strong> &nbsp;
                      <div>
                      <select  name="role"  required>
                        <option value="-1"> {{__('lang.all')}} </option>
                          @foreach ($roles as $role)
                        <option value="{{$role->name}}" id="roleId"  {{@$_GET['role'] == $role->name ? "selected" : ''}}>{{ $role->name }}</option>
                         @endforeach
                      </select>
                      </div>
                </div>   &nbsp;
                 <button type="submit" class="btn btn-primary btn-sm">{{__('lang.search')}}</button> &nbsp;
                 <button type="reset" class="btn btn-secondary btn-sm">{{__('lang.reset')}}</button>
            </form>   
        </div>
</div>



<table class="table table-bordered data-table" >
    <thead>
         <tr>
             <th>{{__('lang.no')}}</th>
             <th>{{__('lang.name')}}</th>
             <th>{{__('lang.email')}}</th>
             <th>{{__('lang.role')}}</th>
             <th width="280px">{{__('lang.action')}}</th>
         </tr>
    </thead>

     <tbody>

        @if(count($users)>0)
           @foreach ($users as $user)
            <tr>
                <td>{{++ $index}}</td>
                <td>{{$user->name}}</td>
                <td>{{$user->email}}</td>
                <td>{{$user->getRoleNames()->first()}}</td>
                 <td>

                    <form action="{{ route('trainingadmins.destroy',$user->id) }}" method="POST">
                        
                        <a class="edit btn btn-primary btn-sm" href="{{ route('trainingadmins.show',$user->id) }}">{{__('lang.show')}}</a>
                      
                        <a class="edit btn btn-success btn-sm" href="{{ route('trainingadmins.edit',$user->id) }}">{{__('lang.edit')}}</a>
            
                        @csrf
                        @method('DELETE')
                    
                         <button type="submit" onclick="return confirm('{{ __('lang.delete-confirmation-text') }}')" class="btn btn-danger btn-sm" style="background-color: #e3342f;border-color: #e3342f;">{{__('lang.delete')}}</button>
                       
                       
                    </form>
            </td>
            </tr>    
           @endforeach
        @else
           <tr>
              <td colspan="5" style="text-align: center;">{{__('lang.no-record')}} </td>

           </tr>

        @endif   

        </tbody>
</table>


 {!! $users->appends(request()->query())->links() !!}

@endsection