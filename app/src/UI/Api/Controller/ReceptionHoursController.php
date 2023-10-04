<?php

namespace App\UI\Api\Controller;

use App\Application\Dto\Request\ReceptionRequestDto;
use App\Application\Service\ReceptionService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/reception', name: 'reception_')]
class ReceptionHoursController extends AbstractController
{
    public function __construct(private SerializerInterface    $serializer,
    private ValidatorInterface     $validator,
    private ReceptionService $receptionService,
    private EntityManagerInterface $entityManager)
    {
    }

    #[Route('/add', name: 'add', methods:['post'] )]
    public function addReceptionHours(Request $request)
    {
        try {
            /** @var ReceptionRequestDto $bookingRequest */
            $receptionRequest = $this->serializer->deserialize(
                $request->getContent(),
                ReceptionRequestDto::class,
                'json'
            );
            $receptionHours = $this->receptionService->addReceptionHours($receptionRequest);
        } catch (Exception $e) {
            echo $e->getMessage() . "\n";
        }
        
        return new JsonResponse([
            'time'=> $receptionHours->getTime()
        ]);
    }
}
