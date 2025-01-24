@extends('ticket::layouts.master')

@section('page', trans('ticket::lang.index-title'))
@section('page_title', trans('ticket::lang.index-my-tickets'))


@section('ticket_header')
{!! link_to_route($setting->grab('main_route').'.create', trans('ticket::lang.btn-create-new-ticket'), null, ['class' => 'btn btn-primary']) !!}
@stop

@section('ticket_content_parent_class', 'pl-0 pr-0')

@section('ticket_content')
    <div id="message"></div>
    @include('ticket::tickets.partials.datatable')
@stop
{{-- Main Index --}}
@section('footer')
	<script src="https://cdn.datatables.net/v/bs4/dt-{{ mpba\Tickets\Helpers\Cdn::DataTables }}/r-{{ mpba\Tickets\Helpers\Cdn::DataTablesResponsive }}/datatables.min.js"></script>
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
                    "complete": "{{ $complete }}",
                    "archived": "{{ $archived }}",
                },
                "url":'{!! route($setting->grab('main_route').'.data', $complete,$archived) !!}',
                "type": "GET",
            },
	        language: {
				decimal:        "{{ trans('ticket::lang.table-decimal') }}",
				emptyTable:     "{{ trans('ticket::lang.table-empty') }}",
				info:           "{{ trans('ticket::lang.table-info') }}",
				infoEmpty:      "{{ trans('ticket::lang.table-info-empty') }}",
				infoFiltered:   "{{ trans('ticket::lang.table-info-filtered') }}",
				infoPostFix:    "{{ trans('ticket::lang.table-info-postfix') }}",
				thousands:      "{{ trans('ticket::lang.table-thousands') }}",
				lengthMenu:     "{{ trans('ticket::lang.table-length-menu') }}",
				loadingRecords: "{{ trans('ticket::lang.table-loading-results') }}",
				processing:     "{{ trans('ticket::lang.table-processing') }}",
				search:         "{{ trans('ticket::lang.table-search') }}",
				zeroRecords:    "{{ trans('ticket::lang.table-zero-records') }}",
				paginate: {
					first:      "{{ trans('ticket::lang.table-paginate-first') }}",
					last:       "{{ trans('ticket::lang.table-paginate-last') }}",
					next:       "{{ trans('ticket::lang.table-paginate-next') }}",
					previous:   "{{ trans('ticket::lang.table-paginate-prev') }}"
				},
				aria: {
					sortAscending:  "{{ trans('ticket::lang.table-aria-sort-asc') }}",
					sortDescending: "{{ trans('ticket::lang.table-aria-sort-desc') }}"
				},
			},
	        columns: [
                { data: 'reference', name: 'tickets.reference' },
	            { data: 'subject', name: 'subject' },
                { data: 'project', name: 'tickets_projects.title' },
	            { data: 'status', name: 'tickets_statuses.name' },
	            { data: 'updated_at', name: 'tickets_projects.updated_at' },
            	{ data: 'agent', name: 'users.name' },
	            @if( $u->isAgent() || $u->isAdmin() )
		            { data: 'priority', name: 'tickets_priorities.name' },
	            	{ data: 'owner', name: 'users.name' },
		            { data: 'category', name: 'tickets_categories.name' }
	            @endif
	        ]
	    });
	</script>
@append
