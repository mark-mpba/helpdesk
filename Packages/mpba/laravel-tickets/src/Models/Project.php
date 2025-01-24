<?php

namespace mpba\Tickets\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $table = 'tickets_projects';
    protected $fillable =[
        'title',
        'color',
        'description',
        'created_at',
        'updated_at',
        'archived_at',
        'deleted_at'
    ];


    /**
     * Get related tickets.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tickets() :HasMany
    {
        return $this->hasMany(Ticket::class, 'project_id');
    }

}
