@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Scorm View') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <iframe id="iframe_a" name="iframe_a" height="500px" width="100%" title="Scorm Player"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')


<script type="text/javascript" src="{{ asset('js/scorm/init.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/scorm/constants.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/scorm/jsonFormatter.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/scorm/baseAPI.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/scorm/scormAPI.js') }}"></script>

<script type="text/javascript">
    $(document).ready(function(){
        document.getElementById("iframe_a").src = "{{ asset('scorm-course/BT/index_lms.html') }}";
    });

</script>

@endsection