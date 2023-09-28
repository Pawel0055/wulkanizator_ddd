<?php

namespace App\Application\Event\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Exception;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface as SymfonyMailerInterface;
use Symfony\Component\Mime\Email;
use App\Domain\Event\BookingConfirmedEvent;

class BookingConfirmedSubscriber implements EventSubscriberInterface
{

    public function __construct(private SymfonyMailerInterface $mailer)
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            BookingConfirmedEvent::NAME => 'onBookingConfirmed'
        ];
    }

    public function onBookingConfirmed(BookingConfirmedEvent $event)
    {
        $time = $event->getReceptionHour()->getTime()->format('H:i');
        $date = $event->getBooking()->getDate()->format('Y-m-d');
        return $this->sendEmailMessage(
            'Otrzymałeś nową wiadomość (Dodanie terminu)',
            'test@test.pl',
            'Nowy termin ' . $date . ' na godzine ' . $time . ' został pomyślnie dodany.'
        );
    }

    protected function sendEmailMessage(string $subject, string $to, string $message): Email
    {
        $email = (new Email())
            ->from('wulkanizator@example.com')
            ->to($to)
            ->subject($subject)
            ->text($message);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            throw new Exception($e->getMessage());
        }
        return $email;
    }
}