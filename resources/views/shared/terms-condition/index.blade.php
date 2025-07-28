@extends('layouts.app')

@section('content')
</main>
<div class="t-n-c">
    <div>
        <form action="{{ url($role . '/terms-conditions') }}" method="POST">
                @csrf
                <input type="checkbox" id="chkterms" /> {{ __('lang.i-have') }} <a href="https://www.piaggiogroup.com/en/privacy" target="_blank">{{ __('lang.read') }}</a> {{ __('lang.term-condition-message') }}
            <div class="mt-3 mb-0">
                <button class="btn-theme-border mb-0" type="submit" disabled="true" id="accept-btn"> {{ __('lang.accept') }}</button>
            </div>        
        </form>
    </div>
</div>



<script>
    $('#chkterms').change(function () {
        if (this.checked) {
            $('button[type="submit"]').prop('disabled', false);
        } else {
            $('button[type="submit"]').prop('disabled', true);
        }
    });

</script>
@endsection
