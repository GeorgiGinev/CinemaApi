<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

trait ImageTrait
{

    /**
     * Save Image to Storage
     * @param $images
     * @return array|string
     */
    public function verifyAndUpload($images)
    {
        if (is_array($images)) {
            $savedImages = [];
            //save images
            foreach ($images as $image) {
                $randomName = $this->generateRandomName();
                $fileName = $randomName . ".jpeg";
                $savedImages[] .= $fileName;
                $image = base64_decode($image);
                Storage::disk('public')->put("/images/" . $fileName, $image);
            }
            return $savedImages;
        }

        $randomName = $this->generateRandomName();
        $fileName = $randomName . ".jpeg";

        $image = base64_decode($images);
        Storage::disk('public')->put("/images/" . $fileName, $image);
        return $fileName;
    }

    /**
     * Generate Random name
     * @return string
     */
    public function generateRandomName(): string
    {
        return md5(rand());
    }

    /**
     * Retrive images
     * @param $logo
     * @return
     */
    public function retriveImages($images)
    {
        $path = Storage::path('public/images/');

        if (is_array($images)) {
            $retrievedImages = [];
            foreach ($images as $image) {
                $retrievedImages[] .= base64_encode(file_get_contents($path. $image));
            }
            return $retrievedImages;
        }
        return base64_encode(file_get_contents($path . $images));
    }

    /**
     * Delete images
     * @param $media
     * @return
     */
    public function deleteImages($media): void
    {
        $path = Storage::path('public/images/');
        if (is_array($media)) {
            foreach ($media as $image) {
                unlink($path . $image);
            }
            return;
        }
        unlink($path . $media);
    }
}
