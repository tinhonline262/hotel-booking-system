<?php
namespace App\Application\DTOs;

class RevenueDataPointDTO {
    public function __construct(
        public readonly string $date, // '2025-11-04'
        public readonly float $revenue
    ) {}
}