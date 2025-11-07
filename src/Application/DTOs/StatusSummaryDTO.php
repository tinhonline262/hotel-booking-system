<?php
namespace Hotel\Application\DTOs;

class StatusSummaryDTO {
    public function __construct(
        public readonly string $status,
        public readonly int $count
    ) {}
}