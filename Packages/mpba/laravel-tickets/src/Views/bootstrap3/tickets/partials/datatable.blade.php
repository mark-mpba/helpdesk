<table class="ticket-table table table-striped table-bordered dt-responsive nowrap" style="width:100%">
    <thead>
        <tr>
            <td>{{ trans('ticket::lang.table-id') }}</td>
            <td>{{ trans('ticket::lang.table-subject') }}</td>
            <td>{{ trans('ticket::lang.table-status') }}</td>
            <td>{{ trans('ticket::lang.table-last-updated') }}</td>
            <td>{{ trans('ticket::lang.table-agent') }}</td>
          @if( $u->isAgent() || $u->isAdmin() )
            <td>{{ trans('ticket::lang.table-priority') }}</td>
            <td>{{ trans('ticket::lang.table-owner') }}</td>
            <td>{{ trans('ticket::lang.table-category') }}</td>
          @endif
        </tr>
    </thead>
</table>
