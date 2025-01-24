<?php

namespace mpba\Tickets\Controllers;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use mpba\Tickets\Helpers\LaravelVersion;
use mpba\Tickets\Mail\TicketNotification;
use mpba\Tickets\Models\Comment;
use mpba\Tickets\Models\Setting;
use mpba\Tickets\Models\Ticket;

class NotificationsController extends Controller
{
    public function newComment(Comment $comment)
    {
        $ticket = $comment->ticket;
        $notification_owner = $comment->user;
        $template = 'ticket::emails.comment';
        $data = ['comment' => serialize($comment), 'ticket' => serialize($ticket)];

        $this->sendNotification($template, $data, $ticket, $notification_owner,
            trans('ticket::lang.notify-new-comment-from').$notification_owner->name.trans('ticket::lang.notify-on').$ticket->subject, 'comment');
    }

    public function ticketStatusUpdated(Ticket $ticket, Ticket $original_ticket)
    {
        $notification_owner = auth()->user();
        $template = 'ticket::emails.status';
        $data = [
            'ticket' => serialize($ticket),
            'notification_owner' => serialize($notification_owner),
            'original_ticket' => serialize($original_ticket),
        ];

        if (strtotime($ticket->completed_at)) {
            $this->sendNotification($template, $data, $ticket, $notification_owner,
                $notification_owner->name.trans('ticket::lang.notify-updated').$ticket->subject.trans('ticket::lang.notify-status-to-complete'), 'status');
        } else {
            $this->sendNotification($template, $data, $ticket, $notification_owner,
                $notification_owner->name.trans('ticket::lang.notify-updated').$ticket->subject.trans('ticket::lang.notify-status-to').$ticket->status->name, 'status');
        }
    }

    public function ticketAgentUpdated(Ticket $ticket, Ticket $original_ticket)
    {
        $notification_owner = auth()->user();
        $template = 'ticket::emails.transfer';
        $data = [
            'ticket' => serialize($ticket),
            'notification_owner' => serialize($notification_owner),
            'original_ticket' => serialize($original_ticket),
        ];

        $this->sendNotification($template, $data, $ticket, $notification_owner,
            $notification_owner->name.trans('ticket::lang.notify-transferred').$ticket->subject.trans('ticket::lang.notify-to-you'), 'agent');
    }

    public function newTicketNotifyAgent(Ticket $ticket)
    {
        $notification_owner = auth()->user();
        $template = 'ticket::emails.assigned';
        $data = [
            'ticket' => serialize($ticket),
            'notification_owner' => serialize($notification_owner),
        ];

        $this->sendNotification($template, $data, $ticket, $notification_owner,
            $notification_owner->name.trans('ticket::lang.notify-created-ticket').$ticket->subject, 'agent');
    }

    /**
     * Send email notifications from the action owner to other involved users.
     *
     * @param  string  $template
     * @param  array  $data
     * @param  object  $ticket
     * @param  object  $notification_owner
     */
    public function sendNotification($template, $data, $ticket, $notification_owner, $subject, $type)
    {

        if (!Setting::grab('disable_notifications')){
            Log::info(Setting::grab('disable_notifications'));
            $to = null;

            if ($type != 'agent') {
                $to = $ticket->user;

                if ($ticket->user->email != $notification_owner->email) {
                    $to = $ticket->user;
                }

                if ($ticket->agent->email != $notification_owner->email) {
                    $to = $ticket->agent;
                }
            } else {
                $to = $ticket->agent;
            }

            if (LaravelVersion::lt('5.4')) {
                $mail_callback = function ($m) use ($to, $notification_owner, $subject) {
                    $m->to($to->email, $to->name);

                    $m->replyTo($notification_owner->email, $notification_owner->name);

                    $m->subject($subject);
                };

                if (Setting::grab('queue_emails') == 'yes') {
                    Mail::queue($template, $data, $mail_callback);
                } else {
                    Mail::send($template, $data, $mail_callback);
                }
            } elseif (LaravelVersion::min('5.4')) {
                $mail = new TicketNotification($template, $data, $notification_owner, $subject);
                if (Setting::grab('queue_emails') == 'yes') {
                    Mail::to($to)->queue($mail);
                } else {
                    Mail::to($to)->send($mail);
                }
            }
        }
    }
}
