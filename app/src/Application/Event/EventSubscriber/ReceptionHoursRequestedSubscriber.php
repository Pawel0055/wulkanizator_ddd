<?php


namespace App\Application\Event\EventSubscriber;

use App\Domain\Entity\ReceptionHours;
use App\Domain\Event\ReceptionHoursRequestedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class ReceptionHoursRequestedSubscriber implements EventSubscriberInterface
{
    private EventDispatcherInterface $eventDispatcher;
    private EntityManagerInterface $entityManager;

    public function __construct(EventDispatcherInterface $eventDispatcher, EntityManagerInterface $entityManager)
     {
        $this->eventDispatcher = $eventDispatcher;
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ReceptionHoursRequestedEvent::class => 'onCreateReceptionHour',
        ];
    }

    public function onCreateReceptionHour(ReceptionHoursRequestedEvent $event)
    {
        $receptionHour = new ReceptionHours();
        $receptionHour->setTime(new DateTime($event->getTime()));
        $this->entityManager->persist($receptionHour);
        $this->entityManager->flush();

        return $receptionHour;
    }
}
