<?php

namespace App\Domain\Entities;

class RoomImage
{
    private ?int $id;
    private int $roomId;
    private string $imageUrl;
    private string $storageType; // 'local' or 'cloudinary'
    private ?string $cloudinaryPublicId;
    private int $fileSize; // in bytes
    private string $mimeType;
    private int $displayOrder;
    private bool $isPrimary;
    private ?string $altText;
    private ?string $createdAt;
    private ?string $updatedAt;

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
        ?string $altText = null,
        ?string $createdAt = null,
        ?string $updatedAt = null
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
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoomId(): int
    {
        return $this->roomId;
    }

    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    public function getStorageType(): string
    {
        return $this->storageType;
    }

    public function getCloudinaryPublicId(): ?string
    {
        return $this->cloudinaryPublicId;
    }

    public function getFileSize(): int
    {
        return $this->fileSize;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function getDisplayOrder(): int
    {
        return $this->displayOrder;
    }

    public function isPrimary(): bool
    {
        return $this->isPrimary;
    }

    public function getAltText(): ?string
    {
        return $this->altText;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    public function setDisplayOrder(int $displayOrder): void
    {
        $this->displayOrder = $displayOrder;
    }

    public function setPrimary(bool $isPrimary): void
    {
        $this->isPrimary = $isPrimary;
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
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }
}

