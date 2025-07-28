
@extends('layouts.app')


@section('content')

<style>
    #iframe_show {
        width: 100%;
        height: 80vh;
    }
</style>

<strong>{{__('lang.show-course')}}:</strong>   
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12">
    <div class="form-group">
     <iframe id="iframe_show" name="iframe_show" height="200px" width="300px" title="Scorm Player"></iframe>
   </div>
   @if(count($scormItems) > 1)
     <button onclick="goPrev()">{{__('lang.previous')}}</button>
     <button onClick="goNext()">{{__('lang.next')}}</button>
   @endif
  </div>
</div>
<br>


@section('scripts')

<script type="text/javascript" src="{{ asset('js/jquery.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/scorm/init.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/scorm/constants.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/scorm/jsonFormatter.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/scorm/baseAPI.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/scorm/scormAPI.js') }}"></script>
<script type="text/javascript">
    let items = @json($scormItems);
  
    let currentIndex = 0;
    let totalItems = {{count($scormItems)}};
    
    $(document).ready(function() {
        setIframeURL(0);
    });

    function setIframeURL(index) {
      currentIndex = index;
      var path = "{{$scormPackage->package_saved_path}}";
      let basePath = "{{asset('/storage')}}" + '/' + path; 
      $("#iframe_show").attr('src', basePath + '/' + items[index].href + (items[index].parameters ? items[index].parameters : ''));
    }

    function goPrev() {
      if(currentIndex > 0) {
        setIframeURL(currentIndex - 1);
      }
    }

    function goNext() {
      if(currentIndex < totalItems) {
        setIframeURL(currentIndex + 1);
      }
    }

</script>
@endsection

@endsection

