@extends('ticket::layouts.master')
@section('page', trans('ticket::lang.show-ticket-title') . trans('ticket::lang.colon') . $ticket->subject)
@section('page_title', $ticket->subject)

@section('ticket_header')
    <div>
        @if(! $ticket->completed_at && $close_perm == 'yes')
            {!! link_to_route($setting->grab('main_route').'.complete', trans('ticket::lang.btn-mark-complete'), $ticket->id,
                                ['class' => 'btn btn-success']) !!}
        @elseif($ticket->completed_at && $reopen_perm == 'yes')
            {!! link_to_route($setting->grab('main_route').'.reopen', trans('ticket::lang.reopen-ticket'), $ticket->id,
                                ['class' => 'btn btn-success']) !!}
        @endif
        @if($u->isAgent() || $u->isAdmin())
            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#ticket-edit-modal">
                {{ trans('ticket::lang.btn-edit')  }}
            </button>
        @endif
        @if($u->isAdmin())
            @if($setting->grab('delete_modal_type') == 'builtin')
                {!! link_to_route(
                                $setting->grab('main_route').'.destroy', trans('ticket::lang.btn-delete'), $ticket->id,
                                [
                                'class' => 'btn btn-danger deleteit',
                                'form' => "delete-ticket-$ticket->id",
                                "node" => $ticket->subject
                                ])
                !!}
            @elseif($setting->grab('delete_modal_type') == 'modal')
                {{-- // OR; Modal Window: 1/2 --}}
                {!! CollectiveForm::open(array(
                        'route' => array($setting->grab('main_route').'.destroy', $ticket->id),
                        'method' => 'delete',
                        'style' => 'display:inline'
                   ))
                !!}
                <button type="button"
                        class="btn btn-danger"
                        data-toggle="modal"
                        data-target="#confirmDelete"
                        data-title="{!! trans('ticket::lang.show-ticket-modal-delete-title', ['id' => $ticket->id]) !!}"
                        data-message="{!! trans('ticket::lang.show-ticket-modal-delete-message', ['subject' => $ticket->subject]) !!}"
                >
                    {{ trans('ticket::lang.btn-delete') }}
                </button>
            @endif
            {!! CollectiveForm::close() !!}
            {{-- // END Modal Window: 1/2 --}}
        @endif
    </div>
@stop

@section('ticket_content')
    @include('ticket::tickets.partials.ticket_body')
    @if($ticket->hasMedia('attachments'))
        <h4>
    <span class="btn btn-primary rounded-pill px-3 py-2">
        <i class="fa fa-paperclip me-1"></i> Attachments
    </span>
        </h4>
        <ul class="list-group mb-4">
            @foreach($ticket->getMedia('attachments') as $media)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                <span>
                    <i class="fa fa-paperclip me-2"></i> {{ $media->file_name }}
                </span>
                    <a href="{{ $media->getUrl() }}"
                       class="btn btn-sm btn-outline-primary"
                       target="_blank" download>
                        <i class="fa fa-download me-1"></i> Download
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
@endsection

@section('ticket_extra_content')

    @include('ticket::tickets.partials.comments')
    {{-- pagination --}}
    {!! $comments->render("pagination::bootstrap-4") !!}
    @include('ticket::tickets.partials.comment_form')
@stop

@section('footer')
    <script>
        $(document).ready(function () {
            $(".deleteit").click(function (event) {
                event.preventDefault();
                if (confirm("{!! trans('ticket::lang.show-ticket-js-delete') !!}" + $(this).attr("node") + " ?")) {
                    var form = $(this).attr("form");
                    $("#" + form).submit();
                }

            });
            $('#category_id').change(function () {
                var loadpage = "{!! route($setting->grab('main_route').'agentselectlist') !!}/" + $(this).val() + "/{{ $ticket->id }}";
                $('#agent_id').load(loadpage);
            });
            $('#confirmDelete').on('show.bs.modal', function (e) {
                $message = $(e.relatedTarget).attr('data-message');
                $(this).find('.modal-body p').text($message);
                $title = $(e.relatedTarget).attr('data-title');
                $(this).find('.modal-title').text($title);

                // Pass form reference to modal for submission on yes/ok
                var form = $(e.relatedTarget).closest('form');
                $(this).find('.modal-footer #confirm').data('form', form);
            });

            <!-- Form confirm (yes/ok) handler, submits form -->
            $('#confirmDelete').find('.modal-footer #confirm').on('click', function () {
                $(this).data('form').submit();
            });
        });
    </script>
    @include('ticket::tickets.partials.summernote')
@append
