<?php
class LostAndFoundImage extends Model
{
    protected $table = 'lostandfound_images';

    /**
     * Add an image for a lost or found item.
     * @param string $itemType 'lost' or 'found'
     * @param int $itemId
     * @param string $imagePath
     * @param string $imageName
     * @param bool $isMain
     * @param int|null $fileSize
     * @param string|null $mimeType
     * @return int|false Image ID on success, false on failure
     */
    public function addImage(
        string $itemType,
        int $itemId,
        string $imagePath,
        string $imageName,
        bool $isMain = false,
        ?int $fileSize = null,
        ?string $mimeType = null
    ) {
        try {
            Logger::info("Adding image for {$itemType} item id={$itemId}");

            // If marking as main, unmark all existing main images for this item first
            if ($isMain) {
                $unmark = $this->db->prepare(
                    "UPDATE lostandfound_images SET is_main = 0 WHERE item_type = ? AND item_id = ?"
                );
                $unmark->execute([$itemType, $itemId]);
            }

            $sql = "INSERT INTO lostandfound_images
                        (item_type, item_id, image_path, image_name, is_main, file_size, mime_type)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $ok = $stmt->execute([
                $itemType,
                $itemId,
                $imagePath,
                $imageName,
                $isMain ? 1 : 0,
                $fileSize,
                $mimeType
            ]);

            if (!$ok) {
                Logger::error("Failed to add image: " . implode(' | ', $stmt->errorInfo()));
                return false;
            }

            $id = (int)$this->db->lastInsertId();
            Logger::info("Image added successfully with id={$id}");
            return $id;
        } catch (Throwable $e) {
            Logger::error("Error adding image: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all images for an item.
     * @param string $itemType
     * @param int $itemId
     * @return array
     */
    public function getImages(string $itemType, int $itemId): array
    {
        try {
            Logger::info("getImages called: type={$itemType}, id={$itemId}");
            
            // Use direct SQL query instead of stored procedure to avoid PDO issues
            $sql = "SELECT id, image_path, image_name, is_main, file_size, mime_type, uploaded_at
                    FROM lostandfound_images
                    WHERE item_type = ? AND item_id = ?
                    ORDER BY is_main DESC, uploaded_at ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$itemType, $itemId]);
            $images = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            
            Logger::info("getImages returned " . count($images) . " images for {$itemType} item {$itemId}: " . json_encode($images));
            return $images;
        } catch (Throwable $e) {
            Logger::error('getImages error: ' . $e->getMessage());
            Logger::error('Error trace: ' . $e->getTraceAsString());
            return [];
        }
    }

    /**
     * Delete an image.
     * @param int $imageId
     * @param string $itemType
     * @param int $itemId
     * @return bool
     */
    public function deleteImage(int $imageId, string $itemType, int $itemId): bool
    {
        try {
            $sql = "CALL sp_delete_item_image(?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $ok = $stmt->execute([$imageId, $itemType, $itemId]);

            if (!$ok) {
                Logger::error("Failed to delete image");
                return false;
            }

            return $stmt->rowCount() > 0;
        } catch (Throwable $e) {
            Logger::error('deleteImage error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Upload and save an image file.
     * @param array $file $_FILES array element
     * @param string $itemType
     * @param int $itemId
     * @param bool $isMain
     * @return array|false Returns array with path info on success, false on failure
     */
    public function uploadImage(array $file, string $itemType, int $itemId, bool $isMain = false)
    {
        try {
            // Validate file
            if ($file['error'] !== UPLOAD_ERR_OK) {
                Logger::error("File upload error: " . $file['error']);
                return false;
            }

            // Check file size (5MB max)
            $maxSize = 5 * 1024 * 1024; // 5MB
            if ($file['size'] > $maxSize) {
                Logger::error("File too large: " . $file['size']);
                return false;
            }

            // Check mime type
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mimeType, $allowedTypes)) {
                Logger::error("Invalid file type: " . $mimeType);
                return false;
            }

            // Create directory if it doesn't exist
            $uploadDir = __DIR__ . '/../../public/storage/lostandfound/' . $itemType . '/' . $itemId;
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '_' . time() . '.' . $extension;
            $filepath = $uploadDir . '/' . $filename;

            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                Logger::error("Failed to move uploaded file");
                return false;
            }

            // Return path relative to public directory
            $relativePath = '/storage/lostandfound/' . $itemType . '/' . $itemId . '/' . $filename;

            return [
                'path' => $relativePath,
                'filename' => $filename,
                'size' => $file['size'],
                'mime_type' => $mimeType
            ];
        } catch (Throwable $e) {
            Logger::error("Error uploading image: " . $e->getMessage());
            return false;
        }
    }
}
