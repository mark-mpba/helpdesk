<?php

namespace mpba\Tickets\Console;

use Illuminate\Console\Command;
use mpba\Tickets\Models\Ticket;

class UpdateTicketReferenceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:add-ref';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds a reference number to a ticket if one does not exists';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $tickets = Ticket::all();
        $seed = 103311;
        foreach ($tickets as $ticket){
            $ticket->reference = 'MG'.$seed;
            $ticket->save();
            $seed++;
        }
        return Command::SUCCESS;
    }
}
