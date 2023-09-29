<?php

namespace App\Domain\Repository;

interface ReceptionHoursRepositoryInterface
{
    public function findFreeDates($busyTimes): array;
}