@extends($master)
@section('page', trans('ticket::lang.show-ticket-title') . trans('ticket::lang.colon') . $ticket->subject)
@section('content')
        @include('ticket::shared.header')
        @include('ticket::tickets.partials.ticket_body')
        <br>
        <h2>{{ trans('ticket::lang.comments') }}</h2>
        @include('ticket::tickets.partials.comments')
        {{-- pagination --}}
        {!! $comments->render() !!}
        @include('ticket::tickets.partials.comment_form')
@endsection

@section('footer')
    <script>
        $(document).ready(function() {
            $( ".deleteit" ).click(function( event ) {
                event.preventDefault();
                if (confirm("{!! trans('ticket::lang.show-ticket-js-delete') !!}" + $(this).attr("node") + " ?"))
                {
                    var form = $(this).attr("form");
                    $("#" + form).submit();
                }

            });
            $('#category_id').change(function(){
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
            $('#confirmDelete').find('.modal-footer #confirm').on('click', function(){
                $(this).data('form').submit();
            });
        });
    </script>
    @include('ticket::tickets.partials.summernote')
@append
