<?php

namespace mpba\Tickets\Models;

use Illuminate\Database\Eloquent\Model;

class Priority extends Model
{
    protected $table = 'tickets_priorities';

    protected $fillable = ['name', 'color'];

    /**
     * Indicates that this model should not be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get related tickets.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tickets()
    {
        return $this->hasMany('mpba\Tickets\Models\Ticket', 'priority_id');
    }
}
