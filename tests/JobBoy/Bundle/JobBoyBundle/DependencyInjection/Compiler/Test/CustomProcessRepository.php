<?php
declare(strict_types=1);

namespace Tests\JobBoy\Bundle\JobBoyBundle\DependencyInjection\Compiler\Test;

use JobBoy\Process\Domain\Entity\Id\ProcessId;
use JobBoy\Process\Domain\Entity\Process;
use JobBoy\Process\Domain\ProcessStatus;
use JobBoy\Process\Domain\Repository\ProcessRepositoryInterface;

class CustomProcessRepository implements ProcessRepositoryInterface
{

    public function add(Process $process): void
    {
    }

    public function remove(Process $process): void
    {
    }

    public function byId(ProcessId $id): ?Process
    {
    }

    public function all(?int $start = null, ?int $length = null): array
    {
    }

    public function handled(?int $start = null, ?int $length = null): array
    {
    }

    public function stale(?\DateTimeImmutable $until = null, ?int $start = null, ?int $length = null): array
    {
    }

    public function byStatus(ProcessStatus $status, ?int $start = null, ?int $length = null): array
    {
    }
}
