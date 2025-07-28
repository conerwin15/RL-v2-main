@extends('layouts.app')

@section('content')

<head>
    
    <div class="dash-title container-fluid mt-3">
        <div>
            <a href="{{ url('/') }}"><b>{{ __('lang.packages') }} &gt;</b></a>
            <span class="bradcrumb">Checkout</span>
        </div>
    </div>
     <div>
        <div class="row mt-2">
            <div class="col-9">
                <div class="row">
                    <div class="card ml-4" style="width:95%; height:59px;">
                        <div class="card ml-5 mt-3" style="width:90%; border:none; background:none">
                            <div class="row">
                                <div class="col-1"><input class="form-check-input" type="checkbox" value="" id="selectAllCheckbox"></div>
                                <div class="col-6">
                                    <span id="selectAllCheckboxText">Select All Items</span>({{ count($cartPackages) }} items)
                                </div>
                                <div class="col-6 col-sm-3">
                                <form action="{{ url('/public/delete-all-cart-item')}}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class ="custom-deleteAll-button" type="submit" style="background: white; border:none; color:#0b99d1;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z" />
                                                <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z" />
                                            </svg> DELETE ALL PACKAGES
                                        </button>
                                
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="card ml-4" style="width:95%; height:600px;">
                        @foreach($cartPackages as $cartPackage)
                        <div class="card ml-5 mt-5" style="width:90%; height:60px; border:none;">
                            <div class="row">
                                <div>
                                    <input type="checkbox" class="cart-checkbox" data-price="{{ $cartPackage->discount_price }}" data-id="{{ $cartPackage->id }}" />
                                    <span class="price"></span>
                                </div>
                                <div class="col-5 ">
                                    <h5><b>{{ $cartPackage->name }}</b></h5>
                                </div>
                                <div class="col-3 ">
                                    <h5 style="color:darkorange;"><b>${{ $cartPackage->discount_price }}</b></h5>
                                </div>
                                <div class="col-3 ">
                                    <form action="{{ url('/public/delete-cart-item/'.$cartPackage->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button  type="submit" style="background: white; border:none; color:#0b99d1;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z" />
                                                <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z" />
                                            </svg> DELETE
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-3">
                <div class="row order-summary">
                    <div class="card" style="width:100%; height:350px;">
                        <div class="card-body">
                            <h5 class="card-title">Order Summary</h5>

                            <div class="row total-item-and-price">
                                <div class="col-7">Subtotal (<span class="counts">0</span> items)</div>
                                <div class="col-3 total" >0.00</div>
                             
                            </div>
                            <br>
                            <br>
                            <br>
                            <div class="row total-price">
                                <div class="col-7">Total:</div>
                                <div class="col-3 total" >0.00</div>
                             
                            </div>
                            <div>
                                <form action="{{ url('/customer/login') }}" method="POST">
                                @csrf
                            <input type="hidden" id="packageIds" name="packageIds" />
                                <button class="btn custom-checkout-button" type="submit">PROCEED TO CHECKOUT</button>
                            </div>   
                        </div>
                    </div>
                </div>

                <!-- <div class="row mt-3">
                <div class="card mx-auto" style="width:77%; height:130px;">
                    <a href="" class="email-button"><button>
                            Sign up with Email
                        </button></a>

                    <a href="" class="google-button"><button>
                            <h5><i class="fab fa-google-plus-g"> </i>&nbsp;&nbsp;Google</h5>
                        </button></a>
                </div>
            </div> -->
            </div>
        </div>
    </div>

    @section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            function updateSubtotal() {
                let subtotal = 0;
                var count = 0;
                let packageIds = [];

                $('.cart-checkbox:checked').each(function() {
                    var price = parseFloat($(this).data('price'));
                    subtotal += price;
                    count++;
                    // var id = parseInt($(this).data('price'));
                    packageIds.push($(this).data('id'))
                });
                $('.total').text('$' + subtotal.toFixed(2));
                $('.counts').text(count);
                $('#packageIds').val(packageIds);


                // Update the button text based on the count
                var checkoutButton = $('.custom-checkout-button');
                var deleteAllButton = $('.custom-deleteAll-button');
                if (count > 0) {
                    checkoutButton.text('PROCEED TO CHECKOUT');
                    checkoutButton.prop('disabled', false);
                    deleteAllButton.prop('disabled', false);
                } else {
                    checkoutButton.text('SELECT PACKAGE TO CHECKOUT');
                    checkoutButton.prop('disabled', true);
                    deleteAllButton.prop('disabled', true);
                }
            }

            // Handle "Select All" checkbox
            $('#selectAllCheckbox').change(function() {
                let isChecked = $(this).prop('checked');
                $('.cart-checkbox').prop('checked', isChecked);
                updateSubtotal();

                // Update the text of the "Select All" checkbox
                let selectAllText = isChecked ? 'Unselect All Items' : 'Select All Items';
                $('#selectAllCheckboxText').text(selectAllText);
            });

            // Handle individual checkbox changes
            $('.cart-checkbox').change(function() {
                updateSubtotal();
            });

            updateSubtotal();
        });
    </script>

<style>
    .custom-checkout-button {
        margin-top: 3rem;
        width: 97%;
        background-color: darkorange;
        color: white;
    }

    .progess-checkbox {
        margin-left: 1rem;
        margin-bottom: 2rem;
        ;
    }
    .total-item-and-price span {
        /* margin-right: 8rem;  */
    }
    .total-price span {
        margin-right: 8rem;
    }
    </style>

    @endsection

    @endsection