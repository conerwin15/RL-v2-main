@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="dash-title container-fluid d-flex justify-content-between align-items-center flex-wrap">
            <b>{{ __('lang.faqs') }}</b>

            <form id="faq-search-form" class="d-lg-flex col-sm-10 justify-content-end align-items-center" method="GET">
                <div class="col-sm-4 mb-2 mb-lg-0">
                    <select name="category" class="form-control select mb-0">
                        <option value="" disabled selected>{{ __('lang.select-category') }}</option>
                        <option value="-1" {{ request('category') == -1 ? 'selected' : '' }}>{{ __('lang.all') }}</option>
                        @foreach ($faqCategories as $faqCategory)
                            <option value="{{ $faqCategory->id }}" {{ request('category') == $faqCategory->id ? 'selected' : '' }}>
                                {{ ucfirst($faqCategory->faq_category) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <input type="text" name="search" id="search" class="form-control ml-lg-2 mb-2 mb-lg-0"
                       value="{{ request('search') }}" placeholder="{{ __('lang.search-by-name-placeholder') }}">

                <button type="submit" class="btn-theme ml-2">{{ __('lang.search') }}</button>
            </form>
        </div>
    </div>
</div>

<br/>

<div class="container-fluid">
    <div class="white-wrapper">
        <div class="table mt-4">
            <h6 class="col-3"><b>{{ __('lang.faqs') }}</b></h6>
            <table class="data-table table display">
                <thead>
                    <tr>
                        <th>{{ __('lang.no') }}</th>
                        <th>{{ __('lang.question') }}</th>
                        <th>{{ __('lang.answer') }}</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        const table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('shared/faqs') }}", // ✅ use actual route if routeSlug is not passed
                data: function (d) {
                    d.search = $('#search').val();
                    d.category = $('select[name="category"]').val();
                }
            },
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                { data: 'question', name: 'question' },
                { data: 'answer', name: 'answer' },
            ],
            searching: false,
            bLengthChange: false,
            order: [[1, 'asc']],
        });

        // ✅ Reload DataTable on search form submit
        $('#faq-search-form').on('submit', function (e) {
            e.preventDefault();
            table.ajax.reload();
        });
    });
</script>
@endsection
