@extends($master)

@section('page')
    {{ trans('ticket::lang.index-title') }}
@stop

@section('content')
    @include('ticket::shared.header')
    @include('ticket::tickets.index')
@stop

@section('footer')
	<script src="https://cdn.datatables.net/v/bs/dt-{{ mpba\Tickets\Helpers\Cdn::DataTables }}/r-{{ mpba\Tickets\Helpers\Cdn::DataTablesResponsive }}/datatables.min.js"></script>
	<script>
	    $('.table').DataTable({
	        processing: false,
	        serverSide: true,
	        responsive: true,
            pageLength: {{ $setting->grab('paginate_items') }},
        	lengthMenu: {{ json_encode($setting->grab('length_menu')) }},
	        ajax: '{!! route($setting->grab('main_route').'.data', $complete,$archived) !!}',
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
	            { data: 'id', name: 'ticket.id' },
	            { data: 'subject', name: 'subject' },
	            { data: 'status', name: 'ticket_statuses.name' },
	            { data: 'updated_at', name: 'ticket.updated_at' },
            	{ data: 'agent', name: 'users.name' },
	            @if( $u->isAgent() || $u->isAdmin() )
		            { data: 'priority', name: 'ticket_priorities.name' },
	            	{ data: 'owner', name: 'users.name' },
		            { data: 'category', name: 'ticket_categories.name' }
	            @endif
	        ]
	    });
	</script>
@append
