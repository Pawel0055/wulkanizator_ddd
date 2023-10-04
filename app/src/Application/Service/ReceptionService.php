<?php

namespace App\Application\Service;

use App\Domain\Event\ReceptionHoursRequestedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ReceptionService
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private ValidatorInterface     $validator,
        private EventDispatcherInterface $eventDispatcher
    )
    {}

    public function addReceptionHours($request)
    {
        $errors = $this->validator->validate($request);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            return new Response($errorsString);
        }

        $receptionHour = $this->eventDispatcher->dispatch(new ReceptionHoursRequestedEvent($request->getTime()));

        return $receptionHour;
    }
}