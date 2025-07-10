<?php

namespace mpba\Tickets\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Jenssegers\Date\Date;
use mpba\LaravelArchivable\Traits\Archivable;
use mpba\Tickets\Traits\ContentEllipse;
use mpba\Tickets\Traits\Purifiable;

class Test extends Model
{
    use ContentEllipse;
    use Purifiable;
    use Archivable;

    public const TABLE_NAME = 'tickets_tests';
    protected $table = self::TABLE_NAME;

    protected $fillable = [
        'step',
        'version',
        'name',
        'details',
        'outcome',
        'actual',
        'passed',
        'archived_at',
    ];


    protected $dates = [
        'completed_at',
        'archived_at'
    ];

    /**
     * List of completed tickets.
     *
     * @return bool
     */
    public function hasComments()
    {
        return (bool)count($this->comments);
    }

    public function isComplete()
    {
        return (bool)$this->completed_at;
    }


    /**
     * List of completed tickets.
     *
     * @return Collection
     */
    public function scopeComplete($query)
    {
        return $query->whereNotNull('completed_at');
    }

    /**
     * List of Archived tickets.
     *
     * @return Collection
     */
    public function scopeAllArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }


    /**
     * List of active tickets.
     *
     * @return Collection
     */
    public function scopeActive($query)
    {
        return $query->whereNull('completed_at');
    }

    /**
     * Get Tickets Project.
     *
     * @return BelongsTo
     */
    public function project()
    {
        return $this->belongsTo('mpba\Tickets\Models\Project', 'project_id');
    }

    /**
     * Get Ticket comments.
     *
     * @return HasMany
     */
    public function comments()
    {
        return $this->hasMany('mpba\Tickets\Models\Comment', 'ticket_id');
    }


    /**
     * @see Illuminate/Database/Eloquent/Model::asDateTime
     */
    public function freshTimestamp()
    {
        return new Date();
    }

    /**
     * @see Illuminate/Database/Eloquent/Model::asDateTime
     */
    protected function asDateTime($value)
    {
        if (is_numeric($value)) {
            return Date::createFromTimestamp($value);
        } elseif (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $value)) {
            return Date::createFromFormat('Y-m-d', $value)->startOfDay();
        } elseif (!$value instanceof \DateTimeInterface) {
            $format = $this->getDateFormat();

            return Date::createFromFormat($format, $value);
        }

        return Date::instance($value);
    }
}
