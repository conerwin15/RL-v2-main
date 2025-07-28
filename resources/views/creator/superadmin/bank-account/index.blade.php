@extends('layouts.app')

@if(Session::has('flash_success'))
    <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert">×</button>
        {{ Session::get('flash_success') }}
    </div>
@endif

@section('content')
    <h4 class="thetitle">Bank Accounts</h4>

    <div>
        <div class="flex-title">
            <div class="iconset">
                <span>
                    <i>
                        <svg viewBox="0 0 352.504 424.981">
                            <g id="money" transform="translate(-36.238 0)">
                                <path id="Path_3277" data-name="Path 3277"
                                    d="M241.4,282.3a20.2,20.2,0,0,0-5.67-4.121,68.419,68.419,0,0,0-14.365-5.075v38.016c7.963-.9,17.1-3.79,21.286-11.224h0a17.369,17.369,0,0,0,1.58-11.867A11.938,11.938,0,0,0,241.4,282.3Z" />
                                <path id="Path_3278" data-name="Path 3278" d="M242.6,299.973l.042-.073h0Z" />
                                <path id="Path_3279" data-name="Path 3279"
                                    d="M184.009,221.532a14.257,14.257,0,0,0-2.465,6.684,16.027,16.027,0,0,0,.815,7.387,11.851,11.851,0,0,0,4.6,5.062,35.929,35.929,0,0,0,6.836,3.528c1.995.8,4.239,1.571,6.658,2.313v-34.4C194.342,213.41,187.665,216.194,184.009,221.532Z" />
                                <path id="Path_3280" data-name="Path 3280"
                                    d="M242.8,299.619c-.05.089-.1.182-.157.28h0C242.709,299.785,242.758,299.7,242.8,299.619Z" />
                                <path id="Path_3281" data-name="Path 3281" d="M243,299.263c.013-.024.015-.026,0,0Z" />
                                <path id="Path_3282" data-name="Path 3282"
                                    d="M234.753,92.469C267.082,65.311,288.684,4.128,275.39,1.452,257.726-2.1,219.368,13.492,200.828,16.24c-26.3,3.175-54.936-28.515-71.012-10.851-13.071,14.362,9.371,66.592,44.482,89.346C69.546,146.219-77.69,404.673,179.171,423.426,534.582,449.375,356.615,142.639,234.753,92.469ZM265.276,296.3a37.366,37.366,0,0,1-14.415,25.374c-8.428,6.532-19,9.57-29.5,10.421v11.133a10.453,10.453,0,1,1-20.906,0V331.058c-1.8-.342-3.589-.749-5.356-1.234a51.318,51.318,0,0,1-25.572-15.732,46.094,46.094,0,0,1-7.882-13.025c-.488-1.241-.923-2.505-1.3-3.783a19.4,19.4,0,0,1-.824-3.539,10.453,10.453,0,0,1,20.322-4.339c.4,1.2.668,2.44,1.115,3.632a23.292,23.292,0,0,0,1.607,3.431,26.647,26.647,0,0,0,4.59,5.875,31.117,31.117,0,0,0,13.3,7.248V268.23c-9.591-2.483-19.491-5.69-27.411-11.848a31.892,31.892,0,0,1-9.254-11.117,34.8,34.8,0,0,1-3.23-14.966,35.659,35.659,0,0,1,3.131-15.153,36.944,36.944,0,0,1,8.578-11.768c7.7-7.087,17.928-11.04,28.187-12.492V179.329a10.453,10.453,0,1,1,20.906,0v11.494q2.051.258,4.086.624c10.074,1.823,19.927,5.983,27.294,13.246A39.888,39.888,0,0,1,261.1,216.7a38.3,38.3,0,0,1,1.477,3.824,23.229,23.229,0,0,1,.983,3.728,10.454,10.454,0,0,1-20.1,5.285c-.438-1.142-.657-2.351-1.1-3.49a18.42,18.42,0,0,0-1.708-3.292,19.94,19.94,0,0,0-4.938-5.179c-4.19-3.094-9.272-4.706-14.35-5.607v39.582a132.725,132.725,0,0,1,17.857,5.3c8.739,3.446,17.02,8.73,21.79,17.062-.74-1.3-1.46-2.563.025.043,1.458,2.56.762,1.34.03.057A37.29,37.29,0,0,1,265.276,296.3Z" />
                                <path id="Path_3283" data-name="Path 3283"
                                    d="M242.493,300.169c-.061.109-.114.2-.156.278C242.373,300.384,242.427,300.289,242.493,300.169Z" />
                            </g>
                        </svg>
                    </i>
                </span>
                @if(count($bankAccounts) > 0)
                    <a href="{{ route('superadmin.bank-account.create') }}" class="btn btn-primary disabled" id="add-bank-account">{{__('lang.add-new-bank-account')}}
                    </a>
                @else
                    <a href="{{ route('superadmin.bank-account.create') }}" class="btn btn-primary" id="add-bank-account">{{__('lang.add-new-bank-account')}}
                    </a>
                @endif
            </div>

            <div>

            </div>
        </div>

        <hr>
        <div class="card-wrap">
            @if (count($bankAccounts) > 0)
                @foreach ($bankAccounts as $bankAccount)
                    <div class="c-card">
                        <div class="iconset">
                            <span>
                                <i>
                                    <svg id="Layer_1" enable-background="new 0 0 512 512" viewBox="0 0 512 512">
                                        <g>
                                            <path
                                                d="m512 163v-27c0-30.928-25.072-56-56-56h-400c-30.928 0-56 25.072-56 56v27c0 2.761 2.239 5 5 5h502c2.761 0 5-2.239 5-5z" />
                                            <path
                                                d="m0 205v171c0 30.928 25.072 56 56 56h400c30.928 0 56-25.072 56-56v-171c0-2.761-2.239-5-5-5h-502c-2.761 0-5 2.239-5 5zm128 131c0 8.836-7.164 16-16 16h-16c-8.836 0-16-7.164-16-16v-16c0-8.836 7.164-16 16-16h16c8.836 0 16 7.164 16 16z" />
                                        </g>
                                    </svg>
                                </i>
                            </span>
                            <span>
                            {{__('lang.bank-name')}} <br>
                                <b><small style="font-size:16px">{{ $bankAccount->name }}</small> </b> <br>
                                @if (count($bankAccounts) > 1 && $bankAccount->primary)
                                    <small style="font-size:16px">(primary)</small>
                                @endif
                            </span>
                        </div>
                        <p>
                            <span>{{__('lang.account-last-four')}}</span> <br>
                            **** **** {{ $bankAccount->last_four }}
                        </p>
                        <div style="display: flex;">
                            <form action="{{ route('superadmin.bank-account.update', $bankAccount->id) }}" method="POST">
                                {{ csrf_field() }}
                                <input type="hidden" name="_method" value="PATCH">
                                @if (!$bankAccount->primary)
                                    <button onclick="return confirm('Are you sure?')" type="submit"
                                        class="btn btn-primary">
                                        Set as primary
                                    </button>
                                @endif
                            </form>

                            <form action="{{ route('superadmin.bank-account.destroy', $bankAccount->id) }}" method="POST">
                                {{ csrf_field() }}
                                <input type="hidden" name="_method" value="DELETE">
                                <button onclick="return confirm('Are you sure?')" type="submit" class="btn btn-danger"
                                    style="background-color: red;">{{__('lang.delete')}}</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="c-card">
                    <p> {{__('lang.bank-not-added-yet')}}</p>
                </div>
            @endif
            

        </div>
    </div>
