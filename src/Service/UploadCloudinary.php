<?php


namespace App\Service;

use Cloudinary\Uploader;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class UploadCloudinary
 *
 * @author Julie Trannois trannois_julie@yahoo.fr
 */
class UploadCloudinary
{
    public function handleImageCloudinary($form, $entity)
    {
        if ($form->get('imageFile')->getData()) {
            $entity->setImageUrl($this->uploadToCloundinary($form));
        }
    }

    public function uploadToCloundinary($form)
    {
        /** @var UploadedFile $file */
        $file = $form->get('imageFile')->getData();
        $fileName = 'uploads/' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        \Cloudinary::config([
            "cloud_name" => $_ENV['CLOUD_NAME'],
            "api_key" => $_ENV['API_KEY'],
            "api_secret" => $_ENV['API_SECRET']
        ]);
        $response = Uploader::upload($file, ['public_id' => $fileName]);
        return $response['secure_url'];
    }

}