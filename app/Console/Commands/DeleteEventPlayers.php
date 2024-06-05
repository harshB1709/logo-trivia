<?php

namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Console\Command;

class DeleteEventPlayers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete-event-players {event}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes all the event players and their related data.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $event = Event::find($this->argument('event'));
        $event->players()->delete();
        $this->info("Deleted all the players and related data for event_id {$event->id}");
    }
}
