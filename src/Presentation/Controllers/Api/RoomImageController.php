<?php

namespace App\Presentation\Controllers\Api;

use App\Application\Interfaces\RoomImageServiceInterface;
use App\Domain\Exceptions\ImageUploadException;
use App\Domain\ValueObjects\UploadedFile;

class RoomImageController extends BaseRestController
{
    private RoomImageServiceInterface $roomImageService;

    public function __construct(RoomImageServiceInterface $roomImageService)
    {
        parent::__construct();
        $this->roomImageService = $roomImageService;
    }

    /**
     * Upload multiple images for a room
     * POST /api/rooms/{id}/images
     */
    public function upload($id = null): void
    {
        header('Content-Type: application/json');

        try {
            // Get room ID from URL parameter
            $roomId = (int)$id;

            if ($roomId <= 0) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid room ID',
                ]);
                return;
            }

            // Check if files were uploaded
            if (empty($_FILES)) {
                error_log('No FILES received');
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'No images provided',
                ]);
                return;
            }

            // Check for 'images' key (from images[])
            if (!isset($_FILES['images'])) {
                error_log('FILES data: ' . print_r($_FILES, true));
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'No images provided',
                    'debug' => [
                        'files_keys' => array_keys($_FILES),
                    ]
                ]);
                return;
            }

            // Get storage type (optional)
            $storageType = $_POST['storage_type'] ?? null;

            // Convert uploaded files to UploadedFile objects
            $files = [];
            $uploadedFiles = $_FILES['images'];

            // Handle multiple file upload
            if (isset($uploadedFiles['name']) && is_array($uploadedFiles['name'])) {
                for ($i = 0; $i < count($uploadedFiles['name']); $i++) {
                    if ($uploadedFiles['error'][$i] === UPLOAD_ERR_OK) {
                        $files[] = UploadedFile::fromArray([
                            'name' => $uploadedFiles['name'][$i],
                            'tmp_name' => $uploadedFiles['tmp_name'][$i],
                            'size' => $uploadedFiles['size'][$i] ?? 0,
                            'type' => $uploadedFiles['type'][$i] ?? 'application/octet-stream',
                            'error' => $uploadedFiles['error'][$i],
                        ]);
                    }
                }
            } else {
                // Single file upload
                if (isset($uploadedFiles['error']) && $uploadedFiles['error'] === UPLOAD_ERR_OK) {
                    $files[] = UploadedFile::fromArray($uploadedFiles);
                }
            }

            if (empty($files)) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'No valid images to upload',
                ]);
                return;
            }

            // Upload images via service
            $result = $this->roomImageService->uploadImages($roomId, $files, $storageType);

            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'Images uploaded successfully',
                'data' => $result,
            ]);
        } catch (ImageUploadException $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->getErrors(),
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Internal server error',
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Set primary image
     * PUT /api/rooms/{roomId}/images/{imageId}/primary
     */
    public function setPrimary($roomId = null, $imageId = null): void
    {
        header('Content-Type: application/json');

        try {
            // Get IDs from URL parameters
            $roomId = (int)$roomId;
            $imageId = (int)$imageId;

            if ($imageId <= 0 || $roomId <= 0) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid image ID or room ID',
                ]);
                return;
            }

            $result = $this->roomImageService->setPrimaryImage($imageId, $roomId);

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Primary image set successfully',
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Image not found or does not belong to this room',
                ]);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Internal server error',
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update display order
     * PUT /api/rooms/images/order
     */
    public function updateOrder(): void
    {
        header('Content-Type: application/json');

        try {
            $input = json_decode(file_get_contents('php://input'), true);

            $orders = $input['orders'] ?? [];

            if (empty($orders)) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'No order data provided',
                ]);
                return;
            }

            $result = $this->roomImageService->updateDisplayOrder($orders);

            echo json_encode([
                'success' => $result,
                'message' => $result ? 'Display order updated successfully' : 'Failed to update display order',
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Internal server error',
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Delete image
     * DELETE /api/rooms/images/{id}
     */
    public function delete($id = null): void
    {
        header('Content-Type: application/json');

        try {
            // Get image ID from URL parameter
            $imageId = (int)$id;

            if ($imageId <= 0) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid image ID',
                ]);
                return;
            }

            $result = $this->roomImageService->deleteImage($imageId);

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Image deleted successfully',
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Image not found',
                ]);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Internal server error',
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Health check
     * GET /api/storage/health
     */
    public function healthCheck(): void
    {
        header('Content-Type: application/json');

        try {
            $storageType = $_GET['storage_type'] ?? null;

            $result = $this->roomImageService->getStorageHealth($storageType);

            echo json_encode([
                'success' => true,
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Internal server error',
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get storage info
     * GET /api/storage/info
     */
    public function storageInfo(): void
    {
        header('Content-Type: application/json');

        try {
            $storageType = $_GET['storage_type'] ?? null;

            $result = $this->roomImageService->getStorageInfo($storageType);

            echo json_encode([
                'success' => true,
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Internal server error',
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Switch storage provider
     * PUT /api/storage/provider
     */
    public function switchProvider(): void
    {
        header('Content-Type: application/json');

        try {
            $input = json_decode(file_get_contents('php://input'), true);

            $provider = $input['provider'] ?? '';

            if (empty($provider)) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Provider not specified',
                ]);
                return;
            }

            $result = $this->roomImageService->switchStorageProvider($provider);

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => "Storage provider switched to {$provider}",
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid storage provider',
                ]);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Internal server error',
                'error' => $e->getMessage(),
            ]);
        }
    }
}
