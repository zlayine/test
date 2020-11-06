<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Campaign_stats;

class StatsEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $stats;

    /**
     * Create a new event instance.
     *
     * @param  $comment
     *
     * @return void
     */

    public function __construct($stats)
    {
        $this->stats = $stats;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */

    public function broadcastOn()
    {
        return new Channel('stats-channel');
    }

    public function broadcastAs()
    {
        return 'new-stats';
    }


}