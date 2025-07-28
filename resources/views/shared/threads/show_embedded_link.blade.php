@extends('layouts.app')


@section('content')

<style>
    #iframe_show {
        width: 100%;
        height: 80vh;
    }
</style>

<strong>{{__('lang.embedded_link')}}:</strong>
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12">
    <div class="form-group">
     <iframe id="iframe_show" name="iframe_show" height="200px" width="300px" title="Embedded Player" frameborder="0" allow="accelerometer; autoplay;" target="_blank"></iframe>
   </div>
  </div>
</div>
<br>



<script>
    $(document).ready(function() {
        $src= '{{$thread->embedded_link}}';
        $("#iframe_show").attr('src', $src);
    });
</script>
@endsection