<div class="card mb-3">
    <div class="card-body row">
        <div class="col-md-6">
            <p>
                <strong>{{ trans('ticket::lang.reference') }}</strong>{{ trans('ticket::lang.colon') }}{{ $ticket->reference }}
            </p>
            <p>
                <strong>{{ trans('ticket::lang.owner') }}</strong>{{ trans('ticket::lang.colon') }}{{ $ticket->user_id == $u->id ? $u->name : $ticket->user->name }}
            </p>
            <p>
                <strong>{{ trans('ticket::lang.project') }}</strong>{{ trans('ticket::lang.colon') }}
                <span style="color: {{ ($ticket->project ? $ticket->project->color : 'N/A') }}">{{ ($ticket->project ? $ticket->project->title:'N/A') }}</span>
            </p>
            <p>
                <strong>{{ trans('ticket::lang.status') }}</strong>{{ trans('ticket::lang.colon') }}

                @if( $ticket->isComplete() && ! $setting->grab('default_close_status_id') )
                    <span style="color: blue">Complete</span>
                @else
                    <span style="color: {{ $ticket->status->color }}">{{ $ticket->status->name }}</span>
                @endif

            </p>
            <p>
                <strong>{{ trans('ticket::lang.priority') }}</strong>{{ trans('ticket::lang.colon') }}
                <span style="color: {{ $ticket->priority->color }}">
                    {{ $ticket->priority->name }}
                </span>
            </p>
        </div>
        <div class="col-md-6">
            <p> <strong>{{ trans('ticket::lang.responsible') }}</strong>{{ trans('ticket::lang.colon') }}{{ $ticket->agent_id == $u->id ? $u->name : $ticket->agent->name }}</p>
            <p>
                <strong>{{ trans('ticket::lang.category') }}</strong>{{ trans('ticket::lang.colon') }}
                <span style="color: {{ $ticket->category->color }}">
                    {{ $ticket->category->name }}
                </span>
            </p>
            <p> <strong>{{ trans('ticket::lang.created') }}</strong>{{ trans('ticket::lang.colon') }}{{ $ticket->created_at->diffForHumans() }}</p>
            <p> <strong>{{ trans('ticket::lang.last-update') }}</strong>{{ trans('ticket::lang.colon') }}{{ $ticket->updated_at->diffForHumans() }}</p>
        </div>
    </div>
</div>

{!! $ticket->html !!}

{!! CollectiveForm::open([
                'method' => 'DELETE',
                'route' => [
                            $setting->grab('main_route').'.destroy',
                            $ticket->id
                            ],
                'id' => "delete-ticket-$ticket->id"
                ])
!!}
{!! CollectiveForm::close() !!}


@if($u->isAgent() || $u->isAdmin())
    @include('ticket::tickets.edit')
@endif

{{-- // OR; Modal Window: 2/2 --}}
@if($u->isAdmin())
    @include('ticket::tickets.partials.modal-delete-confirm')
@endif
{{-- // END Modal Window: 2/2 --}}
