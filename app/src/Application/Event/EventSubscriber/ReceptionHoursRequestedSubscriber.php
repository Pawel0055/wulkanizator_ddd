<?php


namespace App\Application\Event\EventSubscriber;

use App\Domain\Entity\ReceptionHours;
use App\Domain\Event\ReceptionHoursRequestedEvent;
use App\Infrastructure\Repository\ReceptionHoursRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class ReceptionHoursRequestedSubscriber implements EventSubscriberInterface
{
    public function __construct(private EventDispatcherInterface $eventDispatcher, private EntityManagerInterface $entityManager, private ReceptionHoursRepository $receptionHoursRepository)
    {
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
        $this->receptionHoursRepository->save($receptionHour, true);

        return $receptionHour;
    }
}
