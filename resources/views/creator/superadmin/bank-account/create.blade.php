@extends('layouts.app')

@section('title', 'Bank Account')

@section('content')
    <div class="container-fluid">
        <h3 class="mt-3 table-title">{{ __('lang.add-bank-account') }}</h3>
        <hr>
        <div class="">
            <form class="form-horizontal row" action="{{ route('superadmin.bank-account.store') }}" method="POST"
                enctype="multipart/form-data" role="form">
                {{ csrf_field() }}
                <div class="col-sm-8">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class=" mt-1">
                                <label for="routing-number">{{ __('lang.routing-number') }}</label>
                                <input class="form-control" type="text" id="routing-number" value="" required
                                    name="routing_number" />
                            </div>
                            <div class="mt-1">
                                <label for="account-number">{{ __('lang.account-number') }}</label>
                                <input class="form-control" type="text" id="account-number" value="" required
                                    name="account_number" />
                            </div>
                            <div class="mt-1">
                                <label for="account-holder-name">{{ __('lang.account-holder-name') }}</label>
                                <input class="form-control" type="text" id="account-holder-name" value="" required
                                    name="account_holder_name" />
                            </div>
                            <div class="col-xs-10 mb-2 mt-2">
                                <button type="submit"    class="btn btn-primary">{{ __('lang.submit') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('scripts')

@endsection
