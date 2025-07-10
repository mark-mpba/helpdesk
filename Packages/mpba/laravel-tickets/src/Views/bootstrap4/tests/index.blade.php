@extends('ticket::layouts.master')

@section('page', trans('ticket::lang.index-title'))
@section('page_title', trans('ticket::lang.index-my-tickets'))


@section('ticket_header')
    {!! link_to_route('test.create','Create a New Test', null, ['class' => 'btn btn-primary']) !!}
@stop

@section('ticket_content_parent_class', 'pl-0 pr-0')

@section('ticket_content')
    <div id="message"></div>
    @include('ticket::tests.partials.datatable')
@stop
{{-- Tests Index --}}
@section('footer')
    <script
        src="https://cdn.datatables.net/v/bs4/dt-{{ mpba\Tickets\Helpers\Cdn::DataTables }}/r-{{ mpba\Tickets\Helpers\Cdn::DataTablesResponsive }}/datatables.min.js"></script>
    <script>
        $('.table').DataTable({
            processing: false,
            serverSide: true,
            responsive: true,
            pageLength: {{ $setting->grab('paginate_items') }},
            lengthMenu: {{ json_encode($setting->grab('length_menu')) }},
            ajax: {
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                "url": '{!! route('tests.index') !!}',
                "type": "GET",
            },
            language: {
                decimal: "{{ trans('ticket::lang.table-decimal') }}",
                emptyTable: "{{ trans('ticket::lang.table-empty') }}",
                info: "{{ trans('ticket::lang.table-info') }}",
                infoEmpty: "{{ trans('ticket::lang.table-info-empty') }}",
                infoFiltered: "{{ trans('ticket::lang.table-info-filtered') }}",
                infoPostFix: "{{ trans('ticket::lang.table-info-postfix') }}",
                thousands: "{{ trans('ticket::lang.table-thousands') }}",
                lengthMenu: "{{ trans('ticket::lang.table-length-menu') }}",
                loadingRecords: "{{ trans('ticket::lang.table-loading-results') }}",
                processing: "{{ trans('ticket::lang.table-processing') }}",
                search: "{{ trans('ticket::lang.table-search') }}",
                zeroRecords: "{{ trans('ticket::lang.table-zero-records') }}",
                paginate: {
                    first: "{{ trans('ticket::lang.table-paginate-first') }}",
                    last: "{{ trans('ticket::lang.table-paginate-last') }}",
                    next: "{{ trans('ticket::lang.table-paginate-next') }}",
                    previous: "{{ trans('ticket::lang.table-paginate-prev') }}"
                },
                aria: {
                    sortAscending: "{{ trans('ticket::lang.table-aria-sort-asc') }}",
                    sortDescending: "{{ trans('ticket::lang.table-aria-sort-desc') }}"
                },
            },
            columns: [
                {data: 'step', name: 'test.step'},
                {data: 'name', name: 'test.name'},
                {data: 'details', name: 'test.details'},
                {data: 'outcome', name: 'test.expected'},
                {data: 'actual', name: 'test.results'},
                {data: 'passed', name: 'test.status'},
                {data: 'updated_at', name: 'test.updated_at'},
            ]
        });
    </script>
@append

