<?php

namespace App\Service;


use App\Interfaces\UploadAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * Class FileUploader
 *
 * @package       App\Service
 */
class FileUploader implements UploadAwareInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var
     */
    private $uploadDir;

    /**
     * FileUploader constructor.
     *
     * @param LoggerInterface $logger
     * @param string          $uploadDir
     */
    public function __construct(LoggerInterface $logger, string $uploadDir)
    {
        $this->logger    = $logger;
        $this->uploadDir = $uploadDir;
    }

    /**
     * @param UploadedFile $file
     *
     * @return mixed|string
     */
    public function upload(UploadedFile $file)
    {
        try {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename      = $originalFilename.'-'.uniqid().'.'.$file->guessExtension();
            $file->move($this->uploadDir, $newFilename);
        } catch (FileException $ex) {
            $this->logger->error('Failed to upload file: '.$ex->getMessage());
            throw new FileException('Failed to upload file');
        }

        return $newFilename;
    }

    /**
     * @return mixed
     */
    public function getUploadDir()
    {
        return $this->uploadDir;
    }
}
