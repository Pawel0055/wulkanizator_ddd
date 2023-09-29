<?php

namespace App\Domain\Event;

use Symfony\Contracts\EventDispatcher\Event;

class ReceptionHoursRequestedEvent extends Event
{
    private string $time;

    public function __construct(string $time)
    {
        $this->time = $time;
    }

    public function getTime(): string
    {
        return $this->time;
    }
}