@endsection

{{-- @section('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script type="text/javascript">
        $('#add-bank-account').on('click', function() {

            var name = '{{ $user->first_name }}' + ' ' + '{{ $user->last_name }}';
            var stripe = Stripe('{{ env('STRIPE_KEY') }}');

            stripe.collectBankAccountForSetup({
                    clientSecret: '{{ $token }}',
                    params: {
                        payment_method_type: 'us_bank_account',
                        payment_method_data: {
                            billing_details: {
                                name: name,
                                email: '{{ $user->email }}'
                            },
                        },
                    },
                })
                .then(({
                    setupIntent,
                    error
                }) => {
                    if (error) {
                        console.error(error.message);
                    } else if (setupIntent.status === 'requires_confirmation') {
                        stripe.confirmUsBankAccountSetup('{{ $token }}')
                            .then(({
                                setupIntent,
                                error
                            }) => {
                                $.ajax({
                                    url: "{{ url('/superadmin/bank-account') }}",
                                    type: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    data: setupIntent,
                                    cache: false,
                                    success: function(data) {
                                        alert(data.message);
                                        return data;
                                    },
                                    error: function(err) {
                                        console.error(err);
                                    }
                                });
                            });
                    }
                });
        });
    </script>
@endsection --}}
<style>
    .iconset {
    display: inline-flex;
    align-items: center;
    min-width: 180px;
    margin-bottom: 20px;
    position: relative;
    z-index: 1
}

.iconset i {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 42px;
    height: 42px;
    background: rgba(87, 146, 246, .3);
    border-radius: 100%;
    position: relative;
    margin-right: 10px
}

.iconset i:after {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: #5792f6;
    border-radius: 100%;
    z-index: -1;
    transition: all 200ms linear;
    transform: scale(0.7)
}

.iconset i svg {
    width: 16px
}

.iconset i svg path {
    fill: #fff
}

.iconset b {
    font-size: 20px
}

.card-wrap {
    display: flex;
    flex-wrap: wrap
}

.card-wrap .c-card {
    background: #fff;
    position: relative;
    z-index: 1;
    padding: 16px 16px;
    margin: 1%;
    border-radius: 4px;
    width: 31%;
    box-shadow: 0 0 10px rgba(0, 0, 0, .1)
}

.thetitle {
    margin-top: -42px;
    margin-bottom: 30px;
    font-weight: 600;
    font-size: 18px
}

.flex-title {
    display: flex;
    align-items: center;
    justify-content: space-between
}

.disabled {
    opacity: 0.5; /* Adjust the opacity to visually indicate the disabled state */
    pointer-events: none; /* Prevent interactions */
    cursor: not-allowed; /* Change cursor to indicate non-interactivity */
}
</style>