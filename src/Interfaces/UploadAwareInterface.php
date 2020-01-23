<?php

namespace App\Interfaces;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Interface UploadableInterface
 * @package       App\Interfaces
 */
interface UploadAwareInterface
{
    /**
     * @param UploadedFile $file
     *
     * @return mixed
     */
    public function upload(UploadedFile $file);

}
