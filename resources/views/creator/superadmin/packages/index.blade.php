@extends('layouts.app')

@section('content')
<div class="piaggio-alert">
    <div id="publishAlert"></div>
</div>
<div class="dash-title container-fluid no-flex">
    <b>{{ __('lang.packages') }}</b>
    <div class="d-lg-flex align-items-center justify-content-end no-flex">
        <form class="d-lg-flex mb-0 justify-content-end align-items-center" method="GET">
            @if(isset($_GET['name']))
            <input type="text" placeholder="{{__('lang.search-by-package-name')}}"
                class="form-controller form-control mb-0" id="search" name="name" value="{{$_GET['name']}}">
            @else
            <input type="text" placeholder="{{__('lang.search-by-package-name')}}"
                class="form-controller form-control mb-0" id="search" name="name">
            @endif
            <button type="submit" class="ml-2 btn-theme">{{__('lang.search')}}</button>
        </form>
        <a class="btn-theme ml-2" href="{{ url('superadmin/packages/create') }}">+ {{__('lang.create-package')}}</a>
    </div>
</div>
<br />
<form class="container-fluid max-width" method="GET">
    <div class="white-wrapper pb-4">
        <div class="row align-items-end">
            <div class="col-sm-12">
                <h6><b>{{__('lang.filter')}}</b></h6>
            </div>
            <div class="col-sm-2">
                <label>{{__('lang.select-category')}}: </label>
                <select name="filter_category" id="category" class="form-control select">
                    <option disabled selected> {{ __('lang.select') }} {{ __('lang.category') }} </option>
                    @foreach ($categories as $category)
                    <option value="{{$category->id}}" {{ @$_GET['filter_category']==$category->id ? 'selected' : ''
                        }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-sm-2">
                <label>{{ __('lang.sub-category') }}:</label>
                <select name="filter_sub_category" id="sub_category" class="select form-control" required>
                    <option disabled selected> {{ __('lang.select') }} {{ __('lang.sub-category') }} </option>
                    @foreach($subCategories as $subCategory)
                    <option value="{{ $subCategory->id }}" id="jobRoleId" {{
                        @$_GET['filter_sub_category']==$subCategory->id ? "selected" : '' }}>
                        {{ $subCategory->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col">
                <label for=""><br></label><br>
                <button type="submit" class="btn-theme">{{ __('lang.apply-filter') }}</button>
            </div>
        </div>
    </div>
</form>
<div class="container-fluid">
    <div class="white-wrapper">
        <div class="table">
            <div class="data-table-container">
                <table class="data-table">
                    <thead>
                        <tr>
							<th>{{__('lang.image')}}</th>
                            <th>{{__('lang.id')}}</th>
                            <th>{{__('lang.name')}}</th>
                            <th>{{__('lang.description')}}</th>
                            <th>{{__('lang.category')}}</th>
                            <th>{{__('lang.sub-category')}}</th>
                            <th>{{__('lang.price')}}</th>
                            <th>{{__('lang.discount-price')}}</th>
                            <th>{{__('lang.no-of-learning-path')}}</th>
                            <th>{{__('lang.created-on')}}</th>
                            <th>{{__('lang.published-unpublished-on')}}</th>
                            <th width="374px" style="text-align:center; mx-0">{{__('lang.action')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $('#category').change(function () {
        var Id = $('#category').val();
        var ajaxurl = app_url + "/superadmin/category/" + Id + "/sub-category";

        $.ajax({
            type: 'get',
            url: ajaxurl,
            success: function (data) {
                $("#sub_category").empty();
                console.log(data.length);
                if (data.length > 0) {
                    $.each(data, function (key, value) {
                        $('#sub_category').append(`<option value=" ${value.id}" >${value.name}</option>`)
                    });
                } else {
                    $('#sub_category').append(`<option value="" selected>No Record Found</option>`)
                }
            },
            error: function (data) {
            }
        });
    });

    $(document).ready(function () {

        var ajaxUrl = "{{url('superadmin/packages')}}" + window.location.search;
        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            cache: false,
            processData: false,
            ajax: ajaxUrl,
            columns: [

                {
                    data: 'image',
                    "render": function (data) {
                        return "<img src='" + data + "'>";
                    },
                    orderable: false
                },

            {
                data: 'unique_ID',
                name: 'unique_ID',
            },
            {
                data: 'name',
                name: 'name'
            },
            { 'data': 'description' ,
                "render": function (data)
                    {
                    return ` ${data.substring(0,20)}`;
                    }
                },
                {
                    data: 'category',
                    name: 'category'
                },
                {
                    data: 'sub_category',
                    name: 'sub_category'
                },
                {
                    data: 'price',
                    price_data: 'price',
                    "render": function (data) {
                        return '$' + data.toFixed(2);
                    }
                },
                {
                    data: 'discount_price',
                    render: function (data) {
                        return '$' + data.toFixed(2);
                    }
                },
                {
                    data: 'no_of_learning_paths',
                    name: 'no_of_learning_paths'
                },
                {
                    data: 'created_on',
                    name: 'created_on'
                },
                {
                    data: 'published_unpublished_on',
                    name: 'published_unpublished_on',
                    render: function (data) {
                        return '<div style="padding-top:8px;">' + data + '</div>';
                    }
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ],
            "searching": false,
            "bLengthChange": false,
            'order': [1, 'asc'],
            "createdRow": function (row) {
                $(row).find('td:eq(10)').addClass('flex');
                $(row).find('td:eq(2)').addClass('quiz-text');
            }
        });
    });
</script>
<style>
    #search {
    max-width: 280px;
    min-width:200px;
}
.white-wrapper .table table tr td:last-child {
    text-align: center !important;
    padding-top:15px;
}
</style>
@endsection