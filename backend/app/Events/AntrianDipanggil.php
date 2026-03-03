<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class AntrianDipanggil implements ShouldBroadcastNow
{
    use SerializesModels;

    public array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('antrian_channel');
    }

    public function broadcastAs(): string
    {
        return 'AntrianDipanggil';
    }

    public function broadcastWith(): array
    {
        return $this->data;
    }
}
