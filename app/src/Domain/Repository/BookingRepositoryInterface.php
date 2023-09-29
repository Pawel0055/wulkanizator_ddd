<?php

namespace App\Domain\Repository;

interface BookingRepositoryInterface
{
    public function findBusyTimes($date): array;
}