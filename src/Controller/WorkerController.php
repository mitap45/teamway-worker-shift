<?php

namespace App\Controller;

use App\Entity\Worker;
use App\Entity\WorkerShift;
use DateTime;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class WorkerController
 * @package App\Controller
 *
 * @Route("/worker")
 */
class WorkerController extends AbstractController
{
    /**
     * @Route("/", name="product_index", methods={"GET"})
     * @return JsonResponse
     */
    public function index(ManagerRegistry $doctrine): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $workers = $entityManager->getRepository(Worker::class)->findAll();
        return $this->json($workers);
    }

    /**
     * @Route("/new", name="new", methods={"POST"})
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @return JsonResponse
     */
    public function new(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $fullName = $request->get('fullName');
        $worker = (new Worker)->setFullName($fullName);

        $entityManager = $doctrine->getManager();

        $entityManager->persist($worker);
        $entityManager->flush();

        return $this->json($worker);
    }

    /**
     * @Route("/new-shift", name="new-shift", methods={"POST"})
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @return JsonResponse
     */
    public function newShift(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $workerId = $request->get('workerId');
        $shift = $request->get('shift');
        $date = $request->get('date');

        $date = DateTime::createFromFormat('d-m-Y', $date);

        if ($date === false) {
            return new JsonResponse('Date must be valid and in d-m-Y format', 422);
        }

        if (!array_key_exists($shift, WorkerShift::SHIFTS)) {
            return new JsonResponse('Shift must be one of these ["0-8", "8-16", "16-24"]', 422);
        }


        $entityManager = $doctrine->getManager();
        $worker = $entityManager->getRepository(Worker::class)->find($workerId);

        if (is_null($worker)) {
            return new JsonResponse('Worker could not be found with id: '.$workerId, 422);
        }

        $workerShift = (new WorkerShift())
            ->setWorker($worker)
            ->setShift(WorkerShift::SHIFTS[$shift])
            ->setShiftDate($date);

        $entityManager->persist($workerShift);

        try {
            $entityManager->flush();
        } catch (UniqueConstraintViolationException $e) {
            //If there is a shift on this day with this worker then return 422
            return new JsonResponse('Specified worker already has shift on this date.', 422);
        }

        return $this->json($worker);

    }

}