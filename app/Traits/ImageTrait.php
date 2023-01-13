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

                $data = $image;
                list($type, $data) = explode(';', $data);
                list(, $data) = explode(',', $data);
                $data = base64_decode($data);

                Storage::disk('public')->put("/images/" . $fileName, $data);
            }
            return $savedImages;
        }

        $randomName = $this->generateRandomName();
        $fileName = $randomName . ".jpeg";

        $data = $images;
        list($type, $data) = explode(';', $data);
        list(, $data) = explode(',', $data);
        $data = base64_decode($data);
        Storage::disk('public')->put("/images/" . $fileName, $data);
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
                $ftype = pathinfo($path . $image, PATHINFO_EXTENSION);
                $data = file_get_contents($path . $image);

                $base64 = 'data:image/' . $ftype . ';base64,' . base64_encode($data);

                $retrievedImages[] .= $base64;
            }
            return $retrievedImages;
        }

        $ftype = pathinfo($path . $images, PATHINFO_EXTENSION);
        $data = file_get_contents($path . $images);

        $base64 = 'data:image/' . $ftype . ';base64,' . base64_encode($data);
        return $base64;
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
                if(file_exists($path . $media)) {
                    unlink($path . $media);
                }
            }
            return;
        }

        if(file_exists($path . $media)) {
            unlink($path . $media);
        }
    }

    /**
     * Checks if file exists
     * @param mixed $path
     * @return bool
     */
    private function isPathExists($path) {
        return file_exists($path);
    }
}