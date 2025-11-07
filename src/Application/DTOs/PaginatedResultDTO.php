<?php
namespace Hotel\Application\DTOs;

class PaginatedResultDTO {
    /**
     * @param int $total Tổng số mục
     * @param int $page Trang hiện tại
     * @param int $limit Số mục mỗi trang
     * @param array $data Dữ liệu của trang này
     */
    public function __construct(
        public readonly int $total,
        public readonly int $page,
        public readonly int $limit,
        public readonly array $data
    ) {}

    public function getTotalPages(): int
    {
        if ($this->limit == 0) {
            return 0; // Tránh chia cho 0
        }
        return (int) ceil($this->total / $this->limit);
    }
}