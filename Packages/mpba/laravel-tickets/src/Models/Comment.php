<?php

namespace mpba\Tickets\Models;

use Illuminate\Database\Eloquent\Model;
use mpba\Tickets\Traits\ContentEllipse;
use mpba\Tickets\Traits\Purifiable;

class Comment extends Model
{
    use ContentEllipse;
    use Purifiable;

    protected $table = 'tickets_comments';

    /**
     * Get related ticket.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ticket()
    {
        return $this->belongsTo('mpba\Tickets\Models\Ticket', 'ticket_id');
    }

    /**
     * Get comment owner.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
