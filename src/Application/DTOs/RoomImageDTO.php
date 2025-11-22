<?php

namespace App\Application\DTOs;

class RoomImageDTO
{
    public ?int $id;
    public int $roomId;
    public string $imageUrl;
    public string $storageType;
    public ?string $cloudinaryPublicId;
    public int $fileSize;
    public string $mimeType;
    public int $displayOrder;
    public bool $isPrimary;
    public ?string $altText;

    public function __construct(
        ?int $id,
        int $roomId,
        string $imageUrl,
        string $storageType,
        ?string $cloudinaryPublicId,
        int $fileSize,
        string $mimeType,
        int $displayOrder = 0,
        bool $isPrimary = false,
        ?string $altText = null
    ) {
        $this->id = $id;
        $this->roomId = $roomId;
        $this->imageUrl = $imageUrl;
        $this->storageType = $storageType;
        $this->cloudinaryPublicId = $cloudinaryPublicId;
        $this->fileSize = $fileSize;
        $this->mimeType = $mimeType;
        $this->displayOrder = $displayOrder;
        $this->isPrimary = $isPrimary;
        $this->altText = $altText;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            (int)($data['roomId'] ?? $data['room_id'] ?? 0),
            $data['imageUrl'] ?? $data['image_url'] ?? '',
            $data['storageType'] ?? $data['storage_type'] ?? 'local',
            $data['cloudinaryPublicId'] ?? $data['cloudinary_public_id'] ?? null,
            (int)($data['fileSize'] ?? $data['file_size'] ?? 0),
            $data['mimeType'] ?? $data['mime_type'] ?? '',
            (int)($data['displayOrder'] ?? $data['display_order'] ?? 0),
            (bool)($data['isPrimary'] ?? $data['is_primary'] ?? false),
            $data['altText'] ?? $data['alt_text'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'roomId' => $this->roomId,
            'imageUrl' => $this->imageUrl,
            'storageType' => $this->storageType,
            'cloudinaryPublicId' => $this->cloudinaryPublicId,
            'fileSize' => $this->fileSize,
            'mimeType' => $this->mimeType,
            'displayOrder' => $this->displayOrder,
            'isPrimary' => $this->isPrimary,
            'altText' => $this->altText,
        ];
    }
}

