<?php

namespace App\Application\DTOs;

class RoomDetailDTO
{
    public int $roomId;
    public string $roomNumber;
    public string $roomType;
    public int $capacity;
    public array $amenities;
    public float $pricePerNight;
    public string $status;
    public array $images;

    public function __construct(
        int $roomId,
        string $roomNumber,
        string $roomType,
        int $capacity,
        array $amenities,
        float $pricePerNight,
        string $status = 'available',
        array $images = []
    ) {
        $this->roomId = $roomId;
        $this->roomNumber = $roomNumber;
        $this->roomType = $roomType;
        $this->capacity = $capacity;
        $this->amenities = $amenities;
        $this->pricePerNight = $pricePerNight;
        $this->status = $status;
        $this->images = $images;
    }

    public function toArray(): array
    {
        return [
            'roomId' => $this->roomId,
            'roomNumber' => $this->roomNumber,
            'roomType' => $this->roomType,
            'capacity' => $this->capacity,
            'amenities' => $this->amenities,
            'pricePerNight' => $this->pricePerNight,
            'status' => $this->status,
            'images' => array_map(function($image) {
                return [
                    'imageId' => $image['imageId'],
                    'imageUrl' => $image['imageUrl'],
                    'storageType' => $image['storageType'],
                    'fileSize' => $image['fileSize'],
                    'mimeType' => $image['mimeType'],
                    'isPrimary' => $image['isPrimary'],
                    'displayOrder' => $image['displayOrder']
                ];
            }, $this->images)
        ];
    }
}

