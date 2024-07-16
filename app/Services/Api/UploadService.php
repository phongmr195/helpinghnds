<?php

namespace App\Services\Api;

use App\Traits\ImageUpload;

class UploadService
{
    use ImageUpload;

    /**
     * Upload image
     *
     * @param $image
     *
     * @return string
     */
    public function uploadImage($image)
    {
        return isset($image) ? $this->saveImage($image, 'images/', '') : '';
    }

    /**
     * Upload multi image
     * @param array $images
     *
     * @return array
     */
    public function uploadMultiImage(array $images)
    {
        return isset($images) ? $this->saveImages($images, 'images/', '') : [];
    }
}