<?php

namespace App\Entity;

use App\Repository\WorkerRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * with unique constraint on (shift_date, worker_id)
 * we make sure that worker has only one shift in one day
 *
 * @ORM\Entity(repositoryClass=WorkerRepository::class)
 * @ORM\Table(name="worker_shift",
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="worker_shift_unique",
 *            columns={"shift_date", "worker_id"})
 *    })
 */
class WorkerShift
{
    public const SHIFTS = ['0-8' => 1, '8-16' => 2, '16-24' => 3];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private DateTimeInterface $shiftDate;

    /**
     * @ORM\ManyToOne(targetEntity=Worker::class, inversedBy="shifts")
     * @ORM\JoinColumn(nullable=false)
     */
    private Worker $worker;

    /**
     * @ORM\Column(type="integer")
     */
    private int $shift;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShiftDate(): ?DateTimeInterface
    {
        return $this->shiftDate;
    }

    public function setShiftDate(DateTimeInterface $shiftDate): self
    {
        $this->shiftDate = $shiftDate;

        return $this;
    }

    public function getWorker(): ?Worker
    {
        return $this->worker;
    }

    public function setWorker(Worker $worker): self
    {
        $this->worker = $worker;

        return $this;
    }

    public function getShift(): ?int
    {
        return $this->shift;
    }

    public function setShift(int $shift): self
    {
        $this->shift = $shift;

        return $this;
    }
}
