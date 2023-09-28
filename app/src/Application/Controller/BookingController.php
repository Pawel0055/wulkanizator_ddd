<?php

namespace App\Application\Controller;

use App\Domain\Entity\Booking;
use App\Domain\Entity\ReceptionHours;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Application\Service\BookingService;
use Exception;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use App\Application\Dto\Request\BookingRequestDto;

#[Route('/booking', name: 'booking_')]
class BookingController extends AbstractController
{
    public function __construct(private SerializerInterface    $serializer,
                                private EntityManagerInterface $entityManager,
                                private BookingService $bookingService,
                                private PaginatorInterface $paginator)
    {
    }

    #[Route('/get', name: 'get', methods:['get'] )]
    public function getBookings(Request $request): JsonResponse
    {
        $bookings = $this->entityManager
            ->getRepository(Booking::class)
            ->findAll();

        $pagination = $this->paginator->paginate(
            $bookings,
            $request->query->getInt('page', 1), 
            10 
        );
   
        $data = [];
   
        foreach ($pagination->getItems() as $pagin) {
           $data[] = [
               'id' => $pagin->getId(),
               'registrationNumber' => $pagin->getRegistrationNumber(),
               'date' => $pagin->getDate()->format('Y-m-d'),
               'time' => $pagin->getReceptionHours()->getTime()->format('H:i')
           ];
        }

        return $this->json(['data' => $data,
         'totalItemCount' => $pagination->getTotalItemCount(),
         'currentPage' => $pagination->getCurrentPageNumber()
        ]);
    }

    #[Route('/get/{id}', name: 'get_by_id', methods:['get'] )]
    public function getBooking(int $id): JsonResponse
    {
        $booking = $this->entityManager
            ->getRepository(Booking::class)
            ->findOneById($id);
    
        if($booking) {
        $data = [
            'id' => $booking->getId(),
            'registrationNumber' => $booking->getRegistrationNumber(),
            'date' => $booking->getDate()->format('Y-m-d'),
            'time' => $booking->getReceptionHours()->getTime()->format('H:i')
        ];
        return $this->json($data);
        } else {
            return $this->json(['error' => 'Niepoprawne dane']);
        }
    }

    #[Route('/add', name: 'add', methods:['post'] )]
    public function addBooking(Request $request): JsonResponse
    {
        try {
            /** @var BookingRequestDto $bookingRequest */
            $bookingRequest = $this->serializer->deserialize(
                $request->getContent(),
                BookingRequestDto::class,
                'json'
            );
            $booking = $this->bookingService->addBooking($bookingRequest);
        } catch (Exception $e) {
            echo $e->getMessage() . "\n";
        }
        return $this->json($booking);
    }

    #[Route('/remove/{id}', name: 'remove', methods:['delete'] )]
    public function removeBooking(int $id): JsonResponse
    {
        $booking = $this->entityManager
            ->getRepository(Booking::class)
            ->findOneById($id);

        if(!$booking) {
            return $this->json(['error' => 'Niepoprawne dane']);
        }
        $this->entityManager->remove($booking);
        $this->entityManager->flush();

        return $this->json('Rezerwacja usunieta');
    }

    #[Route('/freedates', name: 'free_dates', methods:['post'] )]
    public function checkFreeDates(Request $request): JsonResponse
    {
        $date = new DateTime($request->request->get('date'));
        $busyTimes = $this->entityManager
            ->getRepository(Booking::class)
            ->findBusyTimes($date->format('Y-m-d'));
        $arrayUnique = array_unique(array_column($busyTimes, 'id'));

        $freeDates = $this->entityManager
        ->getRepository(ReceptionHours::class)
        ->findFreeDates($arrayUnique);

        if(!$freeDates) {
            return $this->json(['error' => 'Niepoprawne dane']);
        }
        
        foreach ($freeDates as $freeDate) {
            $data[] = [
                'time' => $freeDate["time"]->format('H:i')
            ];
         }
 
         return $this->json([
            'data' => $data
         ]);
    }
}
