<?php

namespace App\Application\Validators;

use App\Domain\ValueObjects\UploadedFile;

class ImageUploadValidator
{
    private int $maxFileSize;
    private array $allowedMimeTypes;
    private array $errors = [];

    public function __construct(int $maxFileSize = 5242880, array $allowedMimeTypes = [])
    {
        $this->maxFileSize = $maxFileSize;
        $this->allowedMimeTypes = empty($allowedMimeTypes)
            ? ['image/jpeg', 'image/png', 'image/jpg', 'image/webp']
            : $allowedMimeTypes;
    }

    /**
     * Validate an uploaded file
     */
    public function validate(UploadedFile $file): bool
    {
        $this->errors = [];

        // Check if file was uploaded successfully
        if ($file->getError() !== UPLOAD_ERR_OK) {
            $this->errors[] = $this->getUploadErrorMessage($file->getError());
            return false;
        }

        // Check if it's a valid uploaded file
        if (!$file->isValid()) {
            $this->errors[] = 'Invalid file upload';
            return false;
        }

        // Check file size
        if ($file->getSize() > $this->maxFileSize) {
            $this->errors[] = sprintf(
                'File size exceeds maximum allowed size of %s MB',
                round($this->maxFileSize / 1024 / 1024, 2)
            );
            return false;
        }

        // Check MIME type
        if (!in_array($file->getMimeType(), $this->allowedMimeTypes)) {
            $this->errors[] = sprintf(
                'File type %s is not allowed. Allowed types: %s',
                $file->getMimeType(),
                implode(', ', $this->allowedMimeTypes)
            );
            return false;
        }

        // Verify it's actually an image
        $imageInfo = @getimagesize($file->getTempPath());
        if ($imageInfo === false) {
            $this->errors[] = 'File is not a valid image';
            return false;
        }

        // Check if file size is reasonable (not 0 or too small)
        if ($file->getSize() < 100) {
            $this->errors[] = 'File is too small or corrupted';
            return false;
        }

        return true;
    }

    /**
     * Get validation errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get upload error message
     */
    private function getUploadErrorMessage(int $errorCode): string
    {
        return match($errorCode) {
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize directive',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE directive',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'File upload stopped by extension',
            default => 'Unknown upload error'
        };
    }
}

