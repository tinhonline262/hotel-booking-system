<?php

namespace App\Domain\ValueObjects;

class UploadedFile
{
    private string $tempPath;
    private string $originalName;
    private string $mimeType;
    private int $size;
    private int $error;

    public function __construct(
        string $tempPath,
        string $originalName,
        string $mimeType,
        int $size,
        int $error = UPLOAD_ERR_OK
    ) {
        $this->tempPath = $tempPath;
        $this->originalName = $originalName;
        $this->mimeType = $mimeType;
        $this->size = $size;
        $this->error = $error;
    }

    public static function fromArray(array $fileData): self
    {
        return new self(
            $fileData['tmp_name'],
            $fileData['name'],
            $fileData['type'],
            $fileData['size'],
            $fileData['error'] ?? UPLOAD_ERR_OK
        );
    }

    public function getTempPath(): string
    {
        return $this->tempPath;
    }

    public function getOriginalName(): string
    {
        return $this->originalName;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getError(): int
    {
        return $this->error;
    }

    public function isValid(): bool
    {
        return $this->error === UPLOAD_ERR_OK && is_uploaded_file($this->tempPath);
    }

    public function getExtension(): string
    {
        return pathinfo($this->originalName, PATHINFO_EXTENSION);
    }
}

