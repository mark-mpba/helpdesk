<?php $comment = unserialize($comment);?>
<?php $ticket = unserialize($ticket);?>

@extends($email)

@section('subject')
	{{ trans('ticket::email/globals.comment') }}
@stop

@section('link')
	<a style="color:#ffffff" href="{{ route($setting->grab('main_route').'.show', $ticket->id) }}">
		{{ trans('ticket::email/globals.view-ticket') }}
	</a>
@stop

@section('content')
	{!! trans('ticket::email/comment.data', [
	    'name'      =>  $comment->user->name,
	    'subject'   =>  $ticket->subject,
	    'status'    =>  $ticket->status->name,
	    'category'  =>  $ticket->category->name,
	    'comment'   =>  $comment->getShortContent()
	]) !!}
@stop
